<?php
/**
 * Client SMTP léger et envoi d'emails via le serveur WEDOS.
 *
 * Ce fichier ne dépend d'Aucune bibliothèque externe (pas de Composer) : il
 * implémente le protocole SMTP à partir de fsockopen / OpenSSL, ce qui
 * fonctionne sur le mutualisé WEDOS.
 *
 * Deux fonctions publiques :
 *  - fm_load_smtp_config() : charge les identifiants depuis smtp.secret.php
 *    (et smtp.local.php en local), exactement comme connection.php le fait
 *    pour la base de données.
 *  - fm_send_mail($to, $toName, $subject, $htmlBody, $textBody) : envoie un
 *    mail en SMTP avec STARTTLS (ou SSL sur le port 465).
 */

/**
 * Charge la configuration SMTP depuis le fichier secret.
 *
 * Le fichier smtp.secret.php est ignoré par .gitignore. En local (localhost /
 * 127.0.0.1), on peut éventuellement le surcharger via smtp.local.php.
 *
 * @return array|null  Les clés host, port, username, password, from ; ou null
 *                     si la configuration est absente (l'envoi est alors un
 *                     non-événement silencieux).
 */
function fm_load_smtp_config()
{
    $fm_config = array(
        'host'     => '',
        'port'     => 587,
        'username' => '',
        'password' => '',
        'from'     => '',
    );

    $fm_secret = __DIR__ . '/smtp.secret.php';
    if (is_file($fm_secret)) {
        $fm_config = array_merge($fm_config, (array) require $fm_secret);
    }

    $fm_local = __DIR__ . '/smtp.local.php';
    if (is_file($fm_local)) {
        $fm_hote = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
        $fm_est_local = ($fm_hote !== '')
            && (strpos($fm_hote, 'localhost') === 0 || strpos($fm_hote, '127.0.0.1') === 0);

        if ($fm_est_local) {
            $fm_config = array_merge($fm_config, (array) require $fm_local);
        }
    }

    if ($fm_config['host'] === '' || $fm_config['username'] === '') {
        return null;
    }

    return $fm_config;
}

/**
 * Envoie un email via SMTP avec STARTTLS ou SSL.
 *
 * @param string      $to       Adresse email du destinataire.
 * @param string      $toName   Nom du destinataire.
 * @param string      $subject  Sujet.
 * @param string      $htmlBody Contenu HTML du corps.
 * @param string|null $textBody Contenu texte brut (facultatif).
 *
 * @return bool  true si le mail a été accepté par le serveur, false sinon.
 */
function fm_send_mail($to, $toName, $subject, $htmlBody, $textBody = null)
{
    $cfg = fm_load_smtp_config();
    if ($cfg === null) {
        error_log('fm_send_mail : configuration SMTP manquante, envoi annule.');
        return false;
    }

    $host     = $cfg['host'];
    $port     = $cfg['port'];
    $username = $cfg['username'];
    $password = $cfg['password'];
    $from     = $cfg['from'];

    if ($from === '') {
        $from = $username;
    }

    /* --- Connexion TCP / TLS --- */
    /* Port 465 = TLS implicite (connexion déjà chiffrée).
       Port 587 = STARTTLS (connexion claire, puis on monte en chiffrement). */
    $isTLS  = ($port == 465);
    $prefix = $isTLS ? 'ssl' : 'tcp';

    $errno = 0;
    $errstr = '';
    $socket = @fsockopen($prefix . '://' . $host, $port, $errno, $errstr, 30);
    if (!$socket) {
        error_log('fm_send_mail : connexion echouee vers ' . $host . ':' . $port . ' (' . $errstr . ')');
        return false;
    }

    stream_set_timeout($socket, 30);

    /* Lecture de la bannière de connexion (220). */
    $banner = fm_smtp_read_response($socket);
    if ($banner === false || strpos($banner, '220') !== 0) {
        fclose($socket);
        error_log('fm_send_mail : banniere invalide (attendu 220, recu : ' . ($banner ?: 'rien') . ')');
        return false;
    }

    /* --- EHLO initial --- */
    $ehlo = fm_smtp_send_cmd($socket, 'EHLO foodmax-group.com', '250');
    if ($ehlo === false) {
        fclose($socket);
        error_log('fm_send_mail : EHLO a echoue.');
        return false;
    }

    /* --- Upgrade en TLS si le serveur le propose (STARTTLS, port 587) --- */
    if (!$isTLS && strpos($ehlo, 'STARTTLS') !== false) {
        $starttls = fm_smtp_send_cmd($socket, 'STARTTLS', '220');
        if ($starttls === false) {
            fclose($socket);
            error_log('fm_send_mail : STARTTLS refuse.');
            return false;
        }

        $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if (!$crypto) {
            fclose($socket);
            error_log('fm_send_mail : echec du handshake TLS.');
            return false;
        }

        /* Re-EHLO sur la connexion chiffrée. */
        $ehlo2 = fm_smtp_send_cmd($socket, 'EHLO foodmax-group.com', '250');
        if ($ehlo2 === false) {
            fclose($socket);
            error_log('fm_send_mail : EHLO post-TLS a echoue.');
            return false;
        }
    }

    /* --- Authentification AUTH LOGIN --- */
    $auth = fm_smtp_send_cmd($socket, 'AUTH LOGIN', '334');
    if ($auth === false) {
        fclose($socket);
        error_log('fm_send_mail : AUTH LOGIN a echoue.');
        return false;
    }

    /* 334 = challenge, on envoie le username en base64. */
    $user = fm_smtp_send_cmd($socket, base64_encode($username), '334');
    if ($user === false) {
        fclose($socket);
        error_log('fm_send_mail : authentification utilisateur echouee.');
        return false;
    }

    /* On envoie le password en base64 ; 235 = auth réussie. */
    $pass = fm_smtp_send_cmd($socket, base64_encode($password), '235');
    if ($pass === false) {
        fclose($socket);
        error_log('fm_send_mail : authentification mot de passe echouee.');
        return false;
    }

    /* --- Envoi du message --- */
    $boundary = '----_Part_' . md5(uniqid((string) mt_rand(), true)) . '----';

    $fromEncoded = fm_encode_mailbox($from);

    $headers = array();
    $headers[] = 'Date: ' . date('r');
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'From: ' . fm_format_address($from, 'Foodmax Group');
    $headers[] = 'To: ' . fm_format_address($to, $toName);
    $headers[] = 'Subject: ' . fm_header_encode($subject);
    $headers[] = 'Message-ID: <' . uniqid() . '@' . $host . '>';
    $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
    $headers[] = 'Return-Path: ' . $from;

    $headerStr = implode("\r\n", $headers);

    if ($textBody === null) {
        $textBody = fm_strip_tags_simple($htmlBody);
    }

    $body = $headerStr . "\r\n\r\n"
        . '--' . $boundary . "\r\n"
        . 'Content-Type: text/plain; charset=UTF-8' . "\r\n"
        . 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n"
        . $textBody . "\r\n\r\n"
        . '--' . $boundary . "\r\n"
        . 'Content-Type: text/html; charset=UTF-8' . "\r\n"
        . 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n"
        . $htmlBody . "\r\n\r\n"
        . '--' . $boundary . "--\r\n";

    /* MAIL FROM / RCPT TO (les adresses sont déjà entourées de <>). */
    $mailFrom = fm_smtp_send_cmd($socket, 'MAIL FROM:' . $fromEncoded, '250');
    if ($mailFrom === false) {
        fclose($socket);
        error_log('fm_send_mail : MAIL FROM a echoue.');
        return false;
    }

    $rcptTo = fm_smtp_send_cmd($socket, 'RCPT TO:' . fm_encode_mailbox($to), '250');
    if ($rcptTo === false) {
        fclose($socket);
        error_log('fm_send_mail : RCPT TO a echoue (dest : ' . $to . ').');
        return false;
    }

    /* DATA : on déclenche l'envoi du corps. 354 = serveur prêt. */
    $data = fm_smtp_send_cmd($socket, 'DATA', '354');
    if ($data === false) {
        fclose($socket);
        error_log('fm_send_mail : DATA a echoue.');
        return false;
    }

    /* On envoie les données ligne par ligne. Une ligne commençant par "."
       est doublée ("." -> "..") selon la RFC 5321 §4.5.2. */
    $lines = explode("\r\n", $body);
    foreach ($lines as $line) {
        if (substr($line, 0, 1) === '.') {
            $line = '.' . $line;
        }
        fwrite($socket, $line . "\r\n");
    }
    fwrite($socket, ".\r\n");

    /* Réponse finale : 250 si le message a été accepté. */
    $result = fm_smtp_send_cmd($socket, '', null);
    if ($result === false || strpos($result, '250') !== 0) {
        @fm_smtp_send_cmd($socket, 'QUIT', '221');
        fclose($socket);
        error_log('fm_send_mail : server reply after DATA (attendu 250, recu : ' . ($result ?: 'rien') . ')');
        return false;
    }

    /* Quitte proprement. */
    @fm_smtp_send_cmd($socket, 'QUIT', '221');
    fclose($socket);

    return true;
}

/* ------------------------------------------------------------------ *
 * Fonctions utilitaires SMTP
 * ------------------------------------------------------------------ */

/**
 * Envoie une commande SMTP et lit la réponse complète du serveur.
 *
 * @param resource $socket
 * @param string   $cmd    Commande à envoyer (sans CRLF final). Envoi une
 *                         chaîne vide après un DATA pour lire la réponse
 *                         finale.
 * @param string|null $expect Code de réponse attendu (ex: '250', '334').
 *                            null = ne vérifie pas le code.
 * @return string|false  La réponse complète du serveur (toutes les lignes),
 *                        ou false en cas d'erreur de lecture.
 */
function fm_smtp_send_cmd($socket, $cmd, $expect = null)
{
    if ($cmd !== '') {
        fwrite($socket, $cmd . "\r\n");
    }

    $response = fm_smtp_read_response($socket);

    if ($response === false) {
        return false;
    }

    if ($expect !== null) {
        /* Le code de réponse est les 3 premiers caractères. */
        $code = substr($response, 0, 3);
        if (strpos($code, $expect) !== 0) {
            error_log('fm_smtp_send_cmd : "' . $cmd . '" -> reponse inattendue (' . $expect . ' attendu, ' . $code . ' recu) : ' . trim($response));
            return false;
        }
    }

    return $response;
}

/**
 * Lit toutes les lignes de réponse SMTP jusqu'à la ligne finale.
 *
 * Une réponse SMTP se termine quand une ligne commence par
 * "XYZ " (code suivi d'un espace). Les lignes intermédiaires
 * commencent par "XYZ-" (code suivi d'un tiret).
 *
 * @param resource $socket
 * @return string|false  La réponse complète (toutes les lignes jointes),
 *                        ou false en cas d'erreur / timeout.
 */
function fm_smtp_read_response($socket)
{
    $response = '';
    $start = microtime(true);

    while (true) {
        $line = @fgets($socket, 515);
        if ($line === false) {
            return false;
        }

        $trimmed = rtrim($line, "\r\n");
        $response .= $trimmed . "\n";

        /* Vérifie si c'est la dernière ligne (code + espace). */
        if (preg_match('/^\d{3}( |$)/', $trimmed)) {
            return trim($response);
        }

        if ((microtime(true) - $start) > 30) {
            return false;
        }
    }
}

/**
 * Encode l'adresse de boîte aux lettres pour le protocole SMTP (RFC 5321).
 *
 * @param string $email
 * @return string
 */
function fm_encode_mailbox($email)
{
    return '<' . $email . '>';
}

/**
 * Formate une adresse email avec un nom (RFC 5322).
 *
 * @param string $email
 * @param string $name
 * @return string
 */
function fm_format_address($email, $name)
{
    if ($name === '' || $name === null) {
        return '<' . $email . '>';
    }

    /* Encodage RFC 2047 pour les caractères spéciaux. */
    $name = trim($name);
    $needsEncoding = false;
    for ($i = 0; $i < strlen($name); $i++) {
        $c = ord($name[$i]);
        if ($c > 127 || $name[$i] === '"' || $name[$i] === '\\' || $name[$i] === "\n" || $name[$i] === "\r") {
            $needsEncoding = true;
            break;
        }
    }

    if ($needsEncoding) {
        $name = '=?UTF-8?B?' . base64_encode($name) . '?=';
    }

    return $name . ' <' . $email . '>';
}

/**
 * Encodage RFC 2047 (Base64) pour le sujet.
 *
 * @param string $str
 * @return string
 */
function fm_header_encode($str)
{
    return '=?UTF-8?B?' . base64_encode($str) . '?=';
}

/**
 * Convertit du HTML simple en texte brut (pour le corps texte du mail).
 *
 * @param string $html
 * @return string
 */
function fm_strip_tags_simple($html)
{
    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}
