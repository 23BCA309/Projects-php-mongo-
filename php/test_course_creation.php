<?php
// Test script showing course creation and video upload workflow
echo "<h1>Course Creation & Video Upload Test</h1>";

// Example of how the system will work:
$example_courses = [
    [
        'title' => '7 Hard Challenge',
        'description' => 'Intense 7-day yoga challenge for advanced practitioners',
        'level' => 'advanced',
        'thumbnail' => 'will_be_uploaded.jpg'
    ],
    [
        'title' => 'Morning Yoga Basics',
        'description' => 'Gentle morning yoga routine for beginners',
        'level' => 'beginner',
        'thumbnail' => 'will_be_uploaded.jpg'
    ]
];

echo "<h2>📋 Course Creation Process:</h2>";
foreach ($example_courses as $course) {
    echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
    echo "<h3>📚 Course: " . $course['title'] . "</h3>";
    
    // Show folder name conversion
    $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $course['title']));
    $folder_name = preg_replace('/_{2,}/', '_', $folder_name);
    $course_folder_path = "content/courses/" . $folder_name;
    
    echo "<p><strong>📁 Folder created:</strong> " . $course_folder_path . "</p>";
    echo "<p><strong>📊 Level:</strong> " . $course['level'] . "</p>";
    echo "<p><strong>📝 Description:</strong> " . $course['description'] . "</p>";
    echo "<p><strong>🖼️ Thumbnail location:</strong> " . $course_folder_path . "/thumbnail_" . time() . ".jpg</p>";
    
    echo "<h4>📂 Expected folder structure:</h4>";
    echo "<pre>";
    echo $course_folder_path . "/\n";
    echo "├── thumbnail_" . time() . ".jpg (course thumbnail)\n";
    echo "├── " . (time()+100) . "_intro_video.mp4\n";
    echo "├── " . (time()+200) . "_session_1.mp4\n";
    echo "├── " . (time()+300) . "_session_2.mp4\n";
    echo "└── " . (time()+400) . "_final_session.mp4\n";
    echo "</pre>";
    
    echo "</div>";
}

echo "<h2>🎥 Video Upload Process:</h2>";
echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h3>Step-by-step workflow:</h3>";
echo "<ol>";
echo "<li><strong>Admin selects course:</strong> '7 Hard Challenge' from dropdown</li>";
echo "<li><strong>System finds course folder:</strong> content/courses/7_Hard_Challenge/</li>";
echo "<li><strong>Video uploaded:</strong> warmup_session.mp4</li>";
echo "<li><strong>File renamed:</strong> " . time() . "_warmup_session.mp4</li>";
echo "<li><strong>Stored in:</strong> content/courses/7_Hard_Challenge/" . time() . "_warmup_session.mp4</li>";
echo "<li><strong>Database record:</strong> video_url = 'content/courses/7_Hard_Challenge/" . time() . "_warmup_session.mp4'</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🗄️ Database Tables Structure:</h2>";
echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
echo "<h3>Courses Table:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>id</th><th>title</th><th>description</th><th>level</th><th>thumbnail</th><th>created_at</th></tr>";
echo "<tr><td>1</td><td>7 Hard Challenge</td><td>Intense 7-day yoga challenge...</td><td>advanced</td><td>content/courses/7_Hard_Challenge/thumbnail_" . time() . ".jpg</td><td>2025-09-26 12:30:00</td></tr>";
echo "<tr><td>2</td><td>Morning Yoga Basics</td><td>Gentle morning yoga routine...</td><td>beginner</td><td>content/courses/Morning_Yoga_Basics/thumbnail_" . (time()+50) . ".jpg</td><td>2025-09-26 12:35:00</td></tr>";
echo "</table>";

echo "<h3>Course Videos Table:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%; margin-top: 15px;'>";
echo "<tr><th>id</th><th>course_id</th><th>title</th><th>description</th><th>video_url</th><th>duration</th></tr>";
echo "<tr><td>1</td><td>1</td><td>Warmup Session</td><td>Initial warmup for the challenge</td><td>content/courses/7_Hard_Challenge/" . time() . "_warmup_session.mp4</td><td>15 min</td></tr>";
echo "<tr><td>2</td><td>1</td><td>Main Workout</td><td>Core challenge workout</td><td>content/courses/7_Hard_Challenge/" . (time()+100) . "_main_workout.mp4</td><td>45 min</td></tr>";
echo "<tr><td>3</td><td>2</td><td>Basic Poses</td><td>Introduction to basic yoga poses</td><td>content/courses/Morning_Yoga_Basics/" . (time()+200) . "_basic_poses.mp4</td><td>20 min</td></tr>";
echo "</table>";
echo "</div>";

echo "<h2>✅ Benefits of This System:</h2>";
echo "<ul>";
echo "<li><strong>🗂️ Organized Storage:</strong> Each course has its own folder</li>";
echo "<li><strong>🖼️ Thumbnail Management:</strong> Course thumbnails stored with course files</li>";
echo "<li><strong>🔗 Easy Retrieval:</strong> Database stores complete file paths</li>";
echo "<li><strong>📱 Scalable:</strong> Easy to add more courses and videos</li>";
echo "<li><strong>🛡️ File Validation:</strong> Only allows proper image/video formats</li>";
echo "<li><strong>📏 Size Control:</strong> File size limits prevent server overload</li>";
echo "</ul>";

echo "<h2>🚀 How to Use:</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>For Admin:</h3>";
echo "<ol>";
echo "<li><strong>Create Course:</strong> Go to admin panel → Courses → Add New Course</li>";
echo "<li><strong>Fill details:</strong> Title, Description, Level, Upload Thumbnail</li>";
echo "<li><strong>Submit:</strong> System creates folder and database entry</li>";
echo "<li><strong>Add Videos:</strong> Go to Videos → Add New Video → Select Course → Upload Video</li>";
echo "<li><strong>Organized:</strong> Videos automatically go to correct course folder</li>";
echo "</ol>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Creation System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c5a87; }
        table { margin-top: 10px; }
        th { background-color: #2c5a87; color: white; padding: 8px; }
        td { padding: 8px; }
        pre { background: #f4f4f4; padding: 10px; border-left: 4px solid #2c5a87; font-size: 14px; }
    </style>
</head>
<body>
</body>
</html>