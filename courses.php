<?php
// Include header
require_once 'includes/header.php';

// Include database connection
require_once 'config/database.php';

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT name, student_id FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get current semester
$current_semester = "Spring 2025"; // In a real app, this might be determined dynamically

// Get student's courses for the current semester
$current_courses_query = "
    SELECT c.id, c.code, c.name, c.description, c.credits, 
           sc.semester, sc.grade, sc.progress,
           s.name AS instructor, s.email AS instructor_email
    FROM courses c
    JOIN student_courses sc ON c.id = sc.course_id
    JOIN staff s ON c.instructor_id = s.id
    WHERE sc.student_id = ? AND sc.semester = ?
    ORDER BY c.code
";
$current_courses_stmt = $conn->prepare($current_courses_query);
$current_courses_stmt->bind_param("is", $student_id, $current_semester);
$current_courses_stmt->execute();
$current_courses_result = $current_courses_stmt->get_result();
$current_courses_stmt->close();

// Get student's past courses
$past_courses_query = "
    SELECT c.id, c.code, c.name, c.description, c.credits, 
           sc.semester, sc.grade, sc.progress,
           s.name AS instructor
    FROM courses c
    JOIN student_courses sc ON c.id = sc.course_id
    JOIN staff s ON c.instructor_id = s.id
    WHERE sc.student_id = ? AND sc.semester != ?
    ORDER BY sc.semester DESC, c.code
";
$past_courses_stmt = $conn->prepare($past_courses_query);
$past_courses_stmt->bind_param("is", $student_id, $current_semester);
$past_courses_stmt->execute();
$past_courses_result = $past_courses_stmt->get_result();
$past_courses_stmt->close();

// Get available courses for registration
$available_courses_query = "
    SELECT c.id, c.code, c.name, c.description, c.credits, 
           s.name AS instructor
    FROM courses c
    JOIN staff s ON c.instructor_id = s.id
    WHERE c.id NOT IN (
        SELECT course_id FROM student_courses WHERE student_id = ?
    )
    ORDER BY c.code
";
$available_courses_stmt = $conn->prepare($available_courses_query);
$available_courses_stmt->bind_param("i", $student_id);
$available_courses_stmt->execute();
$available_courses_result = $available_courses_stmt->get_result();
$available_courses_stmt->close();

// Get course schedule for current semester
$schedule_query = "
    SELECT cs.day_of_week, cs.start_time, cs.end_time, cs.room,
           c.code, c.name
    FROM course_schedule cs
    JOIN courses c ON cs.course_id = c.id
    JOIN student_courses sc ON c.id = sc.course_id
    WHERE sc.student_id = ? AND sc.semester = ?
    ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), 
             cs.start_time
";
$schedule_stmt = $conn->prepare($schedule_query);
$schedule_stmt->bind_param("is", $student_id, $current_semester);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();
$schedule_stmt->close();

// Get course materials
$materials_query = "
    SELECT cm.id, cm.title, cm.type, cm.upload_date, c.code, c.name
    FROM course_materials cm
    JOIN courses c ON cm.course_id = c.id
    JOIN student_courses sc ON c.id = sc.course_id
    WHERE sc.student_id = ? AND sc.semester = ?
    ORDER BY cm.upload_date DESC
";
$materials_stmt = $conn->prepare($materials_query);
$materials_stmt->bind_param("is", $student_id, $current_semester);
$materials_stmt->execute();
$materials_result = $materials_stmt->get_result();
$materials_stmt->close();

// Get assignments
$assignments_query = "
    SELECT a.id, a.title, a.description, a.due_date, a.max_points,
           c.code, c.name,
           sa.submission_date, sa.grade, sa.status
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    JOIN student_courses sc ON c.id = sc.course_id
    LEFT JOIN student_assignments sa ON a.id = sa.assignment_id AND sa.student_id = ?
    WHERE sc.student_id = ? AND sc.semester = ?
    ORDER BY a.due_date
";
$assignments_stmt = $conn->prepare($assignments_query);
$assignments_stmt->bind_param("iis", $student_id, $student_id, $current_semester);
$assignments_stmt->execute();
$assignments_result = $assignments_stmt->get_result();
$assignments_stmt->close();

// Handle course registration
$registration_message = '';
$registration_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course'])) {
    $course_id = $_POST['course_id'];
    
    // Check if already registered
    $check_stmt = $conn->prepare("SELECT id FROM student_courses WHERE student_id = ? AND course_id = ?");
    $check_stmt->bind_param("ii", $student_id, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $registration_error = "You are already registered for this course.";
    } else {
        // Register for the course
        $register_stmt = $conn->prepare("
            INSERT INTO student_courses (student_id, course_id, semester, progress) 
            VALUES (?, ?, ?, 0)
        ");
        $register_stmt->bind_param("iis", $student_id, $course_id, $current_semester);
        
        if ($register_stmt->execute()) {
            $registration_message = "Successfully registered for the course.";
            // Refresh the page to show updated course list
            header("Location: courses.php?registration=success");
            exit;
        } else {
            $registration_error = "Error registering for the course: " . $conn->error;
        }
        
        $register_stmt->close();
    }
    
    $check_stmt->close();
}
?>

<!-- Main Content -->
<main class="flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold">My Courses</h1>
            <p class="text-gray-500 mt-1">
                <?php echo $current_semester; ?> • <?php echo htmlspecialchars($student['name']); ?>
            </p>
        </div>
        
        <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">Successfully registered for the course.</span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($registration_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $registration_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($registration_error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $registration_error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Tabs for different views -->
        <div class="tabs">
            <div class="grid grid-cols-5 md:w-[600px] bg-gray-100 p-1 rounded-lg">
                <button class="tab-button active" data-tab="current">Current Courses</button>
                <button class="tab-button" data-tab="past">Past Courses</button>
                <button class="tab-button" data-tab="schedule">Schedule</button>
                <button class="tab-button" data-tab="materials">Materials</button>
                <button class="tab-button" data-tab="assignments">Assignments</button>
            </div>

            <!-- Current Courses Tab -->
            <div id="current-tab" class="tab-content active mt-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if ($current_courses_result->num_rows > 0): ?>
                        <?php while ($course = $current_courses_result->fetch_assoc()): ?>
                            <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                                <div class="p-6 border-b">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium"><?php echo htmlspecialchars($course['code']); ?> - <?php echo htmlspecialchars($course['name']); ?></h3>
                                            <p class="text-sm text-gray-500">Instructor: <?php echo htmlspecialchars($course['instructor']); ?></p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $course['credits']; ?> Credits
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span>Progress</span>
                                                <span class="font-medium"><?php echo $course['progress']; ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $course['progress']; ?>%"></div>
                                            </div>
                                        </div>
                                        
                                        <?php if ($course['grade']): ?>
                                            <div class="flex justify-between text-sm">
                                                <span>Current Grade</span>
                                                <span class="font-medium"><?php echo number_format($course['grade'], 1); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-between">
                                        <a href="course_detail.php?id=<?php echo $course['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details
                                        </a>
                                        <a href="mailto:<?php echo $course['instructor_email']; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Contact Instructor
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-full bg-white rounded-lg border shadow-sm p-6">
                            <p class="text-gray-500 text-center">You are not enrolled in any courses for the current semester.</p>
                            <div class="mt-4 text-center">
                                <button class="tab-button px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" data-tab="available">
                                    Browse Available Courses
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium">Available Courses</h3>
                        <p class="text-sm text-gray-500">Courses available for registration</p>
                    </div>
                    <div class="p-6">
                        <?php if ($available_courses_result->num_rows > 0): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php while ($course = $available_courses_result->fetch_assoc()): ?>
                                    <div class="border rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium"><?php echo htmlspecialchars($course['code']); ?> - <?php echo htmlspecialchars($course['name']); ?></h4>
                                                <p class="text-sm text-gray-500">Instructor: <?php echo htmlspecialchars($course['instructor']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo $course['credits']; ?> Credits</p>
                                            </div>
                                            <form action="courses.php" method="POST">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="register_course" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                                    Register
                                                </button>
                                            </form>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($course['description']); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">No additional courses available for registration.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Past Courses Tab -->
            <div id="past-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium">Past Courses</h3>
                        <p class="text-sm text-gray-500">Courses from previous semesters</p>
                    </div>
                    <div class="p-6">
                        <?php if ($past_courses_result->num_rows > 0): ?>
                            <?php 
                                $current_semester = null;
                                while ($course = $past_courses_result->fetch_assoc()):
                                    if ($current_semester !== $course['semester']):
                                        if ($current_semester !== null) echo '</div>';
                                        $current_semester = $course['semester'];
                            ?>
                                <h4 class="font-medium text-lg mb-4 mt-6"><?php echo htmlspecialchars($course['semester']); ?></h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php endif; ?>
                                    <div class="border rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium"><?php echo htmlspecialchars($course['code']); ?> - <?php echo htmlspecialchars($course['name']); ?></h4>
                                                <p class="text-sm text-gray-500">Instructor: <?php echo htmlspecialchars($course['instructor']); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo $course['credits']; ?> Credits</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                    $grade = $course['grade'];
                                                    if ($grade >= 3.7) echo 'bg-green-100 text-green-800';
                                                    elseif ($grade >= 2.7) echo 'bg-blue-100 text-blue-800';
                                                    elseif ($grade >= 1.7) echo 'bg-yellow-100 text-yellow-800';
                                                    else echo 'bg-red-100 text-red-800';
                                                ?>">
                                                Grade: <?php echo number_format($course['grade'], 1); ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($course['description']); ?></p>
                                    </div>
                            <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">No past courses found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Schedule Tab -->
            <div id="schedule-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium">Course Schedule</h3>
                        <p class="text-sm text-gray-500"><?php echo $current_semester; ?> schedule</p>
                    </div>
                    <div class="p-6">
                        <?php if ($schedule_result->num_rows > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php 
                                            $current_day = null;
                                            while ($schedule = $schedule_result->fetch_assoc()):
                                                $row_class = ($current_day === $schedule['day_of_week']) ? 'bg-gray-50' : '';
                                                $current_day = $schedule['day_of_week'];
                                        ?>
                                            <tr class="<?php echo $row_class; ?>">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($schedule['day_of_week']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($schedule['start_time']); ?> - <?php echo htmlspecialchars($schedule['end_time']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="font-medium"><?php echo htmlspecialchars($schedule['code']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($schedule['name']); ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($schedule['room']); ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-6">
                                <button class="px-4 py-2 border rounded-md hover:bg-gray-50">
                                    Download Schedule
                                </button>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">No schedule available for the current semester.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Materials Tab -->
            <div id="materials-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium">Course Materials</h3>
                        <p class="text-sm text-gray-500">Lecture notes, readings, and resources</p>
                    </div>
                    <div class="p-6">
                        <?php if ($materials_result->num_rows > 0): ?>
                            <div class="space-y-4">
                                <?php while ($material = $materials_result->fetch_assoc()): ?>
                                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                                        <div class="p-2 bg-gray-100 rounded">
                                            <i data-lucide="file-text" class="h-6 w-6 text-gray-500"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-medium"><?php echo htmlspecialchars($material['title']); ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($material['code']); ?> • 
                                                <?php echo ucfirst($material['type']); ?> • 
                                                Uploaded on <?php echo date('M j, Y', strtotime($material['upload_date'])); ?>
                                            </p>
                                        </div>
                                        <a href="download_material.php?id=<?php echo $material['id']; ?>" class="px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                                            Download
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">No course materials available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Assignments Tab -->
            <div id="assignments-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm overflow-hidden">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium">Assignments</h3>
                        <p class="text-sm text-gray-500">Homework, projects, and assessments</p>
                    </div>
                    <div class="p-6">
                        <?php if ($assignments_result->num_rows > 0): ?>
                            <div class="space-y-6">
                                <h4 class="font-medium">Upcoming Assignments</h4>
                                <div class="space-y-4">
                                    <?php 
                                        $assignments_result->data_seek(0);
                                        $found_upcoming = false;
                                        while ($assignment = $assignments_result->fetch_assoc()):
                                            $due_date = new DateTime($assignment['due_date']);
                                            $now = new DateTime();
                                            if ($due_date > $now && (!$assignment['submission_date'] || $assignment['status'] === 'resubmit')):
                                                $found_upcoming = true;
                                    ?>
                                        <div class="flex items-start gap-4 p-4 border rounded-lg">
                                            <div class="bg-blue-100 rounded-md p-2 text-blue-600">
                                                <i data-lucide="file-text" class="h-5 w-5"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium"><?php echo htmlspecialchars($assignment['title']); ?></div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($assignment['code']); ?> • 
                                                    Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?> • 
                                                    Points: <?php echo $assignment['max_points']; ?>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($assignment['description']); ?></p>
                                            </div>
                                            <a href="submit_assignment.php?id=<?php echo $assignment['id']; ?>" class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                                <?php echo $assignment['submission_date'] ? 'Resubmit' : 'Submit'; ?>
                                            </a>
                                        </div>
                                    <?php 
                                        endif;
                                        endwhile;
                                        if (!$found_upcoming):
                                    ?>
                                        <p class="text-gray-500 text-center">No upcoming assignments.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <h4 class="font-medium mt-8">Submitted Assignments</h4>
                                <div class="space-y-4">
                                    <?php 
                                        $assignments_result->data_seek(0);
                                        $found_submitted = false;
                                        while ($assignment = $assignments_result->fetch_assoc()):
                                            if ($assignment['submission_date']):
                                                $found_submitted = true;
                                    ?>
                                        <div class="flex items-start gap-4 p-4 border rounded-lg">
                                            <div class="bg-green-100 rounded-md p-2 text-green-600">
                                                <i data-lucide="check-circle" class="h-5 w-5"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium"><?php echo htmlspecialchars($assignment['title']); ?></div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($assignment['code']); ?> • 
                                                    Submitted: <?php echo date('M j, Y', strtotime($assignment['submission_date'])); ?>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        <?php 
                                                            if ($assignment['status'] === 'graded') {
                                                                $grade_percent = ($assignment['grade'] / $assignment['max_points']) * 100;
                                                                if ($grade_percent >= 90) echo 'bg-green-100 text-green-800';
                                                                elseif ($grade_percent >= 80) echo 'bg-blue-100 text-blue-800';
                                                                elseif ($grade_percent >= 70) echo 'bg-yellow-100 text-yellow-800';
                                                                else echo 'bg-red-100 text-red-800';
                                                            } elseif ($assignment['status'] === 'submitted') {
                                                                echo 'bg-blue-100 text-blue-800';
                                                            } elseif ($assignment['status'] === 'resubmit') {
                                                                echo 'bg-yellow-100 text-yellow-800';
                                                            }
                                                        ?>">
                                                        <?php 
                                                            if ($assignment['status'] === 'graded') {
                                                                echo 'Grade: ' . $assignment['grade'] . '/' . $assignment['max_points'];
                                                            } else {
                                                                echo ucfirst($assignment['status']);
                                                            }
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="view_submission.php?id=<?php echo $assignment['id']; ?>" class="px-3 py-1.5 border rounded-md hover:bg-gray-50">
                                                View
                                            </a>
                                        </div>
                                    <?php 
                                        endif;
                                        endwhile;
                                        if (!$found_submitted):
                                    ?>
                                        <p class="text-gray-500 text-center">No submitted assignments.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center">No assignments available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  ?>
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

