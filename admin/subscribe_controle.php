<?php
require_once((__DIR__ . "/../includes/connection.php"));

/* Inscription a la newsletter.
   L'ancien code concateneait $email directement dans l'INSERT (injection SQL),
   renvoyait l'adresse saisie dans la page (XSS refl ech e) et appelait mail(),
   qui echoue bruyamment en local. */

header('Content-Type: text/html; charset=utf-8');

function fm_newsletter_alert($type, $icon, $texte)
{
    return '<div class="fm-form-alert fm-form-alert-' . $type . '" id="message">'
         . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
         . '<i class="fa fa-' . $icon . '" aria-hidden="true"></i>'
         . '<span>' . $texte . '</span>'
         . '</div>';
}

$email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';

if ($email === '') {
    exit(fm_newsletter_alert('warn', 'exclamation-circle', 'Please enter your e-mail address.'));
}

if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(fm_newsletter_alert('warn', 'exclamation-circle', 'This e-mail address does not look valid.'));
}

$date = (isset($_POST['date']) && $_POST['date'] !== '')
      ? substr(trim((string) $_POST['date']), 0, 50)
      : date('j-n-Y H:i:s');

/* La table n'a pas d'index unique : on verifie donc l'absence en base
   avant d'inserer, pour ne pas accumuler les doublons. */
$stmt = mysqli_prepare($conn, "SELECT id_sub FROM subscribe WHERE email_sub = ? LIMIT 1");
if (!$stmt) {
    exit(fm_newsletter_alert('error', 'exclamation-circle',
        'Sorry, your subscription could not be saved. Please try again later.'));
}
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$deja = ($res && mysqli_fetch_assoc($res)) ? true : false;
mysqli_stmt_close($stmt);

if ($deja) {
    exit(fm_newsletter_alert('warn', 'envelope-o',
        'This e-mail address is already subscribed to our newsletter.'));
}

$stmt = mysqli_prepare($conn, "INSERT INTO subscribe (email_sub, date_sub) VALUES (?, ?)");
if (!$stmt) {
    exit(fm_newsletter_alert('error', 'exclamation-circle',
        'Sorry, your subscription could not be saved. Please try again later.'));
}
mysqli_stmt_bind_param($stmt, 'ss', $email, $date);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    exit(fm_newsletter_alert('ok', 'check',
        'Thank you! Your e-mail address has been registered.'));
}

error_log('subscribe_controle.php : ' . mysqli_stmt_error($stmt));
mysqli_stmt_close($stmt);
exit(fm_newsletter_alert('error', 'exclamation-circle',
    'Sorry, your subscription could not be saved. Please try again later.'));
