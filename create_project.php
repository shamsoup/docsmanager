<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $project_name = $_POST['project_name'];
    $team_members = explode(',', $_POST['team_members']);
    $company_name = $_SESSION['company_name'];

    // Generate unique project ID
    $project_id = 'PRJ-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));

    // Insert project into the database
    $sql = "INSERT INTO projects (project_name, project_id, company_name, created_by) VALUES ('$project_name', '$project_id', '$company_name', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        $project_id_db = $conn->insert_id;

        // Add admin (creator) to project team
        $conn->query("INSERT INTO project_team (project_id, user_id, role) VALUES ('$project_id_db', '$user_id', 'admin')");

        // Add team members
        foreach ($team_members as $email) {
            $email = trim($email);
            $sql = "SELECT id FROM users WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $member = $result->fetch_assoc();
                $member_id = $member['id'];
                $conn->query("INSERT INTO project_team (project_id, user_id, role) VALUES ('$project_id_db', '$member_id', 'viewer')");
            }
        }

        header("Location: ../dashboard.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
