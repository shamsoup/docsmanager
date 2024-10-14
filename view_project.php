<?php
session_start();
include 'db.php'; // Include your database connection file

// Enable error reporting (for development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the project ID from the query string (e.g., view_project.php?project_id=1)
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// Debug output
echo "Project ID: " . $project_id . "<br>"; // Debug output

if ($project_id == 0) {
    echo "Invalid project ID.";
    exit();
}

// Fetch the project details from the database
$sql = "SELECT * FROM projects WHERE project_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Project not found!";
    exit();
}

// Fetch project details
$project = $result->fetch_assoc();

// Fetch associated documents for this project
$sql_documents = "SELECT * FROM documents WHERE project_id = ?";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->bind_param("i", $project_id);
$stmt_documents->execute();
$documents_result = $stmt_documents->get_result();

// Close the statements
$stmt->close();
$stmt_documents->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>

        <h1>Project: <?php echo htmlspecialchars($project['project_name']); ?></h1>
        <p>Company: <?php echo htmlspecialchars($project['company_name']); ?></p>

        <h2>Team Members</h2>
        <ul>
            <?php
            // Assuming team members are stored in a JSON format or a serialized string
            $team_members = json_decode($project['team_members'], true);
            if (is_array($team_members)) {
                foreach ($team_members as $member) {
                    echo '<li>' . htmlspecialchars($member['name']) . ' (' . htmlspecialchars($member['role']) . ')</li>';
                }
            } else {
                echo '<li>No team members found.</li>';
            }
            ?>
        </ul>

        <h2>Project Documents</h2>
        <ul>
            <?php
            if ($documents_result->num_rows > 0) {
                while ($doc = $documents_result->fetch_assoc()) {
                    echo '<li><a href="' . htmlspecialchars($doc['doc_path']) . '" download>' . htmlspecialchars($doc['doc_name']) . '</a></li>';
                }
            } else {
                echo '<li>No documents uploaded for this project.</li>';
            }
            ?>
        </ul>

        <a href="upload.php?project_id=<?php echo $project_id; ?>">Upload New Document</a>
    </div>

    <?php
    // Optional: Close the database connection
    $conn->close();
    ?>
</body>
</html>
