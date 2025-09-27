<?php
// Quick database structure check
include 'connect.php';

echo "<h2>Database Connection Test</h2>";

if (!$con) {
    echo "❌ Database connection failed: " . mysqli_connect_error();
    exit();
} else {
    echo "✅ Database connected successfully<br>";
}

echo "<h3>users_tbl structure:</h3>";
$result = mysqli_query($con, "DESCRIBE users_tbl");

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Error describing table: " . mysqli_error($con);
}

echo "<h3>Image Column Check:</h3>";
$column_check = mysqli_query($con, "SHOW COLUMNS FROM users_tbl LIKE 'image'");
if (mysqli_num_rows($column_check) > 0) {
    echo "✅ image column exists";
} else {
    echo "❌ image column does NOT exist";
    echo "<br><strong>Run this SQL to add it:</strong>";
    echo "<pre>ALTER TABLE `users_tbl` ADD COLUMN `image` VARCHAR(255) NULL DEFAULT NULL AFTER `role`;</pre>";
}

echo "<h3>Sample Users Data:</h3>";
$users_result = mysqli_query($con, "SELECT id, username, email, role, image, created_at FROM users_tbl LIMIT 3");
if ($users_result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Image</th><th>Created At</th></tr>";
    while ($row = mysqli_fetch_assoc($users_result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Error fetching users: " . mysqli_error($con);
}

mysqli_close($con);
?>