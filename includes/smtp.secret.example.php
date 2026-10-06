<?php
/**
 * MODELE des identifiants SMTP pour les envois d'emails.
 *
 * 1. Copier ce fichier sous le nom  smtp.secret.php  (dans includes/).
 * 2. Remplacer les valeurs ci-dessous par les vraies.
 * 3. Ne JAMAIS envoyer smtp.secret.php sur GitHub : il est dans .gitignore.
 *
 * Le serveur SMTP utilise est celui de WEDOS :
 *   wes1-smtp.wedos.net  (port 587 avec STARTTLS, ou 465 en SSL)
 *
 * Sur le serveur de production, ce fichier doit exister AVANT l'envoi du
 * premier mail, sinon l'auto-reponse echouera silencieusement.
 */
return array(
    'host'     => 'wes1-smtp.wedos.net',
    'port'     => 587,
    'username' => '',
    'password' => '',
    'from'     => '',
);
