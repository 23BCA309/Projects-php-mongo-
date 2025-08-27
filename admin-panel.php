<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feel-Good Yoga | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Open+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-color: #2a5948;
            --secondary-color: #a8d5ba;
            --accent-color: #88c9a6;
            --text-color: #4a5b53;
            --light-bg: #f5f9f7;
            --card-bg: #ffffff;
            --border-color: #e0e9e3;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --error-color: #f44336;
        }

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sticky Sidebar Navigation */
        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background: linear-gradient(to bottom, var(--primary-color) 0%, #4a7564 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            z-index: 100;
            overflow-y: auto;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
            color: var(--secondary-color);
        }

        .admin-menu {
            list-style: none;
        }

        .admin-menu li {
            margin-bottom: 5px;
        }

        .admin-menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            transition: all 0.3s ease;
        }

        .admin-menu a:hover, .admin-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--secondary-color);
        }

        .admin-menu i {
            width: 25px;
            margin-right: 10px;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 20px 20px 20px 270px; /* Account for sidebar */
            transition: padding 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            font-size: 28px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        /* Stats Overview */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }

        .users-icon {
            background-color: rgba(168, 213, 186, 0.2);
            color: var(--primary-color);
        }

        .videos-icon {
            background-color: rgba(136, 201, 166, 0.2);
            color: var(--primary-color);
        }

        .courses-icon {
            background-color: rgba(74, 117, 100, 0.2);
            color: var(--primary-color);
        }

        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #6a8e7e;
            font-size: 14px;
        }

        /* Content Sections */
        .content-section {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            font-size: 22px;
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            background: #1d4033;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            color: var(--primary-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8faf8;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .edit-btn {
            background: var(--accent-color);
            color: var(--primary-color);
        }

        .delete-btn {
            background: var(--error-color);
            color: white;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #f9fbf8;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(136, 201, 166, 0.2);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
            padding: 25px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            font-size: 22px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #6a8e7e;
        }

        /* Page Content */
        .page-content {
            display: none;
        }
        
        .page-content.active {
            display: block;
        }

        /* Toggle Button for Sidebar */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px;
            cursor: pointer;
            z-index: 101;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                width: 220px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Toggle Button for Mobile -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sticky Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="logo">Sunrise Yoga</div>
        
        <ul class="admin-menu">
            <li><a href="#" class="nav-link active" data-page="dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#" class="nav-link" data-page="users"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#" class="nav-link" data-page="videos"><i class="fas fa-video"></i> Tutorial Videos</a></li>
            <li><a href="#" class="nav-link" data-page="courses"><i class="fas fa-book-open"></i> Courses</a></li>
            <li><a href="#" class="nav-link" data-page="analytics"><i class="fas fa-chart-bar"></i> Analytics</a></li>
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
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>1,248</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon videos-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-info">
                        <h3>86</h3>
                        <p>Tutorial Videos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon courses-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Active Courses</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Recent Users</h2>
                    <button class="btn"><i class="fas fa-plus"></i> Add New User</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah Johnson</td>
                            <td>sarah@example.com</td>
                            <td>Jun 12, 2023</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Michael Chen</td>
                            <td>michael@example.com</td>
                            <td>Jun 10, 2023</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Emma Wilson</td>
                            <td>emma@example.com</td>
                            <td>Jun 8, 2023</td>
                            <td><span style="color: var(--error-color);">Inactive</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Tutorial Videos -->
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Tutorial Videos</h2>
                    <button class="btn" id="addVideoBtn"><i class="fas fa-plus"></i> Add New Video</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Morning Sun Salutation</td>
                            <td>Beginner</td>
                            <td>15 min</td>
                            <td>Jun 10, 2023</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Evening Relaxation Flow</td>
                            <td>Intermediate</td>
                            <td>25 min</td>
                            <td>Jun 5, 2023</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
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
                            <th>Course Name</th>
                            <th>Level</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Yoga for Beginners</td>
                            <td>Beginner</td>
                            <td>342</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Advanced Asana Practice</td>
                            <td>Advanced</td>
                            <td>128</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
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
                    <button class="btn"><i class="fas fa-plus"></i> Add New User</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah Johnson</td>
                            <td>sarah@example.com</td>
                            <td>Jun 12, 2023</td>
                            <td>Today</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Michael Chen</td>
                            <td>michael@example.com</td>
                            <td>Jun 10, 2023</td>
                            <td>Yesterday</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Emma Wilson</td>
                            <td>emma@example.com</td>
                            <td>Jun 8, 2023</td>
                            <td>3 days ago</td>
                            <td><span style="color: var(--error-color);">Inactive</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>David Brown</td>
                            <td>david@example.com</td>
                            <td>Jun 5, 2023</td>
                            <td>Today</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Lisa Taylor</td>
                            <td>lisa@example.com</td>
                            <td>Jun 3, 2023</td>
                            <td>2 days ago</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Tutorial Videos Page -->
        <div class="page-content" id="videos">
            <div class="header">
                <h1 class="page-title">Tutorial Videos</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">All Tutorial Videos</h2>
                    <button class="btn" id="addVideoBtnPage"><i class="fas fa-plus"></i> Add New Video</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Upload Date</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Morning Sun Salutation</td>
                            <td>Beginner</td>
                            <td>15 min</td>
                            <td>Jun 10, 2023</td>
                            <td>1,245</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Evening Relaxation Flow</td>
                            <td>Intermediate</td>
                            <td>25 min</td>
                            <td>Jun 5, 2023</td>
                            <td>987</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Breathing Techniques</td>
                            <td>All Levels</td>
                            <td>10 min</td>
                            <td>May 28, 2023</td>
                            <td>2,134</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Yoga for Back Pain</td>
                            <td>Beginner</td>
                            <td>20 min</td>
                            <td>May 20, 2023</td>
                            <td>3,456</td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
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
                            <th>Course Name</th>
                            <th>Level</th>
                            <th>Enrolled</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Yoga for Beginners</td>
                            <td>Beginner</td>
                            <td>342</td>
                            <td>4 weeks</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Advanced Asana Practice</td>
                            <td>Advanced</td>
                            <td>128</td>
                            <td>6 weeks</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Meditation & Mindfulness</td>
                            <td>All Levels</td>
                            <td>256</td>
                            <td>4 weeks</td>
                            <td><span style="color: var(--warning-color);">Draft</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Yin Yoga Deep Stretch</td>
                            <td>Intermediate</td>
                            <td>187</td>
                            <td>5 weeks</td>
                            <td><span style="color: var(--success-color);">Active</span></td>
                            <td class="action-buttons">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Analytics Page -->
        <div class="page-content" id="analytics">
            <div class="header">
                <h1 class="page-title">Analytics</h1>
                <div class="user-info">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='20' fill='%23a8d5ba'/><text x='50%' y='50%' font-size='16' text-anchor='middle' fill='%232a5948' dy='.3em'>AD</text></svg>" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
            
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon users-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24.5%</h3>
                        <p>User Growth</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon videos-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3>56,789</h3>
                        <p>Total Video Views</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon courses-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-info">
                        <h3>913</h3>
                        <p>Course Completions</p>
                    </div>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Monthly Performance</h2>
                </div>
                
                <div style="height: 300px; background-color: #f8faf8; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6a8e7e;">
                    <i class="fas fa-chart-bar" style="font-size: 48px; margin-right: 15px;"></i>
                    <span>Monthly Analytics Chart</span>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Top Performing Content</h2>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Engagement</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Morning Sun Salutation</td>
                            <td>Video</td>
                            <td>92%</td>
                            <td>88%</td>
                        </tr>
                        <tr>
                            <td>Yoga for Beginners</td>
                            <td>Course</td>
                            <td>87%</td>
                            <td>76%</td>
                        </tr>
                        <tr>
                            <td>Breathing Techniques</td>
                            <td>Video</td>
                            <td>85%</td>
                            <td>91%</td>
                        </tr>
                        <tr>
                            <td>Meditation & Mindfulness</td>
                            <td>Course</td>
                            <td>79%</td>
                            <td>82%</td>
                        </tr>
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
                        <input type="text" id="siteName" value="Feel-Good Yoga">
                    </div>
                    
                    <div class="form-group">
                        <label for="siteDescription">Site Description</label>
                        <textarea id="siteDescription" rows="3">Quick morning energizers, cozy stretch breaks, and calming breath—designed to make you smile.</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="adminEmail">Admin Email</label>
                        <input type="email" id="adminEmail" value="admin@feelgoodyoga.com">
                    </div>
                    
                    <button type="submit" class="btn">Save Settings</button>
                </form>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2 class="section-title">User Permissions</h2>
                </div>
                
                <form>
                    <div class="form-group">
                        <label for="userRegistration">Allow User Registration</label>
                        <select id="userRegistration">
                            <option value="yes" selected>Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultRole">Default User Role</label>
                        <select id="defaultRole">
                            <option value="subscriber">Subscriber</option>
                            <option value="contributor">Contributor</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Save Permissions</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Video Modal -->
    <div class="modal" id="videoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add New Tutorial Video</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form>
                <div class="form-group">
                    <label for="videoTitle">Video Title</label>
                    <input type="text" id="videoTitle" placeholder="Enter video title">
                </div>
                <div class="form-group">
                    <label for="videoDescription">Description</label>
                    <textarea id="videoDescription" rows="3" placeholder="Enter video description"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="videoCategory">Category</label>
                        <select id="videoCategory">
                            <option value="">Select category</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="videoDuration">Duration (minutes)</label>
                        <input type="number" id="videoDuration" placeholder="Duration">
                    </div>
                </div>
                <div class="form-group">
                    <label for="videoFile">Upload Video</label>
                    <input type="file" id="videoFile">
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Save Video</button>
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
            <form>
                <div class="form-group">
                    <label for="courseName">Course Name</label>
                    <input type="text" id="courseName" placeholder="Enter course name">
                </div>
                <div class="form-group">
                    <label for="courseDescription">Description</label>
                    <textarea id="courseDescription" rows="3" placeholder="Enter course description"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="courseLevel">Difficulty Level</label>
                        <select id="courseLevel">
                            <option value="">Select level</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="courseWeeks">Duration (weeks)</label>
                        <input type="number" id="courseWeeks" placeholder="Number of weeks">
                    </div>
                </div>
                <div class="form-group">
                    <label for="courseImage">Course Image</label>
                    <input type="file" id="courseImage">
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Save Course</button>
            </form>
        </div>
    </div>

    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const videoModal = document.getElementById('videoModal');
        const courseModal = document.getElementById('courseModal');
        const addVideoBtn = document.getElementById('addVideoBtn');
        const addCourseBtn = document.getElementById('addCourseBtn');
        const addVideoBtnPage = document.getElementById('addVideoBtnPage');
        const addCourseBtnPage = document.getElementById('addCourseBtnPage');
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
                videoModal.style.display = 'flex';
            });
        }
        
        if (addCourseBtn) {
            addCourseBtn.addEventListener('click', () => {
                courseModal.style.display = 'flex';
            });
        }
        
        if (addVideoBtnPage) {
            addVideoBtnPage.addEventListener('click', () => {
                videoModal.style.display = 'flex';
            });
        }
        
        if (addCourseBtnPage) {
            addCourseBtnPage.addEventListener('click', () => {
                courseModal.style.display = 'flex';
            });
        }
        
        // Close Modals
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                videoModal.style.display = 'none';
                courseModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === videoModal) {
                videoModal.style.display = 'none';
            }
            if (e.target === courseModal) {
                courseModal.style.display = 'none';
            }
        });
        
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
        
        // Form submission (prevent default for demo)
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Form submitted! In a real application, this would save data to the server.');
                videoModal.style.display = 'none';
                courseModal.style.display = 'none';
            });
        });
        
        // Sample data for demonstration
        const sampleUsers = [
            { name: 'Sarah Johnson', email: 'sarah@example.com', joined: 'Jun 12, 2023', status: 'Active' },
            { name: 'Michael Chen', email: 'michael@example.com', joined: 'Jun 10, 2023', status: 'Active' },
            { name: 'Emma Wilson', email: 'emma@example.com', joined: 'Jun 8, 2023', status: 'Inactive' }
        ];
        
        const sampleVideos = [
            { title: 'Morning Sun Salutation', category: 'Beginner', duration: '15 min', date: 'Jun 10, 2023' },
            { title: 'Evening Relaxation Flow', category: 'Intermediate', duration: '25 min', date: 'Jun 5, 2023' }
        ];
        
        const sampleCourses = [
            { name: 'Yoga for Beginners', level: 'Beginner', enrolled: 342, status: 'Active' },
            { name: 'Advanced Asana Practice', level: 'Advanced', enrolled: 128, status: 'Active' }
        ];
        
        // In a real application, you would use this data to dynamically populate tables
        console.log('Sample data loaded:', { sampleUsers, sampleVideos, sampleCourses });
    </script>
</body>
</html>