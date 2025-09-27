<?php
include 'connect.php';

// Insert sample data into the courses/videos table (first table structure)
echo "Inserting sample data into courses/videos table...\n";

$sample_courses = [
    [1, 'Yoga for Beginners', 'Learn the basics of yoga practice', 'https://example.com/video1.mp4', '30 min', '2023-06-01 10:00:00'],
    [2, 'Advanced Asana Practice', 'Deepen your yoga practice with advanced poses', 'https://example.com/video2.mp4', '45 min', '2023-06-02 10:00:00'],
    [3, 'Meditation & Mindfulness', 'Learn meditation and mindfulness techniques', 'https://example.com/video3.mp4', '20 min', '2023-06-03 10:00:00'],
    [4, 'Yin Yoga Deep Stretch', 'Relaxing yin yoga for deep stretching', 'https://example.com/video4.mp4', '60 min', '2023-06-04 10:00:00']
];

foreach ($sample_courses as $course) {
    $sql = "INSERT INTO courses_videos_tbl (course_id, title, description, video_url, duration, created_at)
            VALUES ($course[0], '$course[1]', '$course[2]', '$course[3]', '$course[4]', '$course[5]')";

    if (mysqli_query($con, $sql)) {
        echo "Inserted: $course[1]\n";
    } else {
        echo "Error inserting $course[1]: " . mysqli_error($con) . "\n";
    }
}

// Insert sample data into the teachers table (second table structure)
echo "\nInserting sample data into teachers table...\n";

$sample_teachers = [
    ['Sarah Johnson', 'Expert yoga instructor with 10+ years experience'],
    ['Michael Chen', 'Certified meditation teacher and wellness coach'],
    ['Emma Wilson', 'Specialist in therapeutic yoga and stress relief'],
    ['David Brown', 'Advanced yoga practitioner and mindfulness expert']
];

foreach ($sample_teachers as $teacher) {
    $sql = "INSERT INTO teachers_tbl (title, description, created_at)
            VALUES ('$teacher[0]', '$teacher[1]', NOW())";

    if (mysqli_query($con, $sql)) {
        echo "Inserted teacher: $teacher[0]\n";
    } else {
        echo "Error inserting teacher $teacher[0]: " . mysqli_error($con) . "\n";
    }
}

// Insert sample data into users table
echo "\nInserting sample data into users table...\n";

$sample_users = [
    ['John Doe', 'john@example.com', '2023-01-15', 'Active'],
    ['Jane Smith', 'jane@example.com', '2023-02-20', 'Active'],
    ['Bob Johnson', 'bob@example.com', '2023-03-10', 'Inactive'],
    ['Alice Brown', 'alice@example.com', '2023-04-05', 'Active'],
    ['Charlie Wilson', 'charlie@example.com', '2023-05-12', 'Active']
];

foreach ($sample_users as $user) {
    $sql = "INSERT INTO users_tbl (name, email, joined, status)
            VALUES ('$user[0]', '$user[1]', '$user[2]', '$user[3]')";

    if (mysqli_query($con, $sql)) {
        echo "Inserted user: $user[0]\n";
    } else {
        echo "Error inserting user $user[0]: " . mysqli_error($con) . "\n";
    }
}

echo "\nSample data insertion completed!\n";
?>
