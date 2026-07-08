-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ekattor8 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to the user
GRANT ALL PRIVILEGES ON ekattor8.* TO 'ekattor8_user'@'%';
FLUSH PRIVILEGES;

-- Set default character set
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_connection=utf8mb4;
