
## Setup

### Database
1. Create a MySQL database named `job_alerts_db`.
2. Run the following SQL to create the `job_alerts` table:
    ```sql
    CREATE DATABASE job_alerts_db;

    USE job_alerts_db;

    CREATE TABLE job_alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT,
        qualification VARCHAR(255),
        branch VARCHAR(255),
        batch VARCHAR(255),
        salary VARCHAR(255),
        experience VARCHAR(255),
        date_posted DATE
        apply_link VARCHAR(255);
    );
    ```


CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
);


### Configuration
1. Update the `db.php` file with your database credentials.

## Usage
- Navigate to `index.php` to view the homepage.
- Use `admin.php` to insert new job alerts into the database.

## Features
- List and filter job alerts.
- View detailed job descriptions.
- Admin page for inserting job details.
