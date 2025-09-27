<?php
// Include session check for authentication
require_once 'session_check.php';
include 'connect.php';

// Profile page requires login - redirect if not logged in
if (!checkSession()) {
    header("Location: login-registration.php?redirect=Profile.php&msg=Please+login+to+access+your+profile");
    exit();
}
$session_user = getCurrentUser();

// Fetch fresh user data from database
$user_query = mysqli_query($con, "SELECT * FROM users_tbl WHERE id = '" . $session_user['id'] . "'");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    header("Location: login-registration.php?msg=User+not+found");
    exit();
}
$user = mysqli_fetch_assoc($user_query);

// Handle profile update form submission
$message = '';
if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($con, trim($_POST['username']));
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($new_username)) {
        $message = 'Username cannot be empty';
    } else {
        // Check if username already exists (excluding current user)
        $username_check = mysqli_query($con, "SELECT id FROM users_tbl WHERE username = '$new_username' AND id != '" . $user['id'] . "'");
        if (mysqli_num_rows($username_check) > 0) {
            $message = 'Username already exists. Please choose a different one.';
        } else {
            $update_success = true;
            
            // Handle profile picture upload
            $profile_picture_update = '';
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                $file = $_FILES['profile_picture'];
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                // Validate file type
                if (!in_array($file_extension, $allowed_types)) {
                    $message = 'Error: Only image files (JPG, JPEG, PNG, GIF, WEBP) are allowed!';
                    $update_success = false;
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $message = 'Error: Profile picture must be less than 5MB!';
                    $update_success = false;
                } else {
                    // Create upload directory if it doesn't exist
                    $upload_dir = '../content/user_images/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Generate unique filename
                    $filename = 'profile_' . $user['id'] . '_' . time() . '.' . $file_extension;
                    $target_path = $upload_dir . $filename;
                    $web_path = 'content/user_images/' . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        // Delete old profile picture if it exists (construct file path from web path)
                        if (!empty($user['image']) && file_exists('../' . $user['image'])) {
                            unlink('../' . $user['image']);
                        }
                        $profile_picture_update = ", image = '$web_path'";
                    } else {
                        $message = 'Error: Failed to upload profile picture!';
                        $update_success = false;
                    }
                }
            }
            
            // Update username and possibly profile picture
            $update_query = "UPDATE users_tbl SET username = '$new_username'$profile_picture_update WHERE id = '" . $user['id'] . "'";
            if (!mysqli_query($con, $update_query)) {
                $message = 'Error updating profile: ' . mysqli_error($con);
                $update_success = false;
            }
            
            // Update password only if user wants to change it
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                // If any password field is filled, all password fields are required
                if (empty($current_password)) {
                    $message = 'Please enter your current password to change your password';
                    $update_success = false;
                } elseif (empty($new_password)) {
                    $message = 'Please enter a new password';
                    $update_success = false;
                } elseif (empty($confirm_password)) {
                    $message = 'Please confirm your new password';
                    $update_success = false;
                } else {
                // Get fresh user data for password verification
                $fresh_user_query = mysqli_query($con, "SELECT password FROM users_tbl WHERE id = '" . $user['id'] . "'");
                if (!$fresh_user_query) {
                    $message = 'Database error: ' . mysqli_error($con);
                    $update_success = false;
                } else {
                    $fresh_user = mysqli_fetch_assoc($fresh_user_query);
                    if (!$fresh_user) {
                        $message = 'User not found in database';
                        $update_success = false;
                    } else {
                        // Debug: Add temporary logging to understand the issue
                        // Note: Remove this in production
                        
                        // Verify current password
                        if (!password_verify($current_password, $fresh_user['password'])) {
                            // Add more specific error information for debugging
                            $message = 'Current password is incorrect. Please make sure you are entering the correct password.';
                            $update_success = false;
                        } elseif ($new_password !== $confirm_password) {
                            $message = 'New passwords do not match';
                            $update_success = false;
                        } elseif (strlen($new_password) < 6) {
                            $message = 'New password must be at least 6 characters long';
                            $update_success = false;
                        } else {
                            // Hash and update new password
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $password_query = "UPDATE users_tbl SET password = '$hashed_password' WHERE id = '" . $user['id'] . "'";
                            if (!mysqli_query($con, $password_query)) {
                                $message = 'Error updating password: ' . mysqli_error($con);
                                $update_success = false;
                            }
                        }
                    }
                }
                }
            }
            
            if ($update_success && empty($message)) {
                $message = 'Profile updated successfully!';
                // Refresh user data
                $user_query = mysqli_query($con, "SELECT * FROM users_tbl WHERE id = '" . $user['id'] . "'");
                if ($user_query && mysqli_num_rows($user_query) > 0) {
                    $user = mysqli_fetch_assoc($user_query);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Sunrise Yoga</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .profile-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #a8e6cf, #88d8a3);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 3rem;
        }
        .profile-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        .edit-btn {
            background: linear-gradient(135deg, #a8e6cf, #88d8a3);
            color: #2c5d3f;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(168, 230, 207, 0.4);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin: auto;
            margin-top: 50px;
            margin-bottom: 50px;
            min-height: fit-content;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #a8e6cf;
        }
        .save-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        .alert.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .password-section {
            border-top: 1px solid #e1e1e1;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }
        .password-section h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        .form-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-height: 600px) {
            .modal {
                align-items: flex-start;
            }
            .modal-content {
                margin-top: 20px;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .modal {
                padding: 10px;
            }
            .modal-content {
                padding: 1.5rem;
                margin-top: 20px;
            }
        }
        
        /* Ensure form inputs are properly styled */
        .form-group input[disabled] {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        /* Make sure the modal content is always visible */
        .modal.show {
            display: block !important;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <main class="container profile-page">
        <?php if (!empty($message)): ?>
        <div class="alert <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <section class="section">
            <div class="container">
                <header class="section__head">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>! 👋</h1>
                    <p class="muted">Manage your yoga journey and track your progress</p>
                </header>
                
                <div class="profile-card">
                    <div class="profile-image">
                        <?php if (!empty($user['image']) && file_exists('../' . $user['image'])): ?>
                            <img src="<?php echo htmlspecialchars($user['image']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <div class="profile-image-placeholder">
                                <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                    <button class="edit-btn" onclick="openEditModal()">✏️ Edit Profile</button>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span>📚</span>
                        </div>
                        <h3>5</h3>
                        <p>Courses Completed</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span>🏃‍♀️</span>
                        </div>
                        <h3>12</h3>
                        <p>Classes Attended</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span>⭐</span>
                        </div>
                        <h3>127</h3>
                        <p>Total Minutes</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <span>🔥</span>
                        </div>
                        <h3>7</h3>
                        <p>Day Streak</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                    <div class="form-note">Upload a new profile picture (JPG, PNG, GIF, WEBP - Max 5MB). Leave empty to keep current picture.</div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email_display">Email</label>
                    <input type="email" id="email_display" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <div class="form-note">Email cannot be changed</div>
                </div>
                
                <div class="password-section">
                    <h3>Change Password (Optional)</h3>
                    <div class="form-note">Leave password fields empty if you don't want to change your password</div>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="Enter your current password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password">
                    </div>
                </div>
                
                <button type="submit" name="update_profile" class="save-btn">💾 Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        }
        
        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            // Restore body scrolling
            document.body.style.overflow = 'auto';
            // Reset password fields
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        }
        
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validate username
            if (username === '') {
                alert('Username cannot be empty');
                return false;
            }
            
            // Validate password fields if any password field is filled
            if (currentPassword || newPassword || confirmPassword) {
                if (!currentPassword) {
                    alert('Please enter your current password');
                    return false;
                }
                if (!newPassword) {
                    alert('Please enter a new password');
                    return false;
                }
                if (newPassword.length < 6) {
                    alert('New password must be at least 6 characters long');
                    return false;
                }
                if (newPassword !== confirmPassword) {
                    alert('New passwords do not match');
                    return false;
                }
            }
            
            return true;
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
        
        // Add escape key to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('editModal');
                if (modal.style.display === 'block') {
                    closeEditModal();
                }
            }
        });
        
        // Auto-hide alerts after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
