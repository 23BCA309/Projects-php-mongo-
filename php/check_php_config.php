<?php
// Turn off all error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);

echo "<h2>PHP Configuration Status</h2>";
echo "<p><strong>Error Reporting:</strong> " . (error_reporting() === 0 ? 'OFF (Good)' : 'ON (Showing errors)') . "</p>";
echo "<p><strong>Display Errors:</strong> " . (ini_get('display_errors') ? 'ON (Bad)' : 'OFF (Good)') . "</p>";
echo "<p><strong>Display Startup Errors:</strong> " . (ini_get('display_startup_errors') ? 'ON' : 'OFF') . "</p>";
echo "<p><strong>Log Errors:</strong> " . (ini_get('log_errors') ? 'ON (Good)' : 'OFF') . "</p>";

echo "<hr>";
echo "<p><a href='admin-panel.php'>← Back to Admin Panel</a></p>";
?>