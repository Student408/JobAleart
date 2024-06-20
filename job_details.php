<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details</title>
    <link rel="icon" href="logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./style/job_details.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'header.php'; ?>
        <div class="content-wrapper">
            <?php
            include 'db.php';
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM job_alerts WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="job-details">
                <h2><?php echo $job['company']; ?></h2>
                <h3><?php echo $job['title']; ?></h3>
                <p><?php echo $job['description']; ?></p>
                <p><strong>Qualification:</strong> <?php echo $job['qualification']; ?></p>
                <p><strong>Branch:</strong> <?php echo $job['branch']; ?></p>
                <p><strong>Batch:</strong> <?php echo $job['batch']; ?></p>
                <p><strong>Salary:</strong> <?php echo $job['salary']; ?></p>
                <p><strong>Experience:</strong> <?php echo $job['experience']; ?></p>
                <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
                <p><strong>ID:</strong> <?php echo $job['id']; ?></p> <!-- Displaying the ID -->
                <?php if (!empty($job['apply_link'])): ?>
                    <p><strong>Apply Here:</strong> <a href="<?php echo $job['apply_link']; ?>" target="_blank">Apply Now</a></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer">
            <?php include 'footer.php'; ?>
        </div>
    </div>
</body>
</html>
