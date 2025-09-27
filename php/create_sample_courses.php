<?php
// Sample script to create course folders and demonstrate the structure
include 'connect.php';

function createSampleCourses($con) {
    echo "<h2>Creating Sample Course Folders Structure</h2>";
    
    $sample_courses = [
        "7 Hard Challenge",
        "Morning Yoga Basics", 
        "Advanced Power Yoga",
        "Meditation for Beginners",
        "Flexibility & Stretching"
    ];
    
    foreach ($sample_courses as $course_title) {
        echo "<h3>Processing Course: " . htmlspecialchars($course_title) . "</h3>";
        
        // Create folder name (same logic as admin panel)
        $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $course_title));
        $folder_name = preg_replace('/_{2,}/', '_', $folder_name);
        $course_folder_path = "content/courses/" . $folder_name;
        
        echo "📁 Folder name: " . $folder_name . "<br>";
        echo "📂 Full path: " . $course_folder_path . "<br>";
        
        // Create the course folder
        if (!file_exists($course_folder_path)) {
            if (mkdir($course_folder_path, 0777, true)) {
                echo "✅ Folder created successfully!<br>";
                
                // Create a sample README file in the folder
                $readme_content = "# " . $course_title . "\n\nThis folder contains all videos for the '" . $course_title . "' course.\n\nCreated: " . date('Y-m-d H:i:s');
                file_put_contents($course_folder_path . "/README.txt", $readme_content);
                echo "📄 README.txt created<br>";
                
            } else {
                echo "❌ Failed to create folder<br>";
            }
        } else {
            echo "📁 Folder already exists<br>";
        }
        
        echo "<hr>";
    }
}

function displayCurrentStructure() {
    echo "<h2>Current Courses Folder Structure</h2>";
    $courses_path = "content/courses";
    
    if (file_exists($courses_path)) {
        $folders = scandir($courses_path);
        echo "<ul>";
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..' && is_dir($courses_path . '/' . $folder)) {
                echo "<li><strong>" . htmlspecialchars($folder) . "/</strong>";
                
                // Show files in each course folder
                $course_files = scandir($courses_path . '/' . $folder);
                if (count($course_files) > 2) { // More than just . and ..
                    echo "<ul>";
                    foreach ($course_files as $file) {
                        if ($file != '.' && $file != '..') {
                            echo "<li>" . htmlspecialchars($file) . "</li>";
                        }
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "❌ Courses folder doesn't exist yet.";
    }
}

function showExpectedStructureExample() {
    echo "<h2>Expected Folder Structure After Admin Creates Courses</h2>";
    echo "<pre>";
    echo "php/content/courses/\n";
    echo "├── 7_Hard_Challenge/\n";
    echo "│   ├── 1640995200_warmup_session.mp4\n";
    echo "│   ├── 1640995300_main_workout.mp4\n";
    echo "│   └── 1640995400_cooldown.mp4\n";
    echo "├── Morning_Yoga_Basics/\n";
    echo "│   ├── 1640995500_introduction.mp4\n";
    echo "│   └── 1640995600_basic_poses.mp4\n";
    echo "├── Advanced_Power_Yoga/\n";
    echo "│   ├── 1640995700_power_flow.mp4\n";
    echo "│   └── 1640995800_advanced_poses.mp4\n";
    echo "└── Meditation_for_Beginners/\n";
    echo "    ├── 1640995900_breathing_basics.mp4\n";
    echo "    └── 1641000000_guided_meditation.mp4\n";
    echo "</pre>";
}

// Run the demonstrations
createSampleCourses($con);
displayCurrentStructure();
showExpectedStructureExample();

echo "<h2>🎯 How It Works:</h2>";
echo "<ol>";
echo "<li><strong>Admin creates a course:</strong> Course name \"7 Hard Challenge\" → Folder \"7_Hard_Challenge\" is created</li>";
echo "<li><strong>Admin uploads videos:</strong> Videos are automatically stored in \"content/courses/7_Hard_Challenge/\"</li>";
echo "<li><strong>Database stores path:</strong> video_url = \"content/courses/7_Hard_Challenge/1640995200_video_name.mp4\"</li>";
echo "<li><strong>Website displays:</strong> Videos are loaded from the organized folder structure</li>";
echo "</ol>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Folder Structure Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #2c5a87; border-bottom: 2px solid #2c5a87; padding-bottom: 5px; }
        h3 { color: #4a7c87; }
        pre { background: #f4f4f4; padding: 15px; border-left: 4px solid #2c5a87; }
        ul { list-style-type: none; }
        li { margin: 5px 0; }
        hr { margin: 20px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
</body>
</html>