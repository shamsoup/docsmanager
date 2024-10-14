<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Bind the email as a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['company_name'] = $user['company_name'];

            echo "Redirecting to dashboard...";
            sleep(2); // Delay for observation (optional)

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Password did not match
            echo "Invalid password!";
        }
    } else {
        // No user found with that email
        echo "No user found with that email!";
    }

    // Close statement
    $stmt->close();
}
?>

