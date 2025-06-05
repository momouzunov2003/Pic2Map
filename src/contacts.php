<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $message) {
        $to      = 'webtestmail@mail.bg';
        $subject = "Contact Form Submission from $name";
        $body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers =
            "From: hello@mailersend.com" .
            "\r\n" .
            "Reply-To: reply@mailersend.com" .
            "\r\n" .
            mail($to, $subject, $message, $headers);

        if (mail($to, $subject, $body, $headers)) {
            header('Location: /pic2map/contacts.php?sent=1');
            exit;
        } else {
            header('Location: /pic2map/contacts.php?sent=2');
            exit;
        }
    } else {
        header('Location: /pic2map/contacts.php?sent=3  ');
        exit;
    }
}

header('Location: /pic2map/contacts.php');
exit;
?>