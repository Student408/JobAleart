<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Alerts</title>
    <link rel="stylesheet" href="./style/header.css">
    <!-- Additional meta tags, scripts, or stylesheets can be included here -->
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">JobAlerts</a>
            </div>
            <nav id="nav-menu">
                <div class="hamburger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <ul>
                    <!-- <li><a href="index.php">Home</a></li> -->
                    <li><a href="job_alerts.php">Jobs</a></li>
                    <li><a href="about_us.php">About</a></li>
                    <li><a href="admin.php">Admin</a></li>
                    <!-- Additional menu items can be added as needed -->
                </ul>
            </nav>
        </div>
    </header>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('#nav-menu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    </script>
</body>
</html>