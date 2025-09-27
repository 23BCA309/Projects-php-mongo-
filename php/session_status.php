<?php
// session_status.php - Display session status (for admin/debugging)
if (!function_exists('getSessionStatus')) {
    require_once 'session_check.php';
}

function displaySessionStatus($show_details = false) {
    $status = getSessionStatus();
    $user = getCurrentUser();
    
    if (!$status['logged_in']) {
        echo '<div class="session-status guest">';
        echo '<span class="status-indicator">🔴 Guest Session</span>';
        echo '</div>';
        return;
    }
    
    $time_remaining = $status['time_remaining'];
    $minutes_remaining = floor($time_remaining / 60);
    
    // Determine status color based on time remaining
    if ($time_remaining < 300) { // Less than 5 minutes
        $indicator_class = 'warning';
        $indicator_icon = '🟡';
    } elseif ($time_remaining < 900) { // Less than 15 minutes  
        $indicator_class = 'caution';
        $indicator_icon = '🟠';
    } else {
        $indicator_class = 'active';
        $indicator_icon = '🟢';
    }
    
    echo '<div class="session-status ' . $indicator_class . '">';
    echo '<span class="status-indicator">' . $indicator_icon . ' ' . $user['username'] . '</span>';
    
    if ($show_details) {
        echo '<div class="session-details">';
        echo '<small>Session expires in: ' . $minutes_remaining . ' min</small><br>';
        echo '<small>Role: ' . ucfirst($user['role']) . '</small>';
        echo '</div>';
    }
    
    echo '</div>';
}

function getSessionStatusCSS() {
    return '
    <style>
    .session-status {
        position: fixed;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.95);
        padding: 8px 12px;
        border-radius: 20px;
        border: 2px solid #ddd;
        font-size: 12px;
        z-index: 999;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .session-status.active {
        border-color: #4CAF50;
        background: rgba(76, 175, 80, 0.1);
    }
    
    .session-status.caution {
        border-color: #FF9800;
        background: rgba(255, 152, 0, 0.1);
    }
    
    .session-status.warning {
        border-color: #f44336;
        background: rgba(244, 67, 54, 0.1);
        animation: pulse 2s infinite;
    }
    
    .session-status.guest {
        border-color: #9E9E9E;
        background: rgba(158, 158, 158, 0.1);
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    .session-details {
        margin-top: 5px;
        padding-top: 5px;
        border-top: 1px solid #eee;
    }
    
    @media (max-width: 768px) {
        .session-status {
            top: 5px;
            right: 5px;
            font-size: 10px;
            padding: 5px 8px;
        }
    }
    </style>';
}
?>