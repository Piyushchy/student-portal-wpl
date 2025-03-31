<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';

// Initialize variables
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($student_id) || empty($password)) {
        $error = 'Please enter both student ID and password';
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, student_id, name, password FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();
            
            // Verify password (in a real app, use password_verify with hashed passwords)
            if ($password === $student['password']) { // For demo only, use password_verify in production
                // Set session variables
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                
                // Redirect to dashboard
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Student ID not found';
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <img class="mx-auto h-12 w-auto" src="/placeholder.svg?height=48&width=48" alt="University Logo">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Student Portal Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your credentials to access your account
            </p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <form class="mt-8 space-y-6" action="login.php" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="student_id" class="sr-only">Student ID</label>
                    <input id="student_id" name="student_id" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Student ID">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sign in
                </button>
            </div>
        </form>
        
        <div class="mt-6">
            <p class="text-center text-sm text-gray-600">
                Demo credentials: ST12345 / password123
            </p>
        </div>
    </div>
</body>
</html>

