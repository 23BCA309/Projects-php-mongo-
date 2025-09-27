<?php
    
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Include session check for authentication
    require_once 'session_check.php';
    // Require admin role to access admin panel
    requireRole('admin');
    $current_user = getCurrentUser();
    
    include 'connect.php';
    
    // Check if database connection is successful
    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    // Fetch users (assuming you have a users table - adjust table name as needed)
    $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
    $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
    $total_users_result = mysqli_query($con, "SELECT COUNT(*) as count FROM users_tbl");
    $total_users = $total_users_result ? mysqli_fetch_assoc($total_users_result)['count'] : 0;

    // Fetch videos from course_videos table (corrected table name)
    $videos_result = mysqli_query($con, "SELECT * FROM course_videos ORDER BY id DESC LIMIT 5");
    $videos = $videos_result ? mysqli_fetch_all($videos_result, MYSQLI_ASSOC) : [];
    $total_videos_result = mysqli_query($con, "SELECT COUNT(*) as count FROM course_videos");
    $total_videos = $total_videos_result ? mysqli_fetch_assoc($total_videos_result)['count'] : 0;

    // Check if courses table exists, if not set empty arrays
    $courses_result = @mysqli_query($con, "SELECT * FROM courses ORDER BY id DESC LIMIT 5");
    if ($courses_result) {
        $courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
        $total_courses_result = mysqli_query($con, "SELECT COUNT(*) as count FROM courses");
        $total_courses = mysqli_fetch_assoc($total_courses_result)['count'];
    } else {
        // Courses table doesn't exist
        $courses = [];
        $total_courses = 0;
    }

    // Fetch tutorial videos
    $tutorial_videos_result = @mysqli_query($con, "SELECT * FROM tutorial_videos ORDER BY id DESC LIMIT 5");
    if ($tutorial_videos_result) {
        $tutorial_videos = mysqli_fetch_all($tutorial_videos_result, MYSQLI_ASSOC);
        $total_tutorial_videos_result = mysqli_query($con, "SELECT COUNT(*) as count FROM tutorial_videos");
        $total_tutorial_videos = mysqli_fetch_assoc($total_tutorial_videos_result)['count'];
    } else {
        $tutorial_videos = [];
        $total_tutorial_videos = 0;
    }

    // Function to handle profile picture upload
    function handleProfilePictureUpload($user_id, $file_input_name) {
        if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== 0) {
            return null; // No file uploaded or upload error
        }
        
        $file = $_FILES[$file_input_name];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($file_extension, $allowed_types)) {
            return 'Error: Only image files (JPG, JPEG, PNG, GIF, WEBP) are allowed!';
        }
        
        // Validate file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return 'Error: Profile picture must be less than 5MB!';
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = '../content/user_images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $filename;
        $web_path = 'content/user_images/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return $web_path;
        } else {
            return 'Error: Failed to upload profile picture!';
        }
    }
    
    // Function to handle thumbnail upload for tutorial videos
    function handleThumbnailUpload($video_title, $file_input_name) {
        if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== 0) {
            return null; // No file uploaded or upload error
        }
        
        $file = $_FILES[$file_input_name];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($file_extension, $allowed_types)) {
            return 'Error: Only image files (JPG, JPEG, PNG, GIF, WEBP) are allowed!';
        }
        
        // Validate file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return 'Error: Thumbnail must be less than 5MB!';
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = 'content/tutorials/thumbnails/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $video_title));
        $filename = 'thumb_' . time() . '_' . $safe_title . '.' . $file_extension;
        $target_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return $target_path;
        } else {
            return 'Error: Failed to upload thumbnail!';
        }
    }
    
    // Handle form submissions
    $message = '';

    // Handle admin account creation
    if (isset($_POST['add_admin'])) {
        // Debug: Log the form submission
        error_log("Admin creation form submitted");
        error_log("POST data: " . print_r($_POST, true));
        
        $name = mysqli_real_escape_string($con, $_POST['admin_name']);
        $email = mysqli_real_escape_string($con, $_POST['admin_email']);
        $password = mysqli_real_escape_string($con, $_POST['admin_password']);
        $confirm_password = mysqli_real_escape_string($con, $_POST['admin_confirm_password']);
        $created_at = date('Y-m-d H:i:s');
        
        // Validate passwords match
        if ($password !== $confirm_password) {
            $message = "Error: Passwords do not match!";
        } elseif (strlen($password) < 8) {
            $message = "Error: Password must be at least 8 characters long!";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Check if email already exists
            $email_check = mysqli_query($con, "SELECT email FROM users_tbl WHERE email = '$email'");
            if (mysqli_num_rows($email_check) > 0) {
                $message = "Error: Email already exists!";
            } else {
                // Insert admin user with 'admin' role
                $admin_role = 'admin';
                
                // Check if image column exists
                $column_check = mysqli_query($con, "SHOW COLUMNS FROM users_tbl LIKE 'image'");
                $has_image_column = mysqli_num_rows($column_check) > 0;
                
                if ($has_image_column) {
                    $sql = "INSERT INTO users_tbl (username, email, password, role, image, created_at) 
                            VALUES ('$name', '$email', '$hashed_password', '$admin_role', NULL, '$created_at')";
                } else {
                    $sql = "INSERT INTO users_tbl (username, email, password, role, created_at) 
                            VALUES ('$name', '$email', '$hashed_password', '$admin_role', '$created_at')";
                }
                
                error_log("SQL Query: " . $sql);
                
                if (mysqli_query($con, $sql)) {
                    $admin_id = mysqli_insert_id($con);
                    
                    // Handle profile picture upload
                    $profile_picture = handleProfilePictureUpload($admin_id, 'admin_profile_picture');
                    if ($profile_picture && !strpos($profile_picture, 'Error:')) {
                        $update_sql = "UPDATE users_tbl SET image = '$profile_picture' WHERE id = '$admin_id'";
                        mysqli_query($con, $update_sql);
                        $message = "Admin account created successfully with profile picture!";
                    } elseif ($profile_picture && strpos($profile_picture, 'Error:')) {
                        $message = "Admin account created successfully but " . $profile_picture;
                    } else {
                        $message = "Admin account created successfully!";
                    }
                    
                    // Refresh the users data
                    $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
                    $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
                } else {
                    $message = "Error: " . mysqli_error($con);
                }
            }
        }
    }

    // Handle admin deletion
    if (isset($_POST['delete_admin'])) {
        $admin_id = mysqli_real_escape_string($con, $_POST['delete_admin']);
        
        // Check if this is the last admin account
        $admin_count_result = mysqli_query($con, "SELECT COUNT(*) as count FROM users_tbl WHERE role = 'admin'");
        $admin_count = mysqli_fetch_assoc($admin_count_result)['count'];
        
        if ($admin_count <= 1) {
            $message = "Error: Cannot delete the last admin account! At least one admin account must exist.";
        } else {
            $sql = "DELETE FROM users_tbl WHERE id = '$admin_id' AND role = 'admin'";
            
            if (mysqli_query($con, $sql)) {
                $message = "Admin account deleted successfully!";
                // Refresh the users data
                $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
                $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }

    // Handle user creation
    if (isset($_POST['add_user'])) {
        $name = mysqli_real_escape_string($con, $_POST['user_name']);
        $email = mysqli_real_escape_string($con, $_POST['user_email']);
        $password = mysqli_real_escape_string($con, $_POST['user_password']);
        $created_at = date('Y-m-d H:i:s');
        
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if email already exists
        $email_check = mysqli_query($con, "SELECT email FROM users_tbl WHERE email = '$email'");
        if (mysqli_num_rows($email_check) > 0) {
            $message = "Error: Email already exists!";
        } else {
            // Insert user first to get the user ID  
            $default_role = 'user';
            
            // Check if image column exists
            $column_check = mysqli_query($con, "SHOW COLUMNS FROM users_tbl LIKE 'image'");
            $has_image_column = mysqli_num_rows($column_check) > 0;
            
            if ($has_image_column) {
                $sql = "INSERT INTO users_tbl (username, email, password, role, image, created_at) 
                        VALUES ('$name', '$email', '$hashed_password', '$default_role', NULL, '$created_at')";
            } else {
                $sql = "INSERT INTO users_tbl (username, email, password, role, created_at) 
                        VALUES ('$name', '$email', '$hashed_password', '$default_role', '$created_at')";
            }
            
            if (mysqli_query($con, $sql)) {
                $user_id = mysqli_insert_id($con);
                
                // Handle profile picture upload
                $profile_picture = handleProfilePictureUpload($user_id, 'user_profile_picture');
                if ($profile_picture && !strpos($profile_picture, 'Error:')) {
                    // Update user with profile picture path
                    $update_sql = "UPDATE users_tbl SET image = '$profile_picture' WHERE id = '$user_id'";
                    mysqli_query($con, $update_sql);
                    $message = "User added successfully with profile picture!";
                } elseif ($profile_picture && strpos($profile_picture, 'Error:')) {
                    $message = "User added successfully but " . $profile_picture;
                } else {
                    $message = "User added successfully!";
                }
                
                // Refresh the users data
                $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
                $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }

    // Handle user update
    if (isset($_POST['update_user'])) {
        $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
        $name = mysqli_real_escape_string($con, $_POST['edit_user_name']);
        $email = mysqli_real_escape_string($con, $_POST['edit_user_email']);
        
        // Check if email already exists for other users
        $email_check = mysqli_query($con, "SELECT email FROM users_tbl WHERE email = '$email' AND id != '$user_id'");
        if (mysqli_num_rows($email_check) > 0) {
            $message = "Error: Email already exists for another user!";
        } else {
            // Handle profile picture upload
            $profile_picture_update = '';
            $profile_picture = handleProfilePictureUpload($user_id, 'edit_user_profile_picture');
            if ($profile_picture && !strpos($profile_picture, 'Error:')) {
                $profile_picture_update = ", image = '$profile_picture'";
            }
            
            // Update user data
            $sql = "UPDATE users_tbl SET username = '$name', email = '$email'$profile_picture_update WHERE id = '$user_id'";
            
            if (mysqli_query($con, $sql)) {
                if ($profile_picture && strpos($profile_picture, 'Error:')) {
                    $message = "User updated successfully but " . $profile_picture;
                } else {
                    $message = "User updated successfully!";
                }
                // Refresh the users data
                $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
                $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }

    // Handle user deletion
    if (isset($_POST['delete_user'])) {
        $user_id = mysqli_real_escape_string($con, $_POST['delete_user_id']);
        
        $sql = "DELETE FROM users_tbl WHERE id = '$user_id'";
        
        if (mysqli_query($con, $sql)) {
            $message = "User deleted successfully!";
            // Refresh the users data
            $users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC LIMIT 5");
            $users = $users_result ? mysqli_fetch_all($users_result, MYSQLI_ASSOC) : [];
        } else {
            $message = "Error: " . mysqli_error($con);
        }
    }

    // Handle video upload
    if (isset($_POST['add_video'])) {
        $title = mysqli_real_escape_string($con, $_POST['video_title']);
        $description = mysqli_real_escape_string($con, $_POST['video_description']);
        $course_id = mysqli_real_escape_string($con, $_POST['video_course_id'] ?? '1');
        $duration = mysqli_real_escape_string($con, $_POST['video_duration']);
        $upload_date = date('Y-m-d H:i:s');

        // Get course title from courses table
        $course_query = mysqli_query($con, "SELECT title FROM courses WHERE id = '$course_id'");
        $course_data = mysqli_fetch_assoc($course_query);
        
        if (!$course_data) {
            $message = "Error: Course not found!";
        } else {
            // Handle file upload
            $video_url = '';
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
                // Create folder from course title
                $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $course_data['title']));
                $folder_name = preg_replace('/_{2,}/', '_', $folder_name);
                $upload_dir = "content/courses/" . $folder_name . "/";
                
                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Create unique filename with timestamp
                $file_extension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
                $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $title));
                $file_name = time() . '_' . $safe_title . '.' . $file_extension;
                $target_file = $upload_dir . $file_name;

                // Validate file type
                $allowed_types = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
                if (!in_array(strtolower($file_extension), $allowed_types)) {
                    $message = "Error: Only video files (mp4, avi, mov, wmv, flv) are allowed!";
                } elseif ($_FILES['video_file']['size'] > 100 * 1024 * 1024) { // 100MB limit
                    $message = "Error: File size too large! Maximum 100MB allowed.";
                } else {
                    if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
                        $video_url = $target_file;
                        
                        // Insert into course_videos table
                        $sql = "INSERT INTO course_videos (course_id, title, description, video_url, duration, created_at)
                                VALUES ('$course_id', '$title', '$description', '$video_url', '$duration', '$upload_date')";

                        if (mysqli_query($con, $sql)) {
                            $message = "Video uploaded successfully to: " . $upload_dir;
                            // Refresh the videos data
                            $videos_result = mysqli_query($con, "SELECT * FROM course_videos ORDER BY id DESC LIMIT 5");
                            $videos = $videos_result ? mysqli_fetch_all($videos_result, MYSQLI_ASSOC) : [];
                        } else {
                            $message = "Error saving to database: " . mysqli_error($con);
                        }
                    } else {
                        $message = "Error: Failed to upload video file!";
                    }
                }
            } else {
                $message = "Error: No video file selected or upload error!";
            }
        }
    }

    // Handle course creation
    if (isset($_POST['add_course'])) {
        // Check if courses table exists first
        $table_check = @mysqli_query($con, "SELECT 1 FROM courses LIMIT 1");
        if (!$table_check) {
            $message = "Error: Courses table doesn't exist. Please create it first.";
        } else {
            $title = mysqli_real_escape_string($con, $_POST['course_name']);
            $description = mysqli_real_escape_string($con, $_POST['course_description']);
            $level = mysqli_real_escape_string($con, $_POST['course_level'] ?? 'beginner');
            $created_at = date('Y-m-d H:i:s');

            // Create course folder name (replace spaces with underscores, remove special chars)
            $folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $title));
            $folder_name = preg_replace('/_{2,}/', '_', $folder_name); // Remove multiple underscores
            $course_folder_path = "content/courses/" . $folder_name;
            
            // Create the course folder
            if (!file_exists($course_folder_path)) {
                if (mkdir($course_folder_path, 0777, true)) {
                    $folder_created = true;
                } else {
                    $message = "Error: Failed to create course folder.";
                    $folder_created = false;
                }
            } else {
                $folder_created = true; // Folder already exists
            }
            
            if ($folder_created) {
                // Handle thumbnail upload
                $thumbnail_path = '';
                if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] == 0) {
                    // Validate file type
                    $file_extension = strtolower(pathinfo($_FILES['course_thumbnail']['name'], PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_extension, $allowed_types)) {
                        if ($_FILES['course_thumbnail']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                            $thumbnail_filename = 'thumbnail_' . time() . '.' . $file_extension;
                            $thumbnail_path = $course_folder_path . '/' . $thumbnail_filename;
                            
                            if (move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $thumbnail_path)) {
                                // Thumbnail uploaded successfully
                            } else {
                                $message = "Error: Failed to upload thumbnail.";
                                $thumbnail_path = '';
                            }
                        } else {
                            $message = "Error: Thumbnail file size too large! Maximum 5MB allowed.";
                        }
                    } else {
                        $message = "Error: Only image files (jpg, jpeg, png, gif, webp) are allowed for thumbnail!";
                    }
                }
                
                // Insert course data (only use columns that exist in your table)
                $sql = "INSERT INTO courses (title, description, level, thumbnail, created_at)
                        VALUES ('$title', '$description', '$level', '$thumbnail_path', '$created_at')";

                if (mysqli_query($con, $sql)) {
                    $message = "Course added successfully! Course folder created at: " . $course_folder_path;
                    if (!empty($thumbnail_path)) {
                        $message .= " | Thumbnail uploaded: " . basename($thumbnail_path);
                    }
                    // Refresh the courses data
                    $courses_result = mysqli_query($con, "SELECT * FROM courses ORDER BY id DESC LIMIT 5");
                    $courses = $courses_result ? mysqli_fetch_all($courses_result, MYSQLI_ASSOC) : [];
                } else {
                    $message = "Error: " . mysqli_error($con);
                }
            }
        }
    }

    // Handle tutorial video creation
    if (isset($_POST['add_tutorial_video'])) {
        $title = mysqli_real_escape_string($con, $_POST['tutorial_video_title']);
        $description = mysqli_real_escape_string($con, $_POST['tutorial_video_description']);
        $duration = mysqli_real_escape_string($con, $_POST['tutorial_video_duration']);
        $created_at = date('Y-m-d H:i:s');

        // Convert duration from "X min" format to TIME format (HH:MM:SS)
        $duration_parts = explode(' ', $duration);
        $minutes = intval($duration_parts[0]);
        $formatted_duration = sprintf("%02d:%02d:%02d", 0, $minutes, 0);

        // Handle video file upload
        $video_url = '';
        if (isset($_FILES['tutorial_video_file']) && $_FILES['tutorial_video_file']['error'] == 0) {
            $upload_dir = "content/tutorials/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Create unique filename with timestamp
            $file_extension = pathinfo($_FILES['tutorial_video_file']['name'], PATHINFO_EXTENSION);
            $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $title));
            $file_name = time() . '_' . $safe_title . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            // Validate file type
            $allowed_types = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                $message = "Error: Only video files (mp4, avi, mov, wmv, flv, webm) are allowed!";
            } elseif ($_FILES['tutorial_video_file']['size'] > 200 * 1024 * 1024) { // 200MB limit
                $message = "Error: File size too large! Maximum 200MB allowed.";
            } else {
                if (move_uploaded_file($_FILES['tutorial_video_file']['tmp_name'], $target_file)) {
                    $video_url = $target_file;
                } else {
                    $message = "Error: Failed to upload video file!";
                }
            }
        }

        // Handle thumbnail upload
        $thumbnail_url = '';
        if (!isset($message) || strpos($message, 'Error:') === false) {
            $thumbnail_result = handleThumbnailUpload($title, 'tutorial_video_thumbnail');
            if ($thumbnail_result && !strpos($thumbnail_result, 'Error:')) {
                $thumbnail_url = $thumbnail_result;
            } elseif ($thumbnail_result && strpos($thumbnail_result, 'Error:')) {
                $message = $thumbnail_result; // Set thumbnail error message
            }
        }

        // Insert into tutorial_videos table (only if no upload error)
        if (!isset($message) || strpos($message, 'Error:') === false) {
            $sql = "INSERT INTO tutorial_videos (title, description, url, thumbnail, duration, created_at) 
                    VALUES ('$title', '$description', '$video_url', '$thumbnail_url', '$formatted_duration', '$created_at')";

            if (mysqli_query($con, $sql)) {
                $success_parts = [];
                if (!empty($video_url)) {
                    $success_parts[] = "video file uploaded";
                }
                if (!empty($thumbnail_url)) {
                    $success_parts[] = "thumbnail uploaded";
                }
                
                if (!empty($success_parts)) {
                    $message = "Tutorial video added successfully with " . implode(' and ', $success_parts) . "!";
                } else {
                    $message = "Tutorial video added successfully (no files uploaded)!";
                }
                // Refresh the tutorial videos data
                $tutorial_videos_result = mysqli_query($con, "SELECT * FROM tutorial_videos ORDER BY id DESC LIMIT 5");
                $tutorial_videos = $tutorial_videos_result ? mysqli_fetch_all($tutorial_videos_result, MYSQLI_ASSOC) : [];
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }

    // Handle tutorial video update
    if (isset($_POST['update_tutorial_video'])) {
        $video_id = mysqli_real_escape_string($con, $_POST['tutorial_video_id']);
        $title = mysqli_real_escape_string($con, $_POST['edit_tutorial_video_title']);
        $description = mysqli_real_escape_string($con, $_POST['edit_tutorial_video_description']);
        $duration = mysqli_real_escape_string($con, $_POST['edit_tutorial_video_duration']);

        // Convert duration from "X min" format to TIME format (HH:MM:SS)
        $duration_parts = explode(' ', $duration);
        $minutes = intval($duration_parts[0]);
        $formatted_duration = sprintf("%02d:%02d:%02d", 0, $minutes, 0);

        // Handle video file upload for update
        $video_url_update = '';
        if (isset($_FILES['edit_tutorial_video_file']) && $_FILES['edit_tutorial_video_file']['error'] == 0) {
            $upload_dir = "content/tutorials/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Create unique filename with timestamp
            $file_extension = pathinfo($_FILES['edit_tutorial_video_file']['name'], PATHINFO_EXTENSION);
            $safe_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', str_replace(' ', '_', $title));
            $file_name = time() . '_' . $safe_title . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            // Validate file type
            $allowed_types = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
            if (!in_array(strtolower($file_extension), $allowed_types)) {
                $message = "Error: Only video files (mp4, avi, mov, wmv, flv, webm) are allowed!";
            } elseif ($_FILES['edit_tutorial_video_file']['size'] > 200 * 1024 * 1024) { // 200MB limit
                $message = "Error: File size too large! Maximum 200MB allowed.";
            } else {
                if (move_uploaded_file($_FILES['edit_tutorial_video_file']['tmp_name'], $target_file)) {
                    $video_url_update = ", url = '$target_file'";
                } else {
                    $message = "Error: Failed to upload video file!";
                }
            }
        }

        // Handle thumbnail upload for update
        $thumbnail_url_update = '';
        if (!isset($message) || strpos($message, 'Error:') === false) {
            $thumbnail_result = handleThumbnailUpload($title, 'edit_tutorial_video_thumbnail');
            if ($thumbnail_result && !strpos($thumbnail_result, 'Error:')) {
                $thumbnail_url_update = ", thumbnail = '$thumbnail_result'";
            } elseif ($thumbnail_result && strpos($thumbnail_result, 'Error:')) {
                $message = $thumbnail_result; // Set thumbnail error message
            }
        }

        // Update tutorial video (only if no upload error)
        if (!isset($message) || strpos($message, 'Error:') === false) {
            $sql = "UPDATE tutorial_videos SET title = '$title', description = '$description', duration = '$formatted_duration'$video_url_update$thumbnail_url_update WHERE id = '$video_id'";

            if (mysqli_query($con, $sql)) {
                $update_parts = [];
                if (!empty($video_url_update)) {
                    $update_parts[] = "video file";
                }
                if (!empty($thumbnail_url_update)) {
                    $update_parts[] = "thumbnail";
                }
                
                if (!empty($update_parts)) {
                    $message = "Tutorial video updated successfully with new " . implode(' and ', $update_parts) . " uploaded!";
                } else {
                    $message = "Tutorial video updated successfully!";
                }
                // Refresh the tutorial videos data
                $tutorial_videos_result = mysqli_query($con, "SELECT * FROM tutorial_videos ORDER BY id DESC LIMIT 5");
                $tutorial_videos = $tutorial_videos_result ? mysqli_fetch_all($tutorial_videos_result, MYSQLI_ASSOC) : [];
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }

    // Handle tutorial video deletion
    if (isset($_POST['delete_tutorial_video'])) {
        $video_id = mysqli_real_escape_string($con, $_POST['delete_tutorial_video_id']);
        
        $sql = "DELETE FROM tutorial_videos WHERE id = '$video_id'";
        
        if (mysqli_query($con, $sql)) {
            $message = "Tutorial video deleted successfully!";
            // Refresh the tutorial videos data
            $tutorial_videos_result = mysqli_query($con, "SELECT * FROM tutorial_videos ORDER BY id DESC LIMIT 5");
            $tutorial_videos = $tutorial_videos_result ? mysqli_fetch_all($tutorial_videos_result, MYSQLI_ASSOC) : [];
        } else {
            $message = "Error: " . mysqli_error($con);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sunrise Yoga | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Open+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-panel.css?v=20250927174706">
    <style>
/* Critical CSS Backup */
body { margin: 0; font-family: 'Open Sans', sans-serif; background: #f5f9f7; }
.sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; background: linear-gradient(135deg, #2a5948 0%, #4a7c59 100%); color: white; }
.logo { padding: 2rem; font-size: 1.5rem; font-weight: 600; color: white; }
.main-content { margin-left: 280px; padding: 2rem; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
.stat-card { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.nav-link { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; padding: 1rem 2rem; }
.nav-link.active { background: rgba(255,255,255,0.15); color: white; }
</style>
<style>
        .profile-pic-table {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e1e5e9;
        }
        .profile-pic-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a8d5ba, #88d8a3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info-cell {
            display: flex;
            flex-direction: column;
        }
        .user-name {
            font-weight: 600;
        }
        .user-role {
            font-size: 0.8rem;
            color: #666;
        }

        /* Modal fixes for better scrolling and button visibility */
        .modal {
            display: none !important;    /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .modal.show {
            display: block !important;   /* Show when active */
        }
        .modal .modal-content {
            background: #fff;
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .modal form {
            max-height: none;
        }
        @media (max-height: 600px) {
            .modal .modal-content {
                margin: 20px auto;
                max-height: calc(100vh - 40px);
            }
        }
        @media (max-width: 480px) {
            .modal {
                padding: 10px;
            }
            .modal .modal-content {
                margin: 20px auto;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php
    
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Include session status for admin debugging
    include 'session_status.php';
    echo getSessionStatusCSS();
    ?>
    <!-- Toggle Button for Mobile -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sticky Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="logo">Sunrise Yoga Admin</div>
        
        <ul class="admin-menu">
            <li><a href="#" class="nav-link active" data-page="dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#" class="nav-link" data-page="users"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#" class="nav-link" data-page="admins"><i class="fas fa-user-shield"></i> Admins</a></li>
            <li><a href="#" class="nav-link" data-page="courses"><i class="fas fa-book-open"></i> Courses</a></li>
            <li><a href="#" class="nav-link" data-page="videos"><i class="fas fa-video"></i> Course Videos</a></li>
            <li><a href="#" class="nav-link" data-page="tutorial-videos"><i class="fas fa-play-circle"></i> Tutorial Videos</a></li>
            <li><a href="#" class="nav-link" data-page="settings"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Page -->
        <div class="page-content active" id="dashboard">
            <div class="header">
                <h1 class="page-title">Admin Dashboard</h1>
                <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    displaySessionStatus(true); // Show detailed session info for admins ?>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($current_user['username']); ?></span>
                </div>
            </div>

            <!-- Success/Error Message -->
            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($message)): ?>
            <div class="alert <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>" id="messageAlert">
                <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $message; ?>
                <button class="close-alert" onclick="closeAlert()">&times;</button>
            </div>
            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
            
            <!-- Stats Overview -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon videos-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $total_videos; ?></h3>
                        <p>Course Videos</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon courses-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $total_courses; ?></h3>
                        <p>Total Courses</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $total_tutorial_videos; ?></h3>
                        <p>Tutorial Videos</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Recent Users</h2>
                    <button class="btn" id="addUserBtn"><i class="fas fa-plus"></i> Add New User</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (empty($users)): ?>
                        <tr>
                            <td colspan="5">No users found. Please check if the users_tbl table exists and contains data.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($users as $user): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['id'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="user-cell">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($user['image']) && file_exists('../' . $user['image'])): ?>
                                            <img src="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['image']); ?>" alt="Profile" class="profile-pic-table">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                                            <div class="profile-pic-placeholder">
                                                <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                            </div>
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                                        <div class="user-info-cell">
                                            <span class="user-name"><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['username'] ?? 'N/A'); ?></span>
                                            <span class="user-role"><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo ucfirst($user['role'] ?? 'user'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(date('M d, Y', strtotime($user['created_at'] ?? 'now'))); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editUser(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $user['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['username'] ?? ''); ?>', '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['email'] ?? ''); ?>')">Edit</button>
                                    <button class="delete-btn" onclick="deleteUser(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $user['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['username'] ?? ''); ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Course Videos -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Course Videos</h2>
                    <button class="btn" id="addVideoBtn"><i class="fas fa-plus"></i> Add New Video</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Course ID</th>
                            <th>Duration</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (empty($videos)): ?>
                        <tr>
                            <td colspan="6">No videos found.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($videos as $video): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['title'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['course_id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['duration'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['created_at'] ?? 'N/A'); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Courses -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Courses</h2>
                    <button class="btn" id="addCourseBtn"><i class="fas fa-plus"></i> Add New Course</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (empty($courses)): ?>
                        <tr>
                            <td colspan="5">No courses found.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($courses as $course): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['title'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(substr($course['description'] ?? 'N/A', 0, 50)) . '...'; ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['created_at'] ?? 'N/A'); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Users Page -->
        <div class="page-content" id="users">
            <div class="header">
                <h1 class="page-title">User Management</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">All Users</h2>
                    <button class="btn" id="addUserBtnPage"><i class="fas fa-plus"></i> Add New User</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch all users for the users page
                        $all_users_result = mysqli_query($con, "SELECT * FROM users_tbl ORDER BY id DESC");
                        $all_users = $all_users_result ? mysqli_fetch_all($all_users_result, MYSQLI_ASSOC) : [];
                        
                        if (empty($all_users)): ?>
                        <tr>
                            <td colspan="5">No users found. Please check if the users_tbl table exists and contains data.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($all_users as $user): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['id'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="user-cell">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($user['image']) && file_exists('../' . $user['image'])): ?>
                                            <img src="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['image']); ?>" alt="Profile" class="profile-pic-table">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                                            <div class="profile-pic-placeholder">
                                                <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                                            </div>
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                                        <div class="user-info-cell">
                                            <span class="user-name"><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['username'] ?? 'N/A'); ?></span>
                                            <span class="user-role"><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo ucfirst($user['role'] ?? 'user'); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(date('M d, Y', strtotime($user['created_at'] ?? 'now'))); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editUser(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $user['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['username'] ?? ''); ?>', '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['email'] ?? ''); ?>')">Edit</button>
                                    <button class="delete-btn" onclick="deleteUser(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $user['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($user['username'] ?? ''); ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Admins Page -->
        <div class="page-content" id="admins">
            <div class="header">
                <h1 class="page-title">Admin Management</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Admin Accounts</h2>
                    <button class="btn" id="addAdminBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><i class="fas fa-user-shield"></i> Create New Admin</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Admin</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch only admin users
                        $admin_users_result = mysqli_query($con, "SELECT * FROM users_tbl WHERE role = 'admin' ORDER BY id DESC");
                        $admin_users = $admin_users_result ? mysqli_fetch_all($admin_users_result, MYSQLI_ASSOC) : [];
                        
                        if (empty($admin_users)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No admin accounts found. Create the first admin account!</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($admin_users as $admin): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($admin['id']); ?></td>
                                <td>
                                    <div class="user-cell">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($admin['image']) && file_exists('../' . $admin['image'])): ?>
                                            <img src="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($admin['image']); ?>" alt="Profile" class="profile-pic-table">
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                                            <div class="profile-pic-placeholder" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                                <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo strtoupper(substr($admin['username'] ?? 'A', 0, 1)); ?>
                                            </div>
                                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                                        <div class="user-info-cell">
                                            <span class="user-name"><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($admin['username'] ?? 'N/A'); ?></span>
                                            <span class="user-role" style="color: #667eea; font-weight: bold;">Admin</span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($admin['email'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(date('M d, Y', strtotime($admin['created_at'] ?? 'now'))); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editAdmin(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $admin['id']; ?>)">Edit</button>
                                    <button class="delete-btn" onclick="deleteAdmin(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $admin['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($admin['username'] ?? ''); ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Course Videos Page -->
        <div class="page-content" id="videos">
            <div class="header">
                <h1 class="page-title">Course Videos</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">All Course Videos</h2>
                    <button class="btn" id="addVideoBtnPage"><i class="fas fa-plus"></i> Add New Video</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Course ID</th>
                            <th>Duration</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch all videos for the videos page
                        $all_videos_result = mysqli_query($con, "SELECT * FROM course_videos ORDER BY id DESC");
                        $all_videos = $all_videos_result ? mysqli_fetch_all($all_videos_result, MYSQLI_ASSOC) : [];
                        
                        if (empty($all_videos)): ?>
                        <tr>
                            <td colspan="6">No videos found.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($all_videos as $video): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['title'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['course_id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['duration'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['created_at'] ?? 'N/A'); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Courses Page -->
        <div class="page-content" id="courses">
            <div class="header">
                <h1 class="page-title">Course Management</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">All Courses</h2>
                    <button class="btn" id="addCourseBtnPage"><i class="fas fa-plus"></i> Add New Course</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Title</th>
                            <th>Description</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch all courses for the courses page
                        $all_courses_result = mysqli_query($con, "SELECT * FROM courses ORDER BY id DESC");
                        $all_courses = $all_courses_result ? mysqli_fetch_all($all_courses_result, MYSQLI_ASSOC) : [];
                        
                        if (empty($all_courses)): ?>
                        <tr>
                            <td colspan="5">No courses found.</td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($all_courses as $course): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['title'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(substr($course['description'] ?? 'N/A', 0, 100)) . '...'; ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($course['created_at'] ?? 'N/A'); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn">Edit</button>
                                    <button class="delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tutorial Videos Page -->
        <div class="page-content" id="tutorial-videos">
            <div class="header">
                <h1 class="page-title">Tutorial Videos Management</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">All Tutorial Videos</h2>
                    <button class="btn" id="addTutorialVideoBtnPage" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);"><i class="fas fa-plus"></i> Add New Tutorial Video</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Thumbnail</th>
                            <th>Description</th>
                            <th>Video URL</th>
                            <th>Duration</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch all tutorial videos for the page
                        $all_tutorial_videos_result = @mysqli_query($con, "SELECT * FROM tutorial_videos ORDER BY id DESC");
                        $all_tutorial_videos = $all_tutorial_videos_result ? mysqli_fetch_all($all_tutorial_videos_result, MYSQLI_ASSOC) : [];
                        
                        if (empty($all_tutorial_videos)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #999;">
                                <i class="fas fa-video" style="font-size: 48px; color: #ddd; margin-bottom: 1rem;"></i><br>
                                No tutorial videos found. Add your first tutorial video!
                            </td>
                        </tr>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    foreach ($all_tutorial_videos as $video): ?>
                            <tr>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['id'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['title'] ?? 'N/A'); ?></td>
                                <td style="text-align: center;">
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($video['thumbnail']) && file_exists($video['thumbnail'])): ?>
                                        <img src="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['thumbnail']); ?>" alt="Thumbnail" style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px; border: 2px solid #e1e5e9;">
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                                        <div style="width: 60px; height: 40px; background: linear-gradient(135deg, #ff6b6b, #ee5a24); border-radius: 5px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; margin: 0 auto;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                                </td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(substr($video['description'] ?? 'N/A', 0, 40)) . (strlen($video['description']) > 40 ? '...' : ''); ?></td>
                                <td>
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    if (!empty($video['url'])): ?>
                                        <a href="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['url']); ?>" target="_blank" style="color: #007bff; text-decoration: none;" title="<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['url']); ?>">
                                            <i class="fas fa-play-circle"></i> <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo substr(basename($video['url']), 0, 15) . '...'; ?>
                                        </a>
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    else: ?>
                                        <span style="color: #999; font-style: italic;">No video</span>
                                    <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                                </td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars($video['duration'] ?? 'N/A'); ?></td>
                                <td><?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo htmlspecialchars(date('M d, Y', strtotime($video['created_at'] ?? 'now'))); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="editTutorialVideo(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $video['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($video['title'] ?? ''); ?>', '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($video['description'] ?? ''); ?>', '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($video['duration'] ?? ''); ?>')">Edit</button>
                                    <button class="delete-btn" onclick="deleteTutorialVideo(<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo $video['id']; ?>, '<?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    echo addslashes($video['title'] ?? ''); ?>')">Delete</button>
                                </td>
                            </tr>
                            <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endforeach; ?>
                        <?php 
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Settings Page -->
        <div class="page-content" id="settings">
            <div class="header">
                <h1 class="page-title">Settings</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">General Settings</h2>
                </div>
                
                <form>
                    <div class="form-group">
                        <label for="siteName">Site Name</label>
                        <input type="text" id="siteName" value="SunRise Yoga">
                    </div>
                    
                    <div class="form-group">
                        <label for="siteDescription">Site Description</label>
                        <textarea id="siteDescription" rows="3">Quick morning energizers, cozy stretch breaks, and calming breathGÇödesigned to make you smile.</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="adminEmail">Admin Email</label>
                        <input type="email" id="adminEmail" value="admin@SunRiseyoga.com">
                    </div>
                    
                    <button type="submit" class="btn">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New User</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <!-- In the Add User Modal -->
                <div class="form-group">
                    <label for="userName">Username</label>  <!-- Changed from "Full Name" -->
                    <input type="text" id="userName" name="user_name" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="userEmail">Email Address</label>
                    <input type="email" id="userEmail" name="user_email" placeholder="Enter email address" required>
                </div>
                <div class="form-group">
                    <label for="userPassword">Password</label>
                    <input type="password" id="userPassword" name="user_password" placeholder="Enter password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="userProfilePicture">Profile Picture</label>
                    <input type="file" id="userProfilePicture" name="user_profile_picture" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a profile picture (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                </div>
                <button type="submit" name="add_user" class="btn" style="width: 100%; margin-top: 15px;">Add User</button>
            </form>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal" id="editUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit User</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label for="editUserName">Username</label>
                    <input type="text" id="editUserName" name="edit_user_name" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                    <label for="editUserEmail">Email Address</label>
                    <input type="email" id="editUserEmail" name="edit_user_email" placeholder="Enter email address" required>
                </div>
                <div class="form-group">
                    <label for="editUserProfilePicture">Update Profile Picture</label>
                    <input type="file" id="editUserProfilePicture" name="edit_user_profile_picture" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a new profile picture to replace the current one (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                </div>
                <button type="submit" name="update_user" class="btn" style="width: 100%; margin-top: 15px;">Update User</button>
            </form>
        </div>
    </div>
    
    <!-- Delete User Modal -->
    <div class="modal" id="deleteUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Delete User</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                <p style="color: #e74c3c;">This action cannot be undone.</p>
            </div>
            <form method="POST">
                <input type="hidden" id="deleteUserId" name="delete_user_id">
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn" onclick="closeDeleteModal()" style="background-color: #6c757d; flex: 1;">Cancel</button>
                    <button type="submit" name="delete_user" class="btn" style="background-color: #e74c3c; flex: 1;">Delete User</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Video Modal -->
    <div class="modal" id="videoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Course Video</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="videoTitle">Video Title</label>
                    <input type="text" id="videoTitle" name="video_title" placeholder="Enter video title" required>
                </div>
                <div class="form-group">
                    <label for="videoDescription">Description</label>
                    <textarea id="videoDescription" name="video_description" rows="3" placeholder="Enter video description" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="videoCourse">Select Course</label>
                        <select id="videoCourse" name="video_course_id" required>
                            <option value="">Select course</option>
                            <?php 
                            
    // Turn off error reporting for clean display
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Fetch all courses for the dropdown
                            $all_courses_result = @mysqli_query($con, "SELECT id, title FROM courses ORDER BY title");
                            if ($all_courses_result) {
                                while ($course_option = mysqli_fetch_assoc($all_courses_result)) {
                                    echo '<option value="' . $course_option['id'] . '">' . htmlspecialchars($course_option['title']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="videoDuration">Duration (minutes)</label>
                        <input type="text" id="videoDuration" name="video_duration" placeholder="e.g., 15 min" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="videoFile">Upload Video</label>
                    <input type="file" id="videoFile" name="video_file" accept="video/*">
                </div>
                <button type="submit" name="add_video" class="btn" style="width: 100%; margin-top: 15px;">Save Video</button>
            </form>
        </div>
    </div>
    
    <!-- Add Course Modal -->
    <div class="modal" id="courseModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Course</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="courseName">Course Name</label>
                    <input type="text" id="courseName" name="course_name" placeholder="Enter course name" required>
                </div>
                <div class="form-group">
                    <label for="courseDescription">Description</label>
                    <textarea id="courseDescription" name="course_description" rows="3" placeholder="Enter course description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="courseLevel">Difficulty Level</label>
                    <select id="courseLevel" name="course_level" required>
                        <option value="">Select level</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="courseThumbnail">Course Thumbnail</label>
                    <input type="file" id="courseThumbnail" name="course_thumbnail" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a thumbnail image for the course (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                </div>
                <button type="submit" name="add_course" class="btn" style="width: 100%; margin-top: 15px;">Save Course</button>
            </form>
        </div>
    </div>
    
    <!-- Add Admin Modal -->
    <div class="modal" id="adminModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create New Admin Account</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="adminName">Admin Name</label>
                    <input type="text" id="adminName" name="admin_name" placeholder="Enter admin name" required>
                </div>
                <div class="form-group">
                    <label for="adminEmail">Admin Email</label>
                    <input type="email" id="adminEmail" name="admin_email" placeholder="Enter admin email" required>
                </div>
                <div class="form-group">
                    <label for="adminPassword">Password</label>
                    <input type="password" id="adminPassword" name="admin_password" placeholder="Enter password (min 8 characters)" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="adminConfirmPassword">Confirm Password</label>
                    <input type="password" id="adminConfirmPassword" name="admin_confirm_password" placeholder="Confirm password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="adminProfilePicture">Profile Picture</label>
                    <input type="file" id="adminProfilePicture" name="admin_profile_picture" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a profile picture (JPG, PNG, GIF, WEBP - Max 5MB)</small>
                </div>
                <div class="form-group">
                    <div style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white; margin-bottom: 15px;">
                        <i class="fas fa-shield-alt"></i> <strong>Admin Account</strong><br>
                        <small>This account will have full administrative privileges</small>
                    </div>
                </div>
                <button type="submit" name="add_admin" class="btn" style="width: 100%; margin-top: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;" onclick="return validateAdminForm()">
                    <i class="fas fa-user-shield"></i> Create Admin Account
                </button>
            </form>
            <script>
                function validateAdminForm() {
                    const password = document.getElementById('adminPassword').value;
                    const confirmPassword = document.getElementById('adminConfirmPassword').value;
                    
                    if (password !== confirmPassword) {
                        alert('Passwords do not match!');
                        return false;
                    }
                    if (password.length < 8) {
                        alert('Password must be at least 8 characters long!');
                        return false;
                    }
                    return true;
                }
            </script>
        </div>
    </div>
    
    <!-- Add Tutorial Video Modal -->
    <div class="modal" id="tutorialVideoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Tutorial Video</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tutorialVideoTitle">Video Title</label>
                    <input type="text" id="tutorialVideoTitle" name="tutorial_video_title" placeholder="Enter tutorial video title" required>
                </div>
                <div class="form-group">
                    <label for="tutorialVideoDescription">Description</label>
                    <textarea id="tutorialVideoDescription" name="tutorial_video_description" rows="3" placeholder="Enter tutorial video description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="tutorialVideoDuration">Duration</label>
                    <input type="text" id="tutorialVideoDuration" name="tutorial_video_duration" placeholder="e.g., 15 min" required>
                    <small style="color: #666; font-size: 12px;">Enter duration in minutes (e.g., "15 min")</small>
                </div>
                <div class="form-group">
                    <label for="tutorialVideoFile">Upload Video File</label>
                    <input type="file" id="tutorialVideoFile" name="tutorial_video_file" accept="video/*">
                    <small style="color: #666; font-size: 12px;">Upload a video file (MP4, AVI, MOV, WMV, FLV, WEBM - Max 200MB). Video will be stored in: C:\xampp\htdocs\work\project\Projects-php-mongo-\content\tutorials</small>
                </div>
                <div class="form-group">
                    <label for="tutorialVideoThumbnail">Upload Thumbnail Image</label>
                    <input type="file" id="tutorialVideoThumbnail" name="tutorial_video_thumbnail" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a thumbnail image (JPG, PNG, GIF, WEBP - Max 5MB). Thumbnails will be stored in: C:\xampp\htdocs\work\project\Projects-php-mongo-\content\tutorials\thumbnails</small>
                </div>
                <button type="submit" name="add_tutorial_video" class="btn" style="width: 100%; margin-top: 15px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); border: none;">
                    <i class="fas fa-plus"></i> Add Tutorial Video
                </button>
            </form>
        </div>
    </div>
    
    <!-- Edit Tutorial Video Modal -->
    <div class="modal" id="editTutorialVideoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Tutorial Video</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editTutorialVideoId" name="tutorial_video_id">
                <div class="form-group">
                    <label for="editTutorialVideoTitle">Video Title</label>
                    <input type="text" id="editTutorialVideoTitle" name="edit_tutorial_video_title" placeholder="Enter tutorial video title" required>
                </div>
                <div class="form-group">
                    <label for="editTutorialVideoDescription">Description</label>
                    <textarea id="editTutorialVideoDescription" name="edit_tutorial_video_description" rows="3" placeholder="Enter tutorial video description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editTutorialVideoDuration">Duration</label>
                    <input type="text" id="editTutorialVideoDuration" name="edit_tutorial_video_duration" placeholder="e.g., 15 min" required>
                    <small style="color: #666; font-size: 12px;">Enter duration in minutes (e.g., "15 min")</small>
                </div>
                <div class="form-group">
                    <label for="editTutorialVideoFile">Update Video File</label>
                    <input type="file" id="editTutorialVideoFile" name="edit_tutorial_video_file" accept="video/*">
                    <small style="color: #666; font-size: 12px;">Upload a new video file to replace the current one (MP4, AVI, MOV, WMV, FLV, WEBM - Max 200MB). Leave empty to keep existing video.</small>
                </div>
                <div class="form-group">
                    <label for="editTutorialVideoThumbnail">Update Thumbnail Image</label>
                    <input type="file" id="editTutorialVideoThumbnail" name="edit_tutorial_video_thumbnail" accept="image/*">
                    <small style="color: #666; font-size: 12px;">Upload a new thumbnail image to replace the current one (JPG, PNG, GIF, WEBP - Max 5MB). Leave empty to keep existing thumbnail.</small>
                </div>
                <button type="submit" name="update_tutorial_video" class="btn" style="width: 100%; margin-top: 15px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); border: none;">
                    <i class="fas fa-save"></i> Update Tutorial Video
                </button>
            </form>
        </div>
    </div>
    
    <!-- Delete Tutorial Video Modal -->
    <div class="modal" id="deleteTutorialVideoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Delete Tutorial Video</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteTutorialVideoName"></strong>?</p>
                <p style="color: #e74c3c;">This action cannot be undone.</p>
            </div>
            <form method="POST">
                <input type="hidden" id="deleteTutorialVideoId" name="delete_tutorial_video_id">
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn" onclick="closeDeleteTutorialVideoModal()" style="background-color: #6c757d; flex: 1;">Cancel</button>
                    <button type="submit" name="delete_tutorial_video" class="btn" style="background-color: #e74c3c; flex: 1;">Delete Video</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const videoModal = document.getElementById('videoModal');
        const courseModal = document.getElementById('courseModal');
        const adminModal = document.getElementById('adminModal');
        const userModal = document.getElementById('userModal');
        const editUserModal = document.getElementById('editUserModal');
        const deleteUserModal = document.getElementById('deleteUserModal');
        const addVideoBtn = document.getElementById('addVideoBtn');
        const addCourseBtn = document.getElementById('addCourseBtn');
        const addAdminBtn = document.getElementById('addAdminBtn');
        const addUserBtn = document.getElementById('addUserBtn');
        const addVideoBtnPage = document.getElementById('addVideoBtnPage');
        const addCourseBtnPage = document.getElementById('addCourseBtnPage');
        const addUserBtnPage = document.getElementById('addUserBtnPage');
        const tutorialVideoModal = document.getElementById('tutorialVideoModal');
        const editTutorialVideoModal = document.getElementById('editTutorialVideoModal');
        const deleteTutorialVideoModal = document.getElementById('deleteTutorialVideoModal');
        const addTutorialVideoBtnPage = document.getElementById('addTutorialVideoBtnPage');
        const closeBtns = document.querySelectorAll('.close-btn');
        const navLinks = document.querySelectorAll('.nav-link');
        const pageContents = document.querySelectorAll('.page-content');
        
        // Toggle Sidebar on Mobile
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Open Modals
        if (addVideoBtn) {
            addVideoBtn.addEventListener('click', () => {
                videoModal.classList.add('show');
            });
        }
        
        if (addCourseBtn) {
            addCourseBtn.addEventListener('click', () => {
                courseModal.classList.add('show');
            });
        }
        
        if (addAdminBtn) {
            addAdminBtn.addEventListener('click', () => {
                adminModal.classList.add('show');
            });
        }
        
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => {
                userModal.classList.add('show');
            });
        }
        
        if (addVideoBtnPage) {
            addVideoBtnPage.addEventListener('click', () => {
                videoModal.classList.add('show');
            });
        }
        
        if (addCourseBtnPage) {
            addCourseBtnPage.addEventListener('click', () => {
                courseModal.classList.add('show');
            });
        }
        
        if (addUserBtnPage) {
            addUserBtnPage.addEventListener('click', () => {
                userModal.classList.add('show');
            });
        }
        
        if (addTutorialVideoBtnPage) {
            addTutorialVideoBtnPage.addEventListener('click', () => {
                tutorialVideoModal.classList.add('show');
            });
        }
        
        // Close Modals
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                videoModal.classList.remove('show');
                courseModal.classList.remove('show');
                adminModal.classList.remove('show');
                userModal.classList.remove('show');
                editUserModal.classList.remove('show');
                deleteUserModal.classList.remove('show');
                tutorialVideoModal.classList.remove('show');
                editTutorialVideoModal.classList.remove('show');
                deleteTutorialVideoModal.classList.remove('show');
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === videoModal) {
                videoModal.classList.remove('show');
            }
            if (e.target === courseModal) {
                courseModal.classList.remove('show');
            }
            if (e.target === adminModal) {
                adminModal.classList.remove('show');
            }
            if (e.target === userModal) {
                userModal.classList.remove('show');
            }
            if (e.target === editUserModal) {
                editUserModal.classList.remove('show');
            }
            if (e.target === deleteUserModal) {
                deleteUserModal.classList.remove('show');
            }
            if (e.target === tutorialVideoModal) {
                tutorialVideoModal.classList.remove('show');
            }
            if (e.target === editTutorialVideoModal) {
                editTutorialVideoModal.classList.remove('show');
            }
            if (e.target === deleteTutorialVideoModal) {
                deleteTutorialVideoModal.classList.remove('show');
            }
        });
        
        // User Management Functions
        function editUser(id, name, email) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            editUserModal.classList.add('show');
        }
        
        function deleteUser(id, name) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            deleteUserModal.classList.add('show');
        }
        
        function closeDeleteModal() {
            deleteUserModal.classList.remove('show');
        }
        
        // Admin Management Functions
        function editAdmin(id) {
            alert('Admin edit functionality - ID: ' + id + '\nThis would open an edit modal for admin account.');
        }
        
        function deleteAdmin(id, name) {
            if (confirm('GÜán+Å WARNING: Are you sure you want to delete admin account "' + name + '"?\n\nThis action cannot be undone and will remove all admin privileges from this account.')) {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="delete_admin" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Tutorial Video Management Functions
        function editTutorialVideo(id, title, description, duration) {
            // Convert TIME format (HH:MM:SS) to minutes for display
            let durationInMinutes = duration;
            if (duration && duration.includes(':')) {
                const parts = duration.split(':');
                const minutes = parseInt(parts[1]) || 0;
                durationInMinutes = minutes + ' min';
            }
            
            document.getElementById('editTutorialVideoId').value = id;
            document.getElementById('editTutorialVideoTitle').value = title;
            document.getElementById('editTutorialVideoDescription').value = description;
            document.getElementById('editTutorialVideoDuration').value = durationInMinutes;
            editTutorialVideoModal.classList.add('show');
        }
        
        function deleteTutorialVideo(id, title) {
            document.getElementById('deleteTutorialVideoId').value = id;
            document.getElementById('deleteTutorialVideoName').textContent = title;
            deleteTutorialVideoModal.classList.add('show');
        }
        
        function closeDeleteTutorialVideoModal() {
            deleteTutorialVideoModal.classList.remove('show');
        }
        
        // Make functions globally available
        window.editUser = editUser;
        window.deleteUser = deleteUser;
        window.closeDeleteModal = closeDeleteModal;
        window.editAdmin = editAdmin;
        window.deleteAdmin = deleteAdmin;
        window.editTutorialVideo = editTutorialVideo;
        window.deleteTutorialVideo = deleteTutorialVideo;
        window.closeDeleteTutorialVideoModal = closeDeleteTutorialVideoModal;
        
        // Navigation between pages
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all links
                navLinks.forEach(navLink => {
                    navLink.classList.remove('active');
                });
                
                // Add active class to clicked link
                link.classList.add('active');
                
                // Hide all page contents
                pageContents.forEach(page => {
                    page.classList.remove('active');
                });
                
                // Show the selected page
                const pageId = link.getAttribute('data-page');
                document.getElementById(pageId).classList.add('active');
                
                // Close sidebar on mobile after selection
                if (window.innerWidth <= 900) {
                    sidebar.classList.remove('active');
                }
            });
        });
        
        // Close alert messages
        function closeAlert() {
            const alert = document.getElementById('messageAlert');
            if (alert) {
                alert.style.display = 'none';
            }
        }
        
        // Auto-hide alert messages after 5 seconds
        const messageAlert = document.getElementById('messageAlert');
        if (messageAlert) {
            setTimeout(() => {
                messageAlert.style.display = 'none';
            }, 5000);
        }
        
        console.log('Admin panel loaded successfully');
    </script>
</body>
</html>



