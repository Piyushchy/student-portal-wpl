<?php
// Include header
require_once 'includes/header.php';

// Include database connection
require_once 'config/database.php';

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("
    SELECT s.*, p.program_name, p.expected_graduation
    FROM students s
    LEFT JOIN programs p ON s.program_id = p.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get academic information
$academic_query = "
    SELECT 
        s.enrollment_year,
        (SELECT COUNT(*) FROM student_courses WHERE student_id = ?) AS credits_completed,
        (SELECT AVG(grade) FROM student_courses WHERE student_id = ?) AS gpa,
        (SELECT name FROM staff WHERE id = s.advisor_id) AS advisor_name,
        (SELECT email FROM staff WHERE id = s.advisor_id) AS advisor_email
    FROM students s
    WHERE s.id = ?
";
$academic_stmt = $conn->prepare($academic_query);
$academic_stmt->bind_param("iii", $student_id, $student_id, $student_id);
$academic_stmt->execute();
$academic = $academic_stmt->get_result()->fetch_assoc();
$academic_stmt->close();

// Get documents
$documents_query = "SELECT * FROM student_documents WHERE student_id = ? ORDER BY upload_date DESC";
$documents_stmt = $conn->prepare($documents_query);
$documents_stmt->bind_param("i", $student_id);
$documents_stmt->execute();
$documents_result = $documents_stmt->get_result();
$documents_stmt->close();

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $bio = $_POST['bio'];
    
    $update_stmt = $conn->prepare("
        UPDATE students 
        SET name = ?, email = ?, phone = ?, date_of_birth = ?, address = ?, bio = ?
        WHERE id = ?
    ");
    $update_stmt->bind_param("ssssssi", $name, $email, $phone, $dob, $address, $bio, $student_id);
    
    if ($update_stmt->execute()) {
        $success_message = 'Profile updated successfully!';
        
        // Refresh student data
        $stmt = $conn->prepare("
            SELECT s.*, p.program_name, p.expected_graduation
            FROM students s
            LEFT JOIN programs p ON s.program_id = p.id
            WHERE s.id = ?
        ");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $error_message = 'Error updating profile: ' . $conn->error;
    }
    
    $update_stmt->close();
}
?>

<!-- Main Content -->
<main class="flex-1 p-4 md:p-8">
    <div class="max-w-5xl mx-auto space-y-8">
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 md:p-8">
            <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                <div class="relative">
                    <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden">
                        <img src="<?php echo !empty($student['profile_image']) ? htmlspecialchars($student['profile_image']) : '/placeholder.svg?height=128&width=128'; ?>" alt="Profile Picture" width="128" height="128" class="object-cover">
                    </div>
                    <button class="absolute bottom-0 right-0 rounded-full p-2 bg-gray-200 hover:bg-gray-300">
                        <i data-lucide="camera" class="h-4 w-4"></i>
                        <span class="sr-only">Change profile picture</span>
                    </button>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($student['name']); ?></h1>
                    <p class="text-gray-500">
                        <?php echo htmlspecialchars($student['student_id']); ?> • <?php echo htmlspecialchars($student['program_name']); ?>
                    </p>
                    <div class="flex flex-wrap gap-3 mt-4 justify-center md:justify-start">
                        <div class="flex items-center gap-1 text-sm text-gray-600">
                            <i data-lucide="mail" class="h-4 w-4"></i>
                            <span><?php echo htmlspecialchars($student['email']); ?></span>
                        </div>
                        <div class="flex items-center gap-1 text-sm text-gray-600">
                            <i data-lucide="phone" class="h-4 w-4"></i>
                            <span><?php echo htmlspecialchars($student['phone']); ?></span>
                        </div>
                    </div>
                </div>
          
            </div>
        </div>

        <!-- Profile Content -->
        <div class="tabs">
            <div class="grid grid-cols-3 md:w-[400px] bg-gray-100 p-1 rounded-lg">
                <button class="tab-button active" data-tab="personal">Personal</button>
                <button class="tab-button" data-tab="academic">Academic</button>
                <button class="tab-button" data-tab="documents">Documents</button>
            </div>

            <!-- Personal Information Tab -->
            <div id="personal-tab" class="tab-content active mt-6 space-y-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-medium">Personal Information</h2>
                        <p class="text-sm text-gray-500">Your personal details and contact information</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <form action="profile.php" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" id="fullName" name="fullName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($student['name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($student['email']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($student['phone']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                    <input type="date" id="dob" name="dob" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>">
                                </div>
                            </div>

                            <hr class="my-6 border-gray-200">

                            <div class="form-group">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea id="address" name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($student['address']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                                <textarea id="bio" name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($student['bio']); ?></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" name="update_profile" class="flex gap-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    <i data-lucide="save" class="h-4 w-4"></i>
                                    <span>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Academic Information Tab -->
            <div id="academic-tab" class="tab-content mt-6 space-y-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-medium">Academic Information</h2>
                        <p class="text-sm text-gray-500">Your academic records and progress</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Program</h3>
                                    <p><?php echo htmlspecialchars($student['program_name']); ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Enrollment Year</h3>
                                    <p><?php echo htmlspecialchars($academic['enrollment_year']); ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Expected Graduation</h3>
                                    <p><?php echo htmlspecialchars($student['expected_graduation']); ?></p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Current Semester</h3>
                                    <p>Spring 2025</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">GPA</h3>
                                    <p><?php echo number_format($academic['gpa'], 2); ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Credits Completed</h3>
                                    <p><?php echo $academic['credits_completed']; ?></p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Academic Advisor</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i data-lucide="user" class="h-6 w-6 text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium"><?php echo htmlspecialchars($academic['advisor_name']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($academic['advisor_email']); ?></p>
                                </div>
                                <button class="ml-auto px-3 py-1.5 border rounded-md text-sm hover:bg-gray-50">
                                    Contact
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-medium">Academic Progress</h2>
                        <p class="text-sm text-gray-500">Your progress toward graduation</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Credits Completed</span>
                                    <span class="font-medium"><?php echo $academic['credits_completed']; ?>/120</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo ($academic['credits_completed'] / 120) * 100; ?>%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-100 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold"><?php echo number_format($academic['gpa'], 2); ?></p>
                                    <p class="text-sm text-gray-500">Current GPA</p>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-4 text-center">
                                    <p class="text-2xl font-bold">
                                        <?php 
                                            $graduation_year = intval(substr($student['expected_graduation'], 0, 4));
                                            $current_year = intval(date('Y'));
                                            $semesters_remaining = ($graduation_year - $current_year) * 2;
                                            echo $semesters_remaining;
                                        ?>
                                    </p>
                                    <p class="text-sm text-gray-500">Semesters Remaining</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div id="documents-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6 border-b flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium">Documents</h2>
                            <p class="text-sm text-gray-500">Your uploaded documents and forms</p>
                        </div>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Upload Document
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php if ($documents_result->num_rows > 0): ?>
                                <?php while ($document = $documents_result->fetch_assoc()): ?>
                                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                        <div class="p-2 bg-gray-100 rounded">
                                            <i data-lucide="file-text" class="h-6 w-6 text-gray-500"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-medium"><?php echo htmlspecialchars($document['filename']); ?></p>
                                            <p class="text-sm text-gray-500">
                                                Uploaded on <?php echo date('M j, Y', strtotime($document['upload_date'])); ?> • <?php echo htmlspecialchars($document['filesize']); ?>
                                            </p>
                                        </div>
                                        <a href="download_document.php?id=<?php echo $document['id']; ?>" class="px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                            Download
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No documents available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include footer
require_once 'includes/footer.php';
?>

