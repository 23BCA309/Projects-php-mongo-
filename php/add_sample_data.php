<?php
// Test script to add sample courses and videos for demonstration
include 'connect.php';

echo "<h1>🧪 Adding Sample Data for Testing</h1>";

// First, let's see if we have any courses
echo "<h2>📊 Current Status:</h2>";
$courses_result = @mysqli_query($con, "SELECT COUNT(*) as count FROM courses");
$courses_count = $courses_result ? mysqli_fetch_assoc($courses_result)['count'] : 0;
echo "<p><strong>Existing Courses:</strong> $courses_count</p>";

$videos_result = @mysqli_query($con, "SELECT COUNT(*) as count FROM course_videos");
$videos_count = $videos_result ? mysqli_fetch_assoc($videos_result)['count'] : 0;
echo "<p><strong>Existing Videos:</strong> $videos_count</p>";

// Sample data
$sample_courses = [
    [
        'title' => '7 Hard Challenge',
        'description' => 'Intense 7-day yoga challenge for advanced practitioners. Push your limits and transform your practice.',
        'level' => 'advanced',
        'videos' => [
            ['title' => 'Day 1: Power Flow', 'description' => 'Start your challenge with an intense power flow sequence', 'duration' => '45 min'],
            ['title' => 'Day 2: Core Strength', 'description' => 'Build your core strength with challenging poses', 'duration' => '35 min'],
            ['title' => 'Day 3: Balance & Focus', 'description' => 'Test your balance with advanced poses', 'duration' => '40 min']
        ]
    ],
    [
        'title' => 'Morning Yoga Basics',
        'description' => 'Perfect for beginners who want to start their day with gentle yoga movements and breathing exercises.',
        'level' => 'beginner',
        'videos' => [
            ['title' => 'Morning Stretch Routine', 'description' => 'Gentle stretches to wake up your body', 'duration' => '15 min'],
            ['title' => 'Breathing & Meditation', 'description' => 'Start your day with mindful breathing', 'duration' => '10 min'],
            ['title' => 'Sun Salutation for Beginners', 'description' => 'Learn the classic sun salutation sequence', 'duration' => '20 min']
        ]
    ],
    [
        'title' => 'Flexibility & Stretching',
        'description' => 'Improve your flexibility with targeted stretching exercises and deep stretches for all muscle groups.',
        'level' => 'intermediate',
        'videos' => [
            ['title' => 'Hip Openers', 'description' => 'Deep stretches for tight hips', 'duration' => '25 min'],
            ['title' => 'Hamstring Flexibility', 'description' => 'Gentle stretches for hamstring flexibility', 'duration' => '20 min']
        ]
    ]
];

echo "<h2>🚀 Adding Sample Data:</h2>";

foreach ($sample_courses as $course_data) {
    echo "<h3>📚 Course: " . htmlspecialchars($course_data['title']) . "</h3>";
    
    // Create course folder
    $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $course_data['title']));
    $folder_name = preg_replace('/_{2,}/', '_', $folder_name);
    $course_folder_path = "content/courses/" . $folder_name;
    
    if (!file_exists($course_folder_path)) {
        mkdir($course_folder_path, 0777, true);
        echo "📁 Created folder: $course_folder_path<br>";
    } else {
        echo "📁 Folder already exists: $course_folder_path<br>";
    }
    
    // Check if course already exists
    $title_escaped = mysqli_real_escape_string($con, $course_data['title']);
    $existing_course = mysqli_query($con, "SELECT id FROM courses WHERE title = '$title_escaped'");
    
    if (mysqli_num_rows($existing_course) > 0) {
        $course_id = mysqli_fetch_assoc($existing_course)['id'];
        echo "✅ Course already exists (ID: $course_id)<br>";
    } else {
        // Insert course
        $description = mysqli_real_escape_string($con, $course_data['description']);
        $level = mysqli_real_escape_string($con, $course_data['level']);
        $created_at = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO courses (title, description, level, created_at) VALUES ('$title_escaped', '$description', '$level', '$created_at')";
        
        if (mysqli_query($con, $sql)) {
            $course_id = mysqli_insert_id($con);
            echo "✅ Course created successfully (ID: $course_id)<br>";
        } else {
            echo "❌ Error creating course: " . mysqli_error($con) . "<br>";
            continue;
        }
    }
    
    // Add videos for this course
    echo "<strong>Adding videos:</strong><br>";
    foreach ($course_data['videos'] as $video_data) {
        // Check if video already exists
        $video_title_escaped = mysqli_real_escape_string($con, $video_data['title']);
        $existing_video = mysqli_query($con, "SELECT id FROM course_videos WHERE course_id = '$course_id' AND title = '$video_title_escaped'");
        
        if (mysqli_num_rows($existing_video) > 0) {
            echo "&nbsp;&nbsp;🎥 Video already exists: " . htmlspecialchars($video_data['title']) . "<br>";
        } else {
            $video_description = mysqli_real_escape_string($con, $video_data['description']);
            $duration = mysqli_real_escape_string($con, $video_data['duration']);
            
            // Create sample video path (since we don't have actual video files)
            $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $video_data['title']));
            $video_url = $course_folder_path . '/' . time() . '_' . $safe_title . '.mp4';
            
            $video_sql = "INSERT INTO course_videos (course_id, title, description, video_url, duration, created_at) 
                         VALUES ('$course_id', '$video_title_escaped', '$video_description', '$video_url', '$duration', '$created_at')";
            
            if (mysqli_query($con, $video_sql)) {
                echo "&nbsp;&nbsp;✅ Video added: " . htmlspecialchars($video_data['title']) . "<br>";
                
                // Create a placeholder file to show the structure
                file_put_contents($video_url . '.txt', "Placeholder for: " . $video_data['title'] . "\nDescription: " . $video_data['description'] . "\nDuration: " . $video_data['duration']);
            } else {
                echo "&nbsp;&nbsp;❌ Error adding video: " . mysqli_error($con) . "<br>";
            }
        }
    }
    
    echo "<hr>";
}

// Final status
echo "<h2>📊 Final Status:</h2>";
$final_courses_result = mysqli_query($con, "SELECT COUNT(*) as count FROM courses");
$final_courses_count = mysqli_fetch_assoc($final_courses_result)['count'];
echo "<p><strong>Total Courses:</strong> $final_courses_count</p>";

$final_videos_result = mysqli_query($con, "SELECT COUNT(*) as count FROM course_videos");
$final_videos_count = mysqli_fetch_assoc($final_videos_result)['count'];
echo "<p><strong>Total Videos:</strong> $final_videos_count</p>";

echo "<h2>🎯 Next Steps:</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<ol>";
echo "<li><strong>View Courses:</strong> <a href='courses.php' target='_blank'>Go to Courses Page</a></li>";
echo "<li><strong>Admin Panel:</strong> <a href='admin-panel.php' target='_blank'>Manage Courses & Videos</a></li>";
echo "<li><strong>Test Video Upload:</strong> Upload actual video files through the admin panel</li>";
echo "</ol>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sample Data Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c5a87; }
        hr { margin: 20px 0; border: 1px solid #ddd; }
        a { color: #4a7c59; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
</body>
</html>