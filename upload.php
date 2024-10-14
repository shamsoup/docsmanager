<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $project_id = $_POST['project_id'];
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $filePath = "uploads/" . basename($fileName);

    if (move_uploaded_file($fileTmpName, $filePath)) {
        $sql = "INSERT INTO documents (user_id, project_id, file_name, file_path) VALUES ('$user_id', '$project_id', '$fileName', '$filePath')";
        if ($conn->query($sql) === TRUE) {
            header("Location:dashboard.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>
