<?php
require_once((__DIR__ . "/../includes/connection.php"));

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
