<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobAlerts - Home</title>
    <link rel="icon" href="logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./style/index.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Find Your Dream Job</h1>
                <p>Explore thousands of job opportunities and get hired by top companies.</p>
                <a href="job_alerts.php" class="btn">Browse Job Alerts</a>
            </div>
        </div>
    </section>

    <div class="icontainer">
        <div class="featured-jobs">
            <h2>Featured Job Alerts</h2>
            <?php
            include 'db.php';
            $stmt = $conn->query("SELECT * FROM job_alerts ORDER BY date_posted DESC LIMIT 5");
            $job_alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($job_alerts as $job) {
                echo "<div class='job'>
                        <h3>{$job['title']}</h3>
                        <p>{$job['company']}</p>
                        <p>{$job['location']}</p>
                        <p>{$job['description']}</p>
                        <a href='job_details.php?id={$job['id']}'>Read More</a>
                      </div>";
            }
            ?>
        </div>
    </div>
    <div class="footer">
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>