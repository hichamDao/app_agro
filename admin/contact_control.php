<?php
require_once(__DIR__ . "/../includes/connection.php");
require_once(__DIR__ . "/../includes/smtp_mailer.php");

/* Reponse du formulaire de contact.
   Aucune valeur recue n'est concatenee dans la requete : tout passe par une
   requete preparee, donc l'escapement depend de mysqli et non du code. */

header('Content-Type: text/html; charset=utf-8');

if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
    exit('<div class="fm-form-alert fm-form-alert-error" id="message">'
        . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
        . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
        . '<span>Please fill in your name, your e-mail and your message.</span>'
        . '</div>');
}

$name    = substr(trim((string) $_POST['name']), 0, 120);
$email   = substr(trim((string) $_POST['email']), 0, 250);
$phone   = substr(trim((string) (isset($_POST['phone']) ? $_POST['phone'] : '')), 0, 50);
$company = substr(trim((string) (isset($_POST['company']) ? $_POST['company'] : '')), 0, 150);
$country = substr(trim((string) (isset($_POST['country']) ? $_POST['country'] : '')), 0, 120);
$product = substr(trim((string) (isset($_POST['product']) ? $_POST['product'] : '')), 0, 150);
$message = trim((string) $_POST['message']);
$date    = isset($_POST['date']) && $_POST['date'] !== ''
         ? substr(trim((string) $_POST['date']), 0, 24)
         : date('j-n-Y H:i:s');

/* Un message de moins de 20 caracteres ne permet pas de repondre utilement :
   on le refuse avant tout enregistrement (meme seuil que le formulaire). */
if (mb_strlen($message, 'UTF-8') < 20) {
    exit('<div class="fm-form-alert fm-form-alert-warn" id="message">'
        . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
        . '<i class="fa fa-frown-o" aria-hidden="true"></i>'
        . '<span>Thank you for writing, but your message is a little short for us to answer properly. Could you add a few more details, such as the product, the quantity and the destination?</span>'
        . '</div>');
}

$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO contact (name, email, phone, message, date, company, country, product)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    exit('<div class="fm-form-alert fm-form-alert-error" id="message">'
        . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
        . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
        . '<span>Sorry, the message could not be saved. Please try again later.</span>'
        . '</div>');
}

mysqli_stmt_bind_param($stmt, 'ssssssss', $name, $email, $phone, $message, $date, $company, $country, $product);

if (mysqli_stmt_execute($stmt)) {
    /* Envoi de l'email de confirmation (auto-reponse) au visiteur.
       Si l'envoi echoue, le message reste sauvegarde en base : l'erreur est
       simplement journalisee, l'utilisateur n'est pas impacte. */
    $autoReplySubject = 'Your message has been received — Foodmax Group';
    $autoReplyHtml = fm_auto_reply_html($name);
    $autoReplyText = fm_auto_reply_text($name);
    $mailResult = fm_send_mail($email, $name, $autoReplySubject, $autoReplyHtml, $autoReplyText);
    if ($mailResult === false) {
        error_log('contact_control.php : auto-reponse non envoyee a ' . $email);
    }

    mysqli_stmt_close($stmt);
    exit('<div class="fm-form-alert fm-form-alert-ok" id="message">'
        . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
        . '<i class="fa fa-check" aria-hidden="true"></i>'
        . '<span>Thank you, your message has reached us. A member of our team will reply within one business day.</span>'
        . '</div>');
}

    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    error_log('contact_control.php : ' . $err);
    exit('<div class="fm-form-alert fm-form-alert-error" id="message">'
        . '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
        . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
        . '<span>Sorry, the message could not be saved. Please try again later.</span>'
        . '</div>');

/* ------------------------------------------------------------------ *
 * Corps de l'email de confirmation (auto-reponse) envoye au visiteur
 * ------------------------------------------------------------------ */

/**
 * Generation du corps HTML de l'auto-reponse.
 *
 * @param string $name Nom du visiteur.
 * @return string
 */
function fm_auto_reply_html($name)
{
    $safeName = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return '<!DOCTYPE html>' . "\n"
        . '<html lang="en">' . "\n"
        . '<head><meta charset="utf-8"><title>Message received</title></head>' . "\n"
        . '<body style="margin:0;padding:0;background:#f5f5f5;font-family:\'Open Sans\',Arial,sans-serif;">' . "\n"
        . '<table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;">' . "\n"
        . '<tr><td style="padding:30px 40px;">' . "\n"
        . '<h1 style="color:#2a7d2a;margin:0 0 20px;font-size:24px;">Thank you, ' . $safeName . '.</h1>' . "\n"
        . '<p style="color:#333333;font-size:16px;line-height:1.6;margin:0 0 15px;">'
        . 'We have received your message and a member of our team will reply '
        . 'within one business day.</p>' . "\n"
        . '<p style="color:#333333;font-size:16px;line-height:1.6;margin:0 0 20px;">'
        . 'In the meantime, if you need anything urgent, you can write directly '
        . 'to <a href="mailto:info@foodmax-group.com" style="color:#2a7d2a;">info@foodmax-group.com</a>.</p>' . "\n"
        . '<hr style="border:none;border-top:1px solid #eeeeee;margin:20px 0;">' . "\n"
        . '<p style="color:#999999;font-size:13px;line-height:1.4;">'
        . 'Foodmax Group<br>'
        . '16, Rue AL Ikhae Apt 2<br>'
        . 'Zone industrielle — Marrakech, Morocco</p>' . "\n"
        . '</td></tr></table>' . "\n"
        . '</body></html>';
}

/**
 * Generation du corps texte brut de l'auto-reponse.
 *
 * @param string $name Nom du visiteur.
 * @return string
 */
function fm_auto_reply_text($name)
{
    return 'Thank you, ' . $name . '.' . "\n\n"
        . "We have received your message and a member of our team will reply\n"
        . "within one business day.\n\n"
        . "In the meantime, if you need anything urgent, you can write directly\n"
        . "to info@foodmax-group.com.\n\n"
        . "Foodmax Group\n"
        . "16, Rue AL Ikhae Apt 2\n"
        . "Zone industrielle — Marrakech, Morocco";
}
