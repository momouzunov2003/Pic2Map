<?php
require_once __DIR__ . '/../config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $to      = ADMIN_EMAILS;
        $subject = "Contact Form Submission from $name";
        $body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = 'From: pic2map@pic2map.com' . "\r\n" .
                'Reply-To: ' . ADMIN_EMAILS . "\r\n" .
                'X-Mailer: PHP/' . phpversion();


        if (mail($to, $subject, $body, $headers)) {
            header('Location: ' . APP_ROOT . '/pic2map/contacts.php?sent=1');
            exit;
        } else {
            header('Location: ' . APP_ROOT . '/pic2map/contacts.php?sent=2');
            exit;
        }
    } else {
        header('Location: ' . APP_ROOT . '/pic2map/contacts.php?sent=3');
        exit;
    }
}

header('Location: /pic2map/contacts.php');
exit;
?>