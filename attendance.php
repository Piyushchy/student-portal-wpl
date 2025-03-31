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

// Get courses
$courses_query = "
    SELECT c.id, c.code, c.name, s.name AS instructor
    FROM courses c
    JOIN student_courses sc ON c.id = sc.course_id
    JOIN staff s ON c.instructor_id = s.id
    WHERE sc.student_id = ?
";
$courses_stmt = $conn->prepare($courses_query);
$courses_stmt->bind_param("i", $student_id);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();
$courses = [];
while ($course = $courses_result->fetch_assoc()) {
    $courses[] = $course;
}
$courses_stmt->close();

// Get attendance records
$attendance_query = "
    SELECT a.id, a.course_id, c.code AS course_code, c.name AS course_name, 
           a.date, a.status, a.time
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    WHERE a.student_id = ?
    ORDER BY a.date DESC
";
$attendance_stmt = $conn->prepare($attendance_query);
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
$attendance_records = [];
while ($record = $attendance_result->fetch_assoc()) {
    $attendance_records[] = $record;
}
$attendance_stmt->close();

// Get upcoming classes
$upcoming_query = "
    SELECT c.id AS course_id, c.code AS course_code, c.name AS course_name, 
           s.date, s.time, s.room
    FROM course_schedule s
    JOIN courses c ON s.course_id = c.id
    JOIN student_courses sc ON c.id = sc.course_id
    WHERE sc.student_id = ? AND s.date >= CURDATE()
    ORDER BY s.date ASC, s.time ASC
    LIMIT 4
";
$upcoming_stmt = $conn->prepare($upcoming_query);
$upcoming_stmt->bind_param("i", $student_id);
$upcoming_stmt->execute();
$upcoming_result = $upcoming_stmt->get_result();
$upcoming_classes = [];
while ($class = $upcoming_result->fetch_assoc()) {
    $upcoming_classes[] = $class;
}
$upcoming_stmt->close();

// Calculate attendance statistics
function calculateStats($records, $course_id = null) {
    $filtered_records = $records;
    if ($course_id) {
        $filtered_records = array_filter($records, function($record) use ($course_id) {
            return $record['course_id'] == $course_id;
        });
    }
    
    $total = count($filtered_records);
    $present = count(array_filter($filtered_records, function($record) {
        return $record['status'] == 'present';
    }));
    $absent = count(array_filter($filtered_records, function($record) {
        return $record['status'] == 'absent';
    }));
    $late = count(array_filter($filtered_records, function($record) {
        return $record['status'] == 'late';
    }));
    
    $present_percentage = $total > 0 ? ($present / $total) * 100 : 0;
    $absent_percentage = $total > 0 ? ($absent / $total) * 100 : 0;
    $late_percentage = $total > 0 ? ($late / $total) * 100 : 0;
    
    return [
        'total' => $total,
        'present' => $present,
        'absent' => $absent,
        'late' => $late,
        'present_percentage' => $present_percentage,
        'absent_percentage' => $absent_percentage,
        'late_percentage' => $late_percentage
    ];
}

$stats = calculateStats($attendance_records);

// Handle correction request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_correction'])) {
    $record_id = $_POST['record_id'];
    $correction_type = $_POST['correction_type'];
    $reason = $_POST['reason'];
    
    // In a real application, you would also handle file uploads here
    
    $correction_query = "
        INSERT INTO attendance_corrections (attendance_id, student_id, requested_status, reason, status)
        VALUES (?, ?, ?, ?, 'pending')
    ";
    $correction_stmt = $conn->prepare($correction_query);
    $correction_stmt->bind_param("iiss", $record_id, $student_id, $correction_type, $reason);
    
    if ($correction_stmt->execute()) {
        $success_message = 'Correction request submitted successfully!';
    } else {
        $error_message = 'Error submitting correction request: ' . $conn->error;
    }
    
    $correction_stmt->close();
}
?>

<!-- Main Content -->
<main class="flex-1 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold">Attendance Records</h1>
            <p class="text-gray-500 mt-1">
                Spring Semester 2025 • <?php echo htmlspecialchars($student['name']); ?>
            </p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="p-4 pb-2">
                    <h3 class="text-base font-medium">Total Classes</h3>
                </div>
                <div class="p-4 pt-2">
                    <div class="text-3xl font-bold"><?php echo $stats['total']; ?></div>
                    <p class="text-sm text-gray-500">Scheduled sessions</p>
                </div>
            </div>
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="p-4 pb-2">
                    <h3 class="text-base font-medium">Present</h3>
                </div>
                <div class="p-4 pt-2">
                    <div class="flex items-end gap-2">
                        <div class="text-3xl font-bold text-green-600"><?php echo $stats['present']; ?></div>
                        <div class="text-sm text-gray-500 mb-1">(<?php echo number_format($stats['present_percentage'], 1); ?>%)</div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $stats['present_percentage']; ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="p-4 pb-2">
                    <h3 class="text-base font-medium">Absent</h3>
                </div>
                <div class="p-4 pt-2">
                    <div class="flex items-end gap-2">
                        <div class="text-3xl font-bold text-red-600"><?php echo $stats['absent']; ?></div>
                        <div class="text-sm text-gray-500 mb-1">(<?php echo number_format($stats['absent_percentage'], 1); ?>%)</div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo $stats['absent_percentage']; ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="p-4 pb-2">
                    <h3 class="text-base font-medium">Late</h3>
                </div>
                <div class="p-4 pt-2">
                    <div class="flex items-end gap-2">
                        <div class="text-3xl font-bold text-yellow-600"><?php echo $stats['late']; ?></div>
                        <div class="text-sm text-gray-500 mb-1">(<?php echo number_format($stats['late_percentage'], 1); ?>%)</div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: <?php echo $stats['late_percentage']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for different views -->
        <div class="tabs">
            <div class="grid grid-cols-3 md:w-[400px] bg-gray-100 p-1 rounded-lg">
                <button class="tab-button active" data-tab="records">Records</button>
                <button class="tab-button" data-tab="calendar">Calendar</button>
                <button class="tab-button" data-tab="upcoming">Upcoming</button>
            </div>

            <!-- Records Tab -->
            <div id="records-tab" class="tab-content active mt-6 space-y-6">
                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-4 md:items-end">
                    <div class="space-y-2 flex-1">
                        <label for="course-filter" class="block text-sm font-medium text-gray-700">Course</label>
                        <select id="course-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="all">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['code'] . ' - ' . $course['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Date Range</label>
                        <div class="relative">
                            <input type="text" id="date-range" class="w-full md:w-[300px] px-3 py-2 border border-gray-300 rounded-md" placeholder="Select date range" readonly>
                            <input type="hidden" id="date-from">
                            <input type="hidden" id="date-to">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="present-filter" checked>
                                <span>Present</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="absent-filter" checked>
                                <span>Absent</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="late-filter" checked>
                                <span>Late</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Attendance Records Table -->
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Attendance Records</h3>
                        <p class="text-sm text-gray-500" id="records-count">Showing <?php echo count($attendance_records); ?> of <?php echo count($attendance_records); ?> records</p>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="rounded-md border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="attendance-table-body">
                                    <?php if (count($attendance_records) > 0): ?>
                                        <?php foreach ($attendance_records as $record): ?>
                                            <tr class="attendance-row" 
                                                data-course="<?php echo $record['course_id']; ?>" 
                                                data-date="<?php echo $record['date']; ?>" 
                                                data-status="<?php echo $record['status']; ?>">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo date('D, M j, Y', strtotime($record['date'])); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="font-medium"><?php echo htmlspecialchars($record['course_code']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($record['course_name']); ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php echo htmlspecialchars($record['time']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        <?php 
                                                            if ($record['status'] == 'present') echo 'bg-green-100 text-green-800';
                                                            elseif ($record['status'] == 'absent') echo 'bg-red-100 text-red-800';
                                                            else echo 'bg-yellow-100 text-yellow-800';
                                                        ?>">
                                                        <?php echo ucfirst($record['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <button class="request-correction px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-md" 
                                                            data-record-id="<?php echo $record['id']; ?>"
                                                            data-course-name="<?php echo htmlspecialchars($record['course_name']); ?>"
                                                            data-date="<?php echo date('M j, Y', strtotime($record['date'])); ?>">
                                                        Request Correction
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                No attendance records found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Tab -->
            <div id="calendar-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Attendance Calendar</h3>
                        <p class="text-sm text-gray-500">View your attendance by date</p>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-[350px]">
                                <div class="p-4 border rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <button id="prev-month" class="p-2 border rounded-md hover:bg-gray-50">
                                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                        </button>
                                        <h3 class="font-medium" id="current-month">April 2025</h3>
                                        <button id="next-month" class="p-2 border rounded-md hover:bg-gray-50">
                                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm mb-2">
                                        <div class="text-gray-500">Su</div>
                                        <div class="text-gray-500">Mo</div>
                                        <div class="text-gray-500">Tu</div>
                                        <div class="text-gray-500">We</div>
                                        <div class="text-gray-500">Th</div>
                                        <div class="text-gray-500">Fr</div>
                                        <div class="text-gray-500">Sa</div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 text-center" id="calendar-days">
                                        <!-- Calendar days will be populated by JavaScript -->
                                    </div>
                                    <div class="flex justify-center gap-6 mt-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                            <span class="text-sm">Present</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                            <span class="text-sm">Absent</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                            <span class="text-sm">Late</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="border rounded-lg p-6">
                                    <h3 class="text-lg font-medium mb-4" id="selected-date">April 20, 2025</h3>
                                    <div class="space-y-4" id="day-attendance-list">
                                        <!-- Day attendance will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tab -->
            <div id="upcoming-tab" class="tab-content mt-6">
                <div class="bg-white rounded-lg border shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Upcoming Classes</h3>
                        <p class="text-sm text-gray-500">Classes scheduled for the next week</p>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-4">
                            <?php if (count($upcoming_classes) > 0): ?>
                                <?php foreach ($upcoming_classes as $class): ?>
                                    <div class="flex items-start gap-4 p-4 border rounded-lg">
                                        <div class="bg-blue-100 rounded-md p-2 text-blue-600">
                                            <i data-lucide="calendar" class="h-5 w-5"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium"><?php echo htmlspecialchars($class['course_name']); ?></div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo date('D, M j, Y', strtotime($class['date'])); ?> • <?php echo htmlspecialchars($class['time']); ?>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 text-sm">
                                                <i data-lucide="map-pin" class="h-4 w-4 text-gray-500"></i>
                                                <span><?php echo htmlspecialchars($class['room']); ?></span>
                                            </div>
                                        </div>
                                        <button class="px-3 py-1 text-sm border rounded-md hover:bg-gray-50">
                                            Set Reminder
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-8">No upcoming classes scheduled.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t flex justify-between">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <i data-lucide="alert-circle" class="h-4 w-4"></i>
                            <span>Attendance is mandatory for all classes</span>
                        </div>
                        <button class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">
                            View Full Schedule
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-lg border shadow-sm mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium">Attendance Policy</h3>
                        <p class="text-sm text-gray-500">University attendance requirements</p>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-4 text-sm">
                            <p>Students are expected to attend all scheduled classes and laboratory sessions. Attendance is taken at the beginning of each class.</p>
                            <div class="space-y-2">
                                <h4 class="font-medium">Absence Policy:</h4>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Students are allowed a maximum of 3 absences per course per semester.</li>
                                    <li>Exceeding this limit may result in grade penalties or course failure.</li>
                                    <li>Medical absences require documentation within 7 days of return.</li>
                                </ul>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-medium">Tardiness:</h4>
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Arriving more than 10 minutes late is considered "late".</li>
                                    <li>Three "late" marks will count as one absence.</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="alert-circle" class="h-5 w-5 text-yellow-600 mt-0.5"></i>
                                    <div>
                                        <h4 class="font-medium text-yellow-800">Important Notice</h4>
                                        <p class="text-yellow-700">Students with attendance below 75% will not be eligible for final examinations.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-t">
                        <button class="w-full px-4 py-2 border rounded-md hover:bg-gray-50">
                            Download Complete Attendance Policy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Correction Request Modal -->
<div id="correction-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-lg font-medium">Request Attendance Correction</h2>
                    <p class="text-sm text-gray-500" id="correction-description">Submit a request to correct your attendance record.</p>
                </div>
                <button id="close-modal" class="p-1 rounded-md hover:bg-gray-100">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="attendance.php" method="POST" class="mt-6 space-y-4">
                <input type="hidden" id="record_id" name="record_id">
                
                <div class="space-y-2">
                    <label for="correction_type" class="block text-sm font-medium text-gray-700">Correction Type</label>
                    <select id="correction_type" name="correction_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="present">Mark as Present</option>
                        <option value="excused">Mark as Excused Absence</option>
                        <option value="late">Mark as Late</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Correction</label>
                    <textarea id="reason" name="reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Please provide details about why this correction is needed..."></textarea>
                </div>
                
                <div class="space-y-2">
                    <label for="evidence" class="block text-sm font-medium text-gray-700">Supporting Evidence (Optional)</label>
                    <input id="evidence" name="evidence" type="file" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">Upload any supporting documents (e.g., doctor's note, email confirmation)</p>
                </div>
                
                <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                    <button type="button" id="cancel-correction" class="px-4 py-2 border rounded-md hover:bg-gray-50">Cancel</button>
                    <button type="submit" name="submit_correction" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Calendar functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance records from PHP
        const attendanceRecords = <?php echo json_encode($attendance_records); ?>;
        
        // Filter functionality
        const courseFilter = document.getElementById('course-filter');
        const presentFilter = document.getElementById('present-filter');
        const absentFilter = document.getElementById('absent-filter');
        const lateFilter = document.getElementById('late-filter');
        const attendanceRows = document.querySelectorAll('.attendance-row');
        const recordsCount = document.getElementById('records-count');
        
        function applyFilters() {
            const selectedCourse = courseFilter.value;
            const showPresent = presentFilter.checked;
            const showAbsent = absentFilter.checked;
            const showLate = lateFilter.checked;
            
            let visibleCount = 0;
            let totalCount = attendanceRows.length;
            
            attendanceRows.forEach(row => {
                const courseId = row.getAttribute('data-course');
                const status = row.getAttribute('data-status');
                
                const courseMatch = selectedCourse === 'all' || courseId === selectedCourse;
                const statusMatch = 
                    (status === 'present' && showPresent) || 
                    (status === 'absent' && showAbsent) || 
                    (status === 'late' && showLate);
                
                if (courseMatch && statusMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            recordsCount.textContent = `Showing ${visibleCount} of ${totalCount} records`;
        }
        
        if (courseFilter) courseFilter.addEventListener('change', applyFilters);
        if (presentFilter) presentFilter.addEventListener('change', applyFilters);
        if (absentFilter) absentFilter.addEventListener('change', applyFilters);
        if (lateFilter) lateFilter.addEventListener('change', applyFilters);
        
        // Calendar functionality
        const calendarDays = document.getElementById('calendar-days');
        const selectedDateElement = document.getElementById('selected-date');
        const dayAttendanceList = document.getElementById('day-attendance-list');
        const prevMonthButton = document.getElementById('prev-month');
        const nextMonthButton = document.getElementById('next-month');
        const currentMonthElement = document.getElementById('current-month');
        
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        let selectedDate = formatDate(currentDate);
        
        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }
        
        function renderCalendar() {
            if (!calendarDays) return;
            
            const date = new Date(currentYear, currentMonth, 1);
            const monthName = date.toLocaleString('default', { month: 'long' });
            currentMonthElement.textContent = `${monthName} ${currentYear}`;
            
            calendarDays.innerHTML = '';
            
            // Add empty cells for days before the first day of the month
            const firstDayOfMonth = date.getDay();
            for (let i = 0; i < firstDayOfMonth; i++) {
                const emptyCell = document.createElement('div');
                calendarDays.appendChild(emptyCell);
            }
            
            // Add days of the month
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                // Find attendance records for this date
                const dayRecords = attendanceRecords.filter(record => record.date === dateStr);
                
                let statusClass = '';
                if (dayRecords.length > 0) {
                    if (dayRecords.some(record => record.status === 'absent')) {
                        statusClass = 'bg-red-100 text-red-800';
                    } else if (dayRecords.some(record => record.status === 'late')) {
                        statusClass = 'bg-yellow-100 text-yellow-800';
                    } else {
                        statusClass = 'bg-green-100 text-green-800';
                    }
                }
                
                const isSelected = dateStr === selectedDate;
                
                const dayElement = document.createElement('div');
                dayElement.className = `aspect-square flex items-center justify-center rounded-full cursor-pointer hover:bg-gray-100 ${statusClass} ${isSelected ? 'ring-2 ring-blue-500' : ''}`;
                dayElement.textContent = day;
                dayElement.setAttribute('data-date', dateStr);
                
                dayElement.addEventListener('click', function() {
                    selectedDate = dateStr;
                    renderCalendar(); // Re-render to update selected day
                    renderDayAttendance();
                });
                
                calendarDays.appendChild(dayElement);
            }
        }
        
        function renderDayAttendance() {
            if (!dayAttendanceList || !selectedDateElement) return;
            
            const date = new Date(selectedDate);
            selectedDateElement.textContent = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                month: 'long', 
                day: 'numeric', 
                year: 'numeric' 
            });
            
            const dayRecords = attendanceRecords.filter(record => record.date === selectedDate);
            
            dayAttendanceList.innerHTML = '';
            
            if (dayRecords.length === 0) {
                dayAttendanceList.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        No attendance records for this date
                    </div>
                `;
                return;
            }
            
            dayRecords.forEach(record => {
                const recordElement = document.createElement('div');
                recordElement.className = 'flex items-start gap-4 p-4 border rounded-lg';
                
                let statusIcon = '';
                let statusBgClass = '';
                
                if (record.status === 'present') {
                    statusIcon = '<i data-lucide="check" class="h-5 w-5 text-green-600"></i>';
                    statusBgClass = 'bg-green-100';
                } else if (record.status === 'absent') {
                    statusIcon = '<i data-lucide="x" class="h-5 w-5 text-red-600"></i>';
                    statusBgClass = 'bg-red-100';
                } else {
                    statusIcon = '<i data-lucide="clock" class="h-5 w-5 text-yellow-600"></i>';
                    statusBgClass = 'bg-yellow-100';
                }
                
                recordElement.innerHTML = `
                    <div class="p-2 rounded-full ${statusBgClass}">
                        ${statusIcon}
                    </div>
                    <div class="flex-1">
                        <div class="font-medium">${record.course_name}</div>
                        <div class="text-sm text-gray-500">${record.time}</div>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                ${record.status === 'present' ? 'bg-green-100 text-green-800' : 
                                  record.status === 'absent' ? 'bg-red-100 text-red-800' : 
                                  'bg-yellow-100 text-yellow-800'}">
                                ${record.status.charAt(0).toUpperCase() + record.status.slice(1)}
                            </span>
                        </div>
                    </div>
                `;
                
                dayAttendanceList.appendChild(recordElement);
            });
            
            // Re-initialize Lucide icons for the new content
            lucide.createIcons();
        }
        
        if (prevMonthButton) {
            prevMonthButton.addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar();
            });
        }
        
        if (nextMonthButton) {
            nextMonthButton.addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar();
            });
        }
        
        // Correction request modal
        const correctionButtons = document.querySelectorAll('.request-correction');
        const correctionModal = document.getElementById('correction-modal');
        const closeModalButton = document.getElementById('close-modal');
        const cancelCorrectionButton = document.getElementById('cancel-correction');
        const recordIdInput = document.getElementById('record_id');
        const correctionDescription = document.getElementById('correction-description');
        
        function openCorrectionModal(recordId, courseName, date) {
            if (correctionModal && recordIdInput && correctionDescription) {
                recordIdInput.value = recordId;
                correctionDescription.textContent = `Submit a request to correct your attendance record for ${courseName} on ${date}.`;
                correctionModal.classList.remove('hidden');
            }
        }
        
        function closeCorrectionModal() {
            if (correctionModal) {
                correctionModal.classList.add('hidden');
            }
        }
        
        correctionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const recordId = this.getAttribute('data-record-id');
                const courseName = this.getAttribute('data-course-name');
                const date = this.getAttribute('data-date');
                openCorrectionModal(recordId, courseName, date);
            });
        });
        
        if (closeModalButton) closeModalButton.addEventListener('click', closeCorrectionModal);
        if (cancelCorrectionButton) cancelCorrectionButton.addEventListener('click', closeCorrectionModal);
        
        // Initialize
        renderCalendar();
        renderDayAttendance();
        applyFilters();
    });
</script>

<?php
// Include footer
require_once 'includes/footer.php';
?>

