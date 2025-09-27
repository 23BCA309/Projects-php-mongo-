<?php
include 'connect.php';

echo "=== CHECKING DATABASE TABLE STRUCTURES ===\n\n";

// Check if database connection is successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// List all tables in the database
echo "=== AVAILABLE TABLES IN DATABASE ===\n";
$tables_result = mysqli_query($con, "SHOW TABLES");
if ($tables_result) {
    while($table = mysqli_fetch_array($tables_result)) {
        echo "- " . $table[0] . "\n";
    }
} else {
    echo "Error retrieving tables: " . mysqli_error($con) . "\n";
}

echo "\n";

// Function to describe table structure
function describe_table($con, $table_name) {
    echo "=== $table_name TABLE STRUCTURE ===\n";
    $result = mysqli_query($con, "DESCRIBE $table_name");
    if ($result) {
        printf("%-20s %-20s %-10s %-10s %-15s %-10s\n", 
               "Field", "Type", "Null", "Key", "Default", "Extra");
        echo str_repeat("-", 85) . "\n";
        while($row = mysqli_fetch_assoc($result)) {
            printf("%-20s %-20s %-10s %-10s %-15s %-10s\n",
                   $row['Field'], 
                   $row['Type'], 
                   $row['Null'], 
                   $row['Key'], 
                   $row['Default'] ?? 'NULL', 
                   $row['Extra']);
        }
        
        // Also show sample data count
        $count_result = mysqli_query($con, "SELECT COUNT(*) as count FROM $table_name");
        if ($count_result) {
            $count = mysqli_fetch_assoc($count_result)['count'];
            echo "\nSample data count: $count rows\n";
        }
        
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
    echo "\n";
}

// Check common table structures
$expected_tables = ['users_tbl', 'courses_tbl', 'videos_tbl', 'teachers_tbl', 'courses_videos'];

foreach ($expected_tables as $table) {
    $check_result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($check_result) > 0) {
        describe_table($con, $table);
    } else {
        echo "=== $table TABLE ===\n";
        echo "TABLE DOES NOT EXIST!\n\n";
    }
}

// Check for any table that might exist but we didn't expect
echo "=== CHECKING ALL EXISTING TABLES ===\n";
$all_tables = mysqli_query($con, "SHOW TABLES");
if ($all_tables) {
    while($table = mysqli_fetch_array($all_tables)) {
        $table_name = $table[0];
        if (!in_array($table_name, $expected_tables)) {
            describe_table($con, $table_name);
        }
    }
}

mysqli_close($con);
?>