<?php
// submit-form.php

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Simple sanitiser
function clean_input($value)
{
    return htmlspecialchars(trim(stripslashes($value)), ENT_QUOTES, 'UTF-8');
}

// Collect fields from form.html
$fullName = clean_input($_POST['fullName'] ?? '');
$email    = clean_input($_POST['email'] ?? '');
$phone    = clean_input($_POST['phone'] ?? '');
$location = clean_input($_POST['location'] ?? '');
$equipment = clean_input($_POST['equipment'] ?? '');
$message  = clean_input($_POST['message'] ?? '');

// Basic validation
$errors = [];

if (empty($fullName)) {
    $errors[] = 'Full Name is required.';
}

if (empty($phone)) {
    $errors[] = 'Contact Number is required.';
}

// Optional: basic email format validation (only if user entered one)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// If validation fails, you can redirect to a simple error page
if (!empty($errors)) {
    // You can also log/inspect $errors if needed
    header('Location: error.html');
    exit;
}

// Email settings
$to      = 'info@manliftchennai.com'; // Destination email
$subject = 'New Enquiry - BoomLift Rentals Chennai';

// Build email body
$body  = "You have received a new enquiry from the Chennai boom lift rentals form.\n\n";
$body .= "Full Name: {$fullName}\n";
$body .= "Contact Number: {$phone}\n";
$body .= "Email Address: " . ($email ?: 'Not provided') . "\n";
$body .= "Area / Location: " . ($location ?: 'Not provided') . "\n";
$body .= "Equipment Selected: " . ($equipment ?: 'Not specified') . "\n\n";
$body .= "Message / Requirement Details:\n";
$body .= ($message ?: 'No additional details provided.') . "\n";

// Headers
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// From / Reply-To
$from_email = !empty($email) ? $email : 'no-reply@manliftchennai.com';
$headers   .= "From: Manlift Chennai Enquiry <{$from_email}>\r\n";
if (!empty($email)) {
    $headers .= "Reply-To: {$email}\r\n";
}

// Send the email
$sent = mail($to, $subject, $body, $headers);

// Redirect based on result
if ($sent) {
    header('Location: thankyou.html');
} else {
    header('Location: error.html'); // Create this if you want a custom error page
}

exit;
