<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$company_name = $_SESSION['company_name'];

// Fetch projects for the logged-in user's company using prepared statements
$stmt = $conn->prepare("SELECT * FROM projects WHERE company_name = ?");
$stmt->bind_param("s", $company_name);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Construction Docs</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to your Dashboard</h2>

        <!-- Create New Project Form -->
        <h3>Create a New Project</h3>
        <form action="create_project.php" method="POST">
            <label for="project_name">Project Name:</label>
            <input type="text" id="project_name" name="project_name" required>

            <label for="team_members">Team Members (Emails, comma-separated):</label>
            <input type="text" id="team_members" name="team_members" required>

            <input type="submit" value="Create Project">
        </form>

        <!-- List of Projects -->
        <h3>Your Projects</h3>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($row['project_name']); ?> (ID: <?php echo htmlspecialchars($row['project_id']); ?>)
                    <a href="view_project.php?project_id=<?php echo $row['project_id']; ?>">View</a>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Add Project Dropdown to Upload Form -->
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <h3>Upload Document</h3>
            <label for="project">Select Project:</label>
            <select name="project_id" id="project" required>
                <?php
                // Reset the result pointer for reuse
                $result->data_seek(0); // Reset the pointer to fetch projects again
                while ($row = $result->fetch_assoc()):
                ?>
                    <option value="<?php echo htmlspecialchars($row['project_id']); ?>"><?php echo htmlspecialchars($row['project_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="file">Upload File:</label>
            <input type="file" name="file" required>
            <input type="submit" value="Upload">
        </form>
    </div>
</body>
</html>

<?php
// Optional: Close the database connection
$conn->close();
?>
