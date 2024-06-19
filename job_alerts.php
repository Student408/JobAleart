<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Alerts</title>
    <link rel="stylesheet" href="./style/job_alert.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include 'header.php'; ?>
        <div class="content-wrapper">
            <div class="job-listings">
                <h2>Job Alerts</h2>

                <!-- Search Form -->
                <form method="GET" action="job_alerts.php">
                    <input type="text" name="search" placeholder="Search...">
                    <select name="filter">
                        <option value="">Filter By</option>
                        <option value="Date Posted">Date Posted</option>
                        <option value="salary">Salary</option>
                        <option value="experience">Experience</option>
                    </select>
                    <button type="submit">Search</button>
                </form>

                <?php
                include 'db.php';

                // Pagination logic (if needed)
                $limit = 10; // Number of job alerts per page
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Search and Filter Parameters
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

                // SQL Query
                $sql = "SELECT * FROM job_alerts WHERE (title LIKE :search OR company LIKE :search OR location LIKE :search OR description LIKE :search OR qualification LIKE :search OR branch LIKE :search OR batch LIKE :search OR salary LIKE :search)";

                // Adding Filter
                if ($filter == 'Date Posted') {
                    $sql .= " ORDER BY date_posted";
                } elseif ($filter == 'salary') {
                    $sql .= " ORDER BY salary";
                } elseif ($filter == 'experience') {
                    $sql .= " ORDER BY experience";
                }

                // Adding Limit and Offset
                $sql .= " LIMIT :limit OFFSET :offset";

                $stmt = $conn->prepare($sql);
                $searchParam = "%" . $search . "%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display job alerts
                foreach ($jobs as $job) {
                    echo '<div class="job">';
                    echo '<h3>' . $job['title'] . '</h3>';
                    echo '<p><strong>Company:</strong> ' . $job['company'] . '</p>';
                    echo '<p><strong>Location:</strong> ' . $job['location'] . '</p>';
                    echo '<p>' . substr($job['description'], 0, 100) . '... <a href="job_details.php?id=' . $job['id'] . '">Read more</a></p>';
                    if (!empty($job['apply_link'])) {
                        echo '<p><strong>Apply Here:</strong> <a href="' . $job['apply_link'] . '" target="_blank">Apply Now</a></p>';
                    }
                    echo '</div>';
                }

                // Pagination links (if needed)
                $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM job_alerts WHERE (title LIKE :search OR company LIKE :search OR location LIKE :search OR description LIKE :search OR qualification LIKE :search OR branch LIKE :search OR batch LIKE :search OR salary LIKE :search)");
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_jobs = $row['total'];
                $total_pages = ceil($total_jobs / $limit);

                echo '<div class="pagination">';
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<a href="job_alerts.php?page=' . $i . '&search=' . $search . '&filter=' . $filter . '&sort=' . $sort . '">' . $i . '</a>';
                }
                echo '</div>';
                ?>
            </div>
        </div>
        <div class="footer">
            <?php include 'footer.php'; ?>
        </div>
    </div>
</body>
</html>