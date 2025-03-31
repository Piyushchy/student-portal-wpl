<?php
// Include header
require_once 'includes/header.php';

// Include database connection
require_once 'config/database.php';

// Get student information
$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get announcements
$announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3";
$announcements_result = $conn->query($announcements_query);

// Get upcoming events
$events_query = "
    SELECT e.* 
    FROM events e
    JOIN student_events se ON e.id = se.event_id
    WHERE se.student_id = ? AND e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
    LIMIT 4
";
$events_stmt = $conn->prepare($events_query);
$events_stmt->bind_param("i", $student_id);
$events_stmt->execute();
$events_result = $events_stmt->get_result();
$events_stmt->close();

// Get courses
$courses_query = "
    SELECT c.*, sc.progress
    FROM courses c
    JOIN student_courses sc ON c.id = sc.course_id
    WHERE sc.student_id = ?
";
$courses_stmt = $conn->prepare($courses_query);
$courses_stmt->bind_param("i", $student_id);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();
$courses_stmt->close();
?>

<!-- Header -->
<header class="bg-white p-6 md:p-10 border-b">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold">Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h1>
        <p class="text-gray-500 mt-1">Spring Semester 2025 • Week 12</p>
    </div>
</header>

<!-- Main Content -->
<main class="flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Quick Access Buttons -->
        <section>
            <h2 class="text-lg font-semibold mb-4">Quick Access</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="#" class="quick-access-button">
                    <i data-lucide="clock" class="h-6 w-6"></i>
                    <span>View Timetable</span>
                </a>
                <a href="#" class="quick-access-button">
                    <i data-lucide="file-text" class="h-6 w-6"></i>
                    <span>Check Results</span>
                </a>
                <a href="attendance.php" class="quick-access-button">
                    <i data-lucide="user" class="h-6 w-6"></i>
                    <span>Attendance Record</span>
                </a>
                <a href="#" class="quick-access-button">
                    <i data-lucide="message-square" class="h-6 w-6"></i>
                    <span>Contact Support</span>
                </a>
            </div>
        </section>

        <!-- Dashboard Grid -->
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Announcements Section -->
            <section class="md:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Important Announcements</h3>
                        <p class="card-description">Latest updates from your university</p>
                    </div>
                    <div class="card-content">
                        <div class="space-y-4">
                            <?php if ($announcements_result->num_rows > 0): ?>
                                <?php while ($announcement = $announcements_result->fetch_assoc()): ?>
                                    <div class="border-b pb-4 last:border-0 last:pb-0">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-medium"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                            <span class="text-xs text-gray-500">
                                                <?php 
                                                    $date = new DateTime($announcement['created_at']);
                                                    $now = new DateTime();
                                                    $interval = $date->diff($now);
                                                    
                                                    if ($interval->days == 0) {
                                                        echo "Today";
                                                    } elseif ($interval->days == 1) {
                                                        echo "Yesterday";
                                                    } elseif ($interval->days < 7) {
                                                        echo $interval->days . " days ago";
                                                    } elseif ($interval->days < 30) {
                                                        echo floor($interval->days / 7) . " weeks ago";
                                                    } else {
                                                        echo $date->format('M j, Y');
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($announcement['content']); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No announcements available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Calendar Overview -->
            <section>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upcoming Events</h3>
                        <p class="card-description">Your schedule for the week</p>
                    </div>
                    <div class="card-content">
                        <div class="space-y-3">
                            <?php if ($events_result->num_rows > 0): ?>
                                <?php while ($event = $events_result->fetch_assoc()): ?>
                                    <div class="flex items-start gap-3 border-b pb-3 last:border-0 last:pb-0">
                                        <div class="bg-blue-100 rounded-md p-2 text-blue-600">
                                            <i data-lucide="calendar-days" class="h-5 w-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-sm"><?php echo htmlspecialchars($event['title']); ?></h4>
                                            <p class="text-xs text-gray-500">
                                                <?php 
                                                    $event_date = new DateTime($event['event_date']);
                                                    $today = new DateTime('today');
                                                    $tomorrow = new DateTime('tomorrow');
                                                    
                                                    if ($event_date->format('Y-m-d') === $today->format('Y-m-d')) {
                                                        echo "Today";
                                                    } elseif ($event_date->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                                                        echo "Tomorrow";
                                                    } else {
                                                        echo $event_date->format('M j');
                                                    }
                                                ?> • <?php echo htmlspecialchars($event['event_time']); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-gray-500">No upcoming events.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Courses Overview -->
        <section>
            <h2 class="text-lg font-semibold mb-4">Your Courses</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if ($courses_result->num_rows > 0): ?>
                    <?php while ($course = $courses_result->fetch_assoc()): ?>
                        <div class="card">
                            <div class="card-header pb-2">
                                <h3 class="card-title text-base"><?php echo htmlspecialchars($course['name']); ?></h3>
                                <p class="card-description"><?php echo htmlspecialchars($course['code']); ?></p>
                            </div>
                            <div class="card-content">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span>Progress</span>
                                        <span class="font-medium"><?php echo $course['progress']; ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-bar-fill bg-blue-500" style="width: <?php echo $course['progress']; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full">
                        <p class="text-gray-500">No courses available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php
// Include footer
require_once 'includes/footer.php';
?>

