<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['student_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit;
}

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - <?php echo ucfirst($current_page); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        
        /* Card styles */
        .card {
            background-color: white;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 500;
            color: #111827;
        }
        
        .card-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        .card-content {
            padding: 1.5rem;
        }
        
        /* Mobile menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 18rem;
            background-color: white;
            z-index: 40;
            transform: translateX(100%);
            transition: transform 0.2s ease-in-out;
        }
        
        .mobile-menu.open {
            transform: translateX(0);
        }
        
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
            display: none;
        }
        
        .mobile-menu-overlay.open {
            display: block;
        }
        
        /* Progress bar */
        .progress-bar {
            width: 100%;
            height: 0.5rem;
            background-color: rgba(229, 231, 235, 1);
            border-radius: 9999px;
        }
        
        .progress-bar-fill {
            height: 0.5rem;
            border-radius: 9999px;
        }
        
        /* Tab styles */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .tab-button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            transition: background-color 0.2s, color 0.2s;
        }
        
        .tab-button:hover {
            background-color: rgba(243, 244, 246, 1);
        }
        
        .tab-button.active {
            background-color: white;
            color: #3b82f6;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Navigation - Mobile -->
    <div class="md:hidden flex items-center justify-between p-4 bg-white border-b">
        <div class="flex items-center gap-2">
            <img src="/placeholder.svg?height=40&width=40" alt="University Logo" width="40" height="40" class="rounded">
            <span class="font-semibold text-lg">Student Portal</span>
        </div>
        <button id="mobile-menu-button" class="p-2 rounded-md hover:bg-gray-100">
            <i data-lucide="menu" class="h-6 w-6"></i>
            <span class="sr-only">Open menu</span>
        </button>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu p-6">
        <div class="flex justify-end mb-6">
            <button id="close-mobile-menu" class="p-2 rounded-md hover:bg-gray-100">
                <i data-lucide="x" class="h-6 w-6"></i>
                <span class="sr-only">Close menu</span>
            </button>
        </div>
        <nav class="flex flex-col gap-4 mt-8">
            <a href="index.php" class="flex items-center gap-2 p-2 <?php echo $current_page == 'index' ? 'bg-gray-100' : 'hover:bg-gray-100'; ?> rounded-md">
                <i data-lucide="graduation-cap" class="h-5 w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="profile.php" class="flex items-center gap-2 p-2 <?php echo $current_page == 'profile' ? 'bg-gray-100' : 'hover:bg-gray-100'; ?> rounded-md">
                <i data-lucide="user" class="h-5 w-5"></i>
                <span>Profile</span>
            </a>
            <a href="attendance.php" class="flex items-center gap-2 p-2 <?php echo $current_page == 'attendance' ? 'bg-gray-100' : 'hover:bg-gray-100'; ?> rounded-md">
                <i data-lucide="clock" class="h-5 w-5"></i>
                <span>Attendance</span>
            </a>
            <a href="#" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <i data-lucide="file-text" class="h-5 w-5"></i>
                <span>Courses</span>
            </a>
            <a href="#" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <i data-lucide="message-square" class="h-5 w-5"></i>
                <span>Announcements</span>
            </a>
        </nav>
    </div>

    <!-- Navigation - Desktop -->
    <div class="hidden md:flex items-center justify-between p-4 bg-white border-b">
        <div class="flex items-center gap-3">
            <img src="/placeholder.svg?height=48&width=48" alt="University Logo" width="48" height="48" class="rounded">
            <span class="font-semibold text-xl">Student Portal</span>
        </div>
        <nav class="flex items-center gap-6">
            <a href="index.php" class="<?php echo $current_page == 'index' ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?> flex items-center gap-1">
                <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                <span>Dashboard</span>
            </a>
            <a href="profile.php" class="<?php echo $current_page == 'profile' ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?> flex items-center gap-1">
                <i data-lucide="user" class="h-4 w-4"></i>
                <span>Profile</span>
            </a>
            <a href="attendance.php" class="<?php echo $current_page == 'attendance' ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-600'; ?> flex items-center gap-1">
                <i data-lucide="clock" class="h-4 w-4"></i>
                <span>Attendance</span>
            </a>
            <a href="#" class="text-gray-700 hover:text-blue-600 flex items-center gap-1">
                <i data-lucide="file-text" class="h-4 w-4"></i>
                <span>Courses</span>
            </a>
            <a href="#" class="text-gray-700 hover:text-blue-600 flex items-center gap-1">
                <i data-lucide="message-square" class="h-4 w-4"></i>
                <span>Announcements</span>
            </a>
        </nav>
    </div>

