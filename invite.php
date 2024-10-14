<?php
function generateToken($length = 50) {
    return bin2hex(random_bytes($length / 2));
}

function sendInvitation($email, $token) {
    $inviteLink = "http://yourdomain.com/register.php?token=$token";
    $subject = "You have been invited!";
    $message = "Please click the following link to join: $inviteLink";
    mail($email, $subject, $message);
}

// When adding team members to a project
$token = generateToken();
$inviteEmail = trim($team_member_email);

// Store the invite in the invites table
$sql = "INSERT INTO invites (email, token) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $inviteEmail, $token);
$stmt->execute();
$stmt->close();

// Send the email
sendInvitation($inviteEmail, $token);
