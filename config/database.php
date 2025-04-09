<?php
$host = '127.0.0.1';
$db   = 'db_jumatan';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // First try to connect to the database
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES LIKE 'schedules'");
        if ($stmt->rowCount() == 0) {
            // Tables don't exist, create them
            $pdo->exec("CREATE TABLE IF NOT EXISTS `schedules` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `year` int(4) NOT NULL,
                      `month` int(2) NOT NULL,
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `year_month` (`year`,`month`)
                    );
                    
                    CREATE TABLE IF NOT EXISTS `schedule_details` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `schedule_id` int(11) NOT NULL,
                      `date` date NOT NULL,
                      `pasaran` varchar(10) NOT NULL,
                      `preacher` varchar(100) NOT NULL,
                      `muadzin` varchar(100) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `schedule_id` (`schedule_id`),
                      CONSTRAINT `schedule_details_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE
                    );");
        }
    } catch (\PDOException $e) {
        // If database doesn't exist, create it
        if ($e->getCode() == 1049) {
            $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            $pdo->exec("CREATE DATABASE `$db`;
                        USE `$db`;
                        
                        CREATE TABLE `schedules` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `year` int(4) NOT NULL,
                          `month` int(2) NOT NULL,
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `year_month` (`year`,`month`)
                        );
                        
                        CREATE TABLE `schedule_details` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `schedule_id` int(11) NOT NULL,
                          `date` date NOT NULL,
                          `pasaran` varchar(10) NOT NULL,
                          `preacher` varchar(100) NOT NULL,
                          `muadzin` varchar(100) DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `schedule_id` (`schedule_id`),
                          CONSTRAINT `schedule_details_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE
                        );");

            // Reconnect to the new database
            $pdo = new PDO($dsn, $user, $pass, $options);
        } else {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}