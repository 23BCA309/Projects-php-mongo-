<?php
// Simple test to verify admin functions work correctly
echo "<h1>🧪 Admin Functions Test</h1>";

// Test course folder creation
echo "<h2>📁 Testing Course Folder Creation</h2>";

$test_courses = [
    "Test Course 1",
    "7 Hard Challenge", 
    "Morning Yoga Basics"
];

foreach ($test_courses as $course_title) {
    echo "<h3>Testing: " . htmlspecialchars($course_title) . "</h3>";
    
    // Same folder creation logic as admin panel
    $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $course_title));
    $folder_name = preg_replace('/_{2,}/', '_', $folder_name);
    $course_folder_path = "content/courses/" . $folder_name;
    
    echo "📂 Folder path: <strong>" . $course_folder_path . "</strong><br>";
    
    // Create folder if doesn't exist
    if (!file_exists($course_folder_path)) {
        if (mkdir($course_folder_path, 0777, true)) {
            echo "✅ Folder created successfully!<br>";
        } else {
            echo "❌ Failed to create folder!<br>";
        }
    } else {
        echo "📁 Folder already exists!<br>";
    }
    
    // Test video file path
    $video_title = "Sample Video";
    $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $video_title));
    $file_name = time() . '_' . $safe_title . '.mp4';
    $video_path = $course_folder_path . '/' . $file_name;
    
    echo "🎥 Video would be stored at: <strong>" . $video_path . "</strong><br>";
    echo "<hr>";
}

// Test database query structure (without actual database connection)
echo "<h2>🗄️ Testing Database Query Structure</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px;'>";
echo "<h3>Course Creation Query:</h3>";
echo "<code>";
echo "INSERT INTO courses (title, description, level, thumbnail, created_at)<br>";
echo "VALUES ('Test Course', 'Test Description', 'beginner', 'content/courses/Test_Course/thumbnail_" . time() . ".jpg', NOW())";
echo "</code>";

echo "<h3>Video Upload Query:</h3>";
echo "<code>";
echo "INSERT INTO course_videos (course_id, title, description, video_url, duration, created_at)<br>";
echo "VALUES ('1', 'Test Video', 'Test Description', 'content/courses/Test_Course/" . time() . "_test_video.mp4', '15 min', NOW())";
echo "</code>";
echo "</div>";

// Show current folder structure
echo "<h2>📂 Current Courses Folder Structure</h2>";
$courses_path = "content/courses";
if (file_exists($courses_path)) {
    $folders = scandir($courses_path);
    echo "<ul>";
    foreach ($folders as $folder) {
        if ($folder != '.' && $folder != '..' && is_dir($courses_path . '/' . $folder)) {
            echo "<li><strong>📁 " . htmlspecialchars($folder) . "/</strong>";
            
            // Show files in folder
            $course_files = scandir($courses_path . '/' . $folder);
            if (count($course_files) > 2) {
                echo "<ul>";
                foreach ($course_files as $file) {
                    if ($file != '.' && $file != '..') {
                        $icon = (pathinfo($file, PATHINFO_EXTENSION) === 'txt') ? '📄' : '🎥';
                        echo "<li>$icon " . htmlspecialchars($file) . "</li>";
                    }
                }
                echo "</ul>";
            }
            echo "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "❌ Courses folder doesn't exist!";
}

echo "<h2>✅ Next Steps</h2>";
echo "<div style='background: #f0fff0; padding: 15px; border-radius: 8px;'>";
echo "<ol>";
echo "<li><strong>Database Setup:</strong> Make sure your courses table has the columns: id, title, description, level, thumbnail, created_at</li>";
echo "<li><strong>Video Table:</strong> Make sure course_videos table exists with: id, course_id, title, description, video_url, duration, created_at</li>";
echo "<li><strong>Test Admin Panel:</strong> Go to admin-panel.php and try creating a course</li>";
echo "<li><strong>Test Video Upload:</strong> After creating a course, try uploading a video</li>";
echo "</ol>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Functions Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c5a87; }
        code { background: #f4f4f4; padding: 10px; display: block; border-left: 4px solid #2c5a87; margin: 10px 0; }
        ul { list-style-type: none; }
        li { margin: 5px 0; }
        hr { margin: 20px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
</body>
</html>