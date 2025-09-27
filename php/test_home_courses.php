<?php
// Test script to show home page with database courses functionality
include 'connect.php';

echo "<h1>🏠 Home Page Database Courses Integration Test</h1>";

// Show what courses would be displayed on home page
echo "<h2>📊 Courses that will appear on Home Page:</h2>";

$courses_query = "SELECT id, title, description, level, thumbnail, created_at FROM courses ORDER BY created_at DESC LIMIT 6";
$courses_result = mysqli_query($con, $courses_query);

if ($courses_result && mysqli_num_rows($courses_result) > 0) {
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin: 2rem 0;'>";
    
    while ($course = mysqli_fetch_assoc($courses_result)) {
        // Get video count
        $video_count_query = "SELECT COUNT(*) as count FROM course_videos WHERE course_id = '{$course['id']}'";
        $video_count_result = mysqli_query($con, $video_count_query);
        $video_count = $video_count_result ? mysqli_fetch_assoc($video_count_result)['count'] : 0;
        
        // Level colors
        $level_colors = [
            'beginner' => '#28a745',
            'intermediate' => '#ffc107',
            'advanced' => '#dc3545'
        ];
        
        $level_emojis = [
            'beginner' => '🌱',
            'intermediate' => '🌿',
            'advanced' => '🌳'
        ];
        
        $level_color = $level_colors[$course['level']] ?? '#6c757d';
        $level_emoji = $level_emojis[$course['level']] ?? '📚';
        
        echo "<div style='background: white; border: 1px solid #ddd; border-radius: 15px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>";
        
        // Course thumbnail/emoji
        echo "<div style='width: 100%; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; margin-bottom: 1rem;'>";
        if ($course['thumbnail'] && file_exists($course['thumbnail'])) {
            echo "<img src='{$course['thumbnail']}' alt='{$course['title']}' style='width: 100%; height: 100%; object-fit: cover; border-radius: 10px;'>";
        } else {
            echo $level_emoji;
        }
        echo "</div>";
        
        // Course info
        echo "<h3 style='color: #2d5a3d; margin-bottom: 0.5rem;'>" . htmlspecialchars($course['title']) . "</h3>";
        
        // Course meta
        echo "<div style='display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;'>";
        echo "<span style='background-color: {$level_color}; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;'>";
        echo ucfirst($course['level']);
        echo "</span>";
        echo "<span style='color: #666; font-size: 0.85rem; background: #f0f0f0; padding: 0.2rem 0.4rem; border-radius: 8px;'>";
        echo "{$video_count} videos";
        echo "</span>";
        echo "</div>";
        
        // Description
        $short_desc = substr($course['description'], 0, 80) . (strlen($course['description']) > 80 ? '...' : '');
        echo "<p style='color: #666; font-size: 0.9rem; line-height: 1.4; margin-bottom: 0.5rem;'>";
        echo htmlspecialchars($short_desc);
        echo "</p>";
        
        // Status
        echo "<div style='color: #4a7c59; font-weight: 600; font-size: 0.85rem;'>✓ Available</div>";
        
        echo "</div>";
    }
    
    echo "</div>";
} else {
    echo "<div style='text-align: center; padding: 3rem; color: #666; border: 2px dashed #ddd; border-radius: 15px;'>";
    echo "<h3>📚 No Courses Available Yet</h3>";
    echo "<p>We're working on adding amazing yoga courses for you. Please check back soon!</p>";
    echo "</div>";
}

echo "<h2>🚀 How It Works:</h2>";
echo "<div style='background: #f0f8ff; padding: 1.5rem; border-radius: 10px; margin: 1rem 0;'>";
echo "<h3>Home Page Integration:</h3>";
echo "<ol>";
echo "<li><strong>Fetches Latest 6 Courses:</strong> FROM database ORDER BY created_at DESC</li>";
echo "<li><strong>Shows Course Info:</strong> Title, description, level, thumbnail, video count</li>";
echo "<li><strong>Level Color Coding:</strong> 🌱 Beginner (green), 🌿 Intermediate (yellow), 🌳 Advanced (red)</li>";
echo "<li><strong>Login Protection:</strong> Non-logged users see login popup when clicking courses</li>";
echo "<li><strong>Direct Navigation:</strong> Logged users go to courses.php with specific course expanded</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔗 Interactive Features:</h2>";
echo "<div style='background: #f0fff0; padding: 1.5rem; border-radius: 10px; margin: 1rem 0;'>";
echo "<h3>User Experience:</h3>";
echo "<ul>";
echo "<li><strong>Guest Users:</strong> Can see courses, get login popup when clicking</li>";
echo "<li><strong>Logged Users:</strong> Click course → go to courses.php?course=ID → auto-expand</li>";
echo "<li><strong>Dynamic Updates:</strong> Home page automatically shows new courses from admin panel</li>";
echo "<li><strong>Responsive Design:</strong> Cards adapt to screen size with grid layout</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📋 Links to Test:</h2>";
echo "<div style='display: flex; gap: 1rem; margin: 1rem 0;'>";
echo "<a href='home.php' style='background: #4a7c59; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none;' target='_blank'>View Home Page</a>";
echo "<a href='courses.php' style='background: #2c5a87; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none;' target='_blank'>View Courses Page</a>";
echo "<a href='admin-panel.php' style='background: #dc3545; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none;' target='_blank'>Admin Panel</a>";
echo "<a href='add_sample_data.php' style='background: #28a745; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none;' target='_blank'>Add Sample Data</a>";
echo "</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page Courses Integration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #2c5a87; }
        a { color: #4a7c59; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
</body>
</html>