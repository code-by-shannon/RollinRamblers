<?php

// Honeypot check
if (!empty($_POST['additional_notes_94xY'])) {
    $logFile = __DIR__ . '/honeypot.txt'; // Logs will be saved to honeypot.txt

    $data = [
        'date' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'field_value' => $_POST['additional_notes_94xY']
    ];

    $logLine = json_encode($data) . PHP_EOL;
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

    // Play it cool: send 200 response with no message
    http_response_code(200);
    exit();
}

// Sanitize inputs
$name = strip_tags(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = strip_tags(trim($_POST['message'] ?? ''));

// Validate required fields
if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("⚠️ Missing or invalid input.");
}

// Email setup
$to = "shannon@rollinramblers.com";
$subject = "Booking or Other Query";
$headers = "From: rollinRambler@rollinramblers.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "CC: sestumpf@gmail.com\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

$txt = "You have received a message from:\n\nName: $name\nEmail: $email\nMessage:\n$message";

// Send it!
mail($to, $subject, $txt, $headers);

// Redirect to thank-you page
header("Location: thankyou.html");
exit();
?>