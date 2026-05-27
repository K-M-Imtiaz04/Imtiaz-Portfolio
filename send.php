<?php
// Honeypot check
if (!empty($_POST['website'])) {
    exit("Bot detected.");
}

// Validate inputs
$name = htmlspecialchars(trim($_POST['name']));
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$message = htmlspecialchars(trim($_POST['message']));

if (!$email || strlen($message) < 5) {
    exit("Please fill in all fields correctly.");
}

// Google reCAPTCHA verification
$secretKey = "6Lf4FissAAAAANqHiLIDNEloWCABEM-jTOrZV6AW"; // Replace with your secret key
$recaptchaResponse = $_POST['g-recaptcha-response'];
$userIP = $_SERVER['REMOTE_ADDR'];

$verifyURL = "https://www.google.com/recaptcha/api/siteverify";
$data = [
    'secret' => $secretKey,
    'response' => $recaptchaResponse,
    'remoteip' => $userIP
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context  = stream_context_create($options);
$response = file_get_contents($verifyURL, false, $context);
$result = json_decode($response);

if (!$result->success) {
    exit("Captcha verification failed.");
}

// Send email safely
$to = "kaziimtiaz34@gmail.com"; // Replace with your email
$subject = "New Contact Message from $name";
$headers = "From: $email\r\nReply-To: $email";

$mailSent = mail($to, $subject, $message, $headers);

if ($mailSent) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message. Please try again.";
}
?>
