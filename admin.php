<?php
// Start session at the beginning of the script
session_start();

// Include database connection
include 'db.php';

// Check if session is active
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Session active, proceed to show job management form
    $showForm = true;
    // Extend session timeout if activity continues (optional)
    $_SESSION['timeout'] = time() + 1800; // Resetting the session timeout
} else {
    // Session expired or not logged in, redirect to login page or handle as needed
    $showForm = false;
}

// Initialize variables
$job = null;

// Display the appropriate content based on $showForm
if ($showForm) {
    // Include necessary files
    include 'header.php'; // Include header HTML

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

        // Insert or update logic
        if ($mode == 'insert' || $mode == 'update') {
            // Validate and sanitize input data
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $company = isset($_POST['company']) ? trim($_POST['company']) : '';
            $location = isset($_POST['location']) ? trim($_POST['location']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $qualification = isset($_POST['qualification']) ? trim($_POST['qualification']) : '';
            $branch = isset($_POST['branch']) ? trim($_POST['branch']) : '';
            $batch = isset($_POST['batch']) ? trim($_POST['batch']) : '';
            $salary = isset($_POST['salary']) ? trim($_POST['salary']) : '';
            $experience = isset($_POST['experience']) ? trim($_POST['experience']) : '';
            $date_posted = isset($_POST['date_posted']) ? trim($_POST['date_posted']) : '';
            $apply_link = isset($_POST['apply_link']) ? trim($_POST['apply_link']) : '';

            // Check if required fields are not empty
            if (!empty($title) && !empty($company) && !empty($location) && !empty($description) && !empty($qualification) && !empty($branch) && !empty($batch) && !empty($salary) && !empty($experience) && !empty($date_posted) && !empty($apply_link)) {
                try {
                    if ($mode == 'insert') {
                        // Insert new job details
                        $sql = "INSERT INTO job_alerts (title, company, location, description, qualification, branch, batch, salary, experience, date_posted, apply_link)
                                VALUES (:title, :company, :location, :description, :qualification, :branch, :batch, :salary, :experience, :date_posted, :apply_link)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            'title' => $title,
                            'company' => $company,
                            'location' => $location,
                            'description' => $description,
                            'qualification' => $qualification,
                            'branch' => $branch,
                            'batch' => $batch,
                            'salary' => $salary,
                            'experience' => $experience,
                            'date_posted' => $date_posted,
                            'apply_link' => $apply_link
                        ]);

                        echo "<p>Job details inserted successfully!</p>";
                    } elseif ($mode == 'update') {
                        // Update existing job details
                        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                        if ($id > 0) {
                            $sql = "UPDATE job_alerts SET title = :title, company = :company, location = :location, description = :description, qualification = :qualification, branch = :branch, batch = :batch, salary = :salary, experience = :experience, date_posted = :date_posted, apply_link = :apply_link WHERE id = :id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([
                                'title' => $title,
                                'company' => $company,
                                'location' => $location,
                                'description' => $description,
                                'qualification' => $qualification,
                                'branch' => $branch,
                                'batch' => $batch,
                                'salary' => $salary,
                                'experience' => $experience,
                                'date_posted' => $date_posted,
                                'apply_link' => $apply_link,
                                'id' => $id
                            ]);

                            echo "<p>Job details updated successfully!</p>";
                        } else {
                            echo "<p>Invalid ID for update.</p>";
                        }
                    }
                } catch (PDOException $e) {
                    echo "<p>Error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>Please fill in all fields.</p>";
            }
        } elseif ($mode == 'delete') {
            // Delete operation
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id > 0) {
                try {
                    $sql = "DELETE FROM job_alerts WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id' => $id]);
                    $rowCount = $stmt->rowCount(); // Check if a row was affected

                    if ($rowCount > 0) {
                        echo "<p>Job details with ID $id deleted successfully!</p>";
                    } else {
                        echo "<p>No job details found with ID $id to delete.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p>Error deleting job details: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>Invalid ID for deletion.</p>";
            }
        }
    }

    // Fetch job details for editing if ID is provided in GET request
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM job_alerts WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // Check if a row was found
        if ($stmt->rowCount() > 0) {
            // Fetch job details
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Set $job to null or handle the scenario where ID doesn't exist
            $job = null;
        }
    }

    // Display job management form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin - Insert/Update/Delete Job Details</title>
        <link rel="icon" href="logo.svg" type="image/svg+xml">
        <link rel="stylesheet" href="./style/admin.css">
    </head>
    <body>
        <div class="admin-form">
            <h2>Insert/Update/Delete Job Details</h2>
            <form method="POST">
                <label for="id">ID (for update/delete):</label>
                <input type="text" id="id" name="id" value="<?php echo isset($job) ? $job['id'] : ''; ?>">
                <button type="button" id="get-details-btn" onclick="getDetails()">Get Details</button>

                

                <label for="title">Job Title:</label>
                <input type="text" id="title" name="title" required value="<?php echo isset($job) ? $job['title'] : ''; ?>">

                <label for="company">Company:</label>
                <input type="text" id="company" name="company" required value="<?php echo isset($job) ? $job['company'] : ''; ?>">

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required value="<?php echo isset($job) ? $job['location'] : ''; ?>">

                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo isset($job) ? $job['description'] : ''; ?></textarea>

                <label for="qualification">Qualification:</label>
                <input type="text" id="qualification" name="qualification" required value="<?php echo isset($job) ? $job['qualification'] : ''; ?>">

                <label for="branch">Branch:</label>
                <input type="text" id="branch" name="branch" required value="<?php echo isset($job) ? $job['branch'] : ''; ?>">

                <label for="batch">Batch:</label>
                <input type="text" id="batch" name="batch" required value="<?php echo isset($job) ? $job['batch'] : ''; ?>">

                <label for="salary">Salary:</label>
                <input type="text" id="salary" name="salary" required value="<?php echo isset($job) ? $job['salary'] : ''; ?>">

                <label for="experience">Experience:</label>
                <input type="text" id="experience" name="experience" required value="<?php echo isset($job) ? $job['experience'] : ''; ?>">

                <label for="date_posted">Date Posted:</label>
                <input type="date" id="date_posted" name="date_posted" required value="<?php echo isset($job) ? $job['date_posted'] : ''; ?>">

                <label for="apply_link">Apply Link:</label>
                <input type="text" id="apply_link" name="apply_link" required value="<?php echo isset($job) ? $job['apply_link'] : ''; ?>">

                <input type="hidden" name="mode" id="mode" value="<?php echo isset($job) ? 'update' : 'insert'; ?>">
                <button type="submit" id="submit-btn"><?php echo isset($job) ? 'Update Job' : 'Insert Job'; ?></button>
            </form>
        </div>

        <script>
            function getDetails() {
                const id = document.getElementById('id').value;
                if (id) {
                    window.location.href = `admin.php?id=${id}`;
                } else {
                    alert('Please enter a valid ID');
                }
            }
        </script>

    </body>
    </html>

    <?php
    include 'footer.php'; // Include footer HTML
    ?>

    <?php
} else {
    // Redirect to login page or handle session expiry/non-logged-in state
    header("Location: login.php");
    exit();
}
?>
