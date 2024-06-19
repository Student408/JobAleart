<?php
// Start session at the beginning of the script
session_start();

// Function to check if the entered password is correct
function checkPassword($enteredPassword) {
    // Replace 'yourpassword' with your desired password
    $correctPassword = 'yourpassword'; // Replace with your actual password
    return password_verify($enteredPassword, password_hash($correctPassword, PASSWORD_DEFAULT));
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if password is submitted and not empty
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $enteredPassword = $_POST['password'];
        if (checkPassword($enteredPassword)) {
            // Password correct, proceed to show job management form
            
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['timeout'] = time() + 1800; // 30 minutes timeout

            // Redirect to job management form or set a flag to show it
            $showForm = true;
        } else {
            // Password incorrect, show error message
            $errorMessage = 'Incorrect password! Please try again.';
            $showForm = false;
        }
    } else {
        // Password not submitted or empty, show error message
        $errorMessage = 'Please enter the password.';
        $showForm = false;
    }
} else {
    // Check if session is active and within timeout period
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && time() < $_SESSION['timeout']) {
        // Session active, proceed to show job management form
        $showForm = true;
        // Extend session timeout if activity continues (optional)
        $_SESSION['timeout'] = time() + 1800; // Resetting the session timeout
    } else {
        // Session expired or not logged in, show password form initially
        $showForm = false;
    }
}


// Display the appropriate content based on $showForm
if ($showForm) {
    // Include necessary files
    include 'header.php'; // Include header HTML
    include 'db.php'; // Include database connection

    // Job management form and logic
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mode'])) {
        $mode = $_POST['mode'];

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
                        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
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
                $sql = "DELETE FROM job_alerts WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['id' => $id]);

                echo "<p>Job details deleted successfully!</p>";
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
        <link rel="stylesheet" href="./style/admin.css">
    </head>
    <body>
        <div class="admin-form">
            <h2>Insert/Update/Delete Job Details</h2>
            <form method="POST">
                <label for="id">ID (for update):</label>
                <input type="text" id="id" name="id" value="<?php echo isset($job) ? $job['id'] : ''; ?>">
                <button type="button" id="get-details-btn" onclick="getDetails()">Get Details</button>
<?php if (isset($job)) { ?>
    <button type="submit" name="mode" value="delete" onclick="return confirm('Are you sure you want to delete this job?')" style="margin-top: 10px;">Delete</button>

<?php } ?>

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

<?php
include 'footer.php'; // Include footer HTML
?>
</body>
</html>

<?php
} else {
   // Show password form if $showForm is false
   ?>
   <!DOCTYPE html>
   <html lang="en">
   <head>
       <meta charset="UTF-8">
       <title>Password Protected Page</title>
       <style>
       /* Reset default margin and padding */
       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
       }

       /* Body styles */
       body {
           font-family: monospace, Arial, sans-serif;
           line-height: 1.6;
           background-color: #f0f0f0;
           color: #333;
           margin: 0;
           padding: 0;
       }

       /* Password form styles */
       .password-form {
           max-width: 400px;
           margin: 50px auto;
           padding: 20px;
           background-color: #fff;
           box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           border-radius: 8px;
       }

       .password-form h2 {
           margin-bottom: 20px;
           text-align: center;
       }

       .password-form form {
           display: flex;
           flex-direction: column;
       }

       .password-form label {
           margin-bottom: 10px;
       }

       .password-form input[type="password"] {
           padding: 10px;
           border: 1px solid #ccc;
           border-radius: 4px;
           margin-bottom: 15px;
           font-size: 1em;
       }

       .password-form button[type="submit"] {
           background-color: #007bff;
           color: #fff;
           border: none;
           padding: 10px 20px;
           cursor: pointer;
           border-radius: 4px;
           font-size: 1em;
       }

       .password-form button[type="submit"]:hover {
           background-color: #0056b3;
       }

       .password-form .error-message {
           color: #dc3545;
           margin-top: 10px;
           text-align: center;
       }
       </style>
   </head>
   <body>
       <div class="password-form">
           <h2>Enter Password to Access Job Management</h2>
           <form method="POST">
               <label for="password">Password:</label>
               <input type="password" id="password" name="password">
               <button type="submit">Submit</button>
           </form>
           <?php
           // Display error message if there's any
           if (isset($errorMessage)) {
               echo "<p class='error-message'>$errorMessage</p>";
           }
           ?>
       </div>
   </body>
   </html>

<?php } ?>