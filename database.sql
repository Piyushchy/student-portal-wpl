-- Create database
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    address TEXT,
    program_id INT,
    enrollment_year VARCHAR(4),
    advisor_id INT,
    bio TEXT,
    profile_image VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create programs table
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    expected_graduation VARCHAR(4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create staff table
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    position VARCHAR(100),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT,
    instructor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create student_courses table
CREATE TABLE IF NOT EXISTS student_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester VARCHAR(20),
    grade FLOAT,
    progress INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    time VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create attendance_corrections table
CREATE TABLE IF NOT EXISTS attendance_corrections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendance_id INT NOT NULL,
    student_id INT NOT NULL,
    requested_status ENUM('present', 'excused', 'late') NOT NULL,
    reason TEXT,
    evidence VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by INT,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES attendance(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES staff(id) ON DELETE SET NULL
);

-- Create announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES staff(id) ON DELETE SET NULL
);

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time VARCHAR(50),
    location VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES staff(id) ON DELETE SET NULL
);

-- Create student_events table
CREATE TABLE IF NOT EXISTS student_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Create course_schedule table
CREATE TABLE IF NOT EXISTS course_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(50) NOT NULL,
    room VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create student_documents table
CREATE TABLE IF NOT EXISTS student_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    filesize VARCHAR(20),
    filetype VARCHAR(50),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert sample data
-- Insert programs
INSERT INTO programs (program_name, department, expected_graduation) VALUES
('Bachelor of Science in Computer Science', 'Computer Science', '2026'),
('Bachelor of Arts in English', 'English', '2026'),
('Bachelor of Science in Mathematics', 'Mathematics', '2026'),
('Bachelor of Science in Physics', 'Physics', '2026');

-- Insert staff
INSERT INTO staff (name, email, phone, position, department) VALUES
('Dr. Robert Chen', 'robert.chen@university.edu', '(555) 111-2222', 'Professor', 'Computer Science'),
('Dr. Maria Garcia', 'maria.garcia@university.edu', '(555) 222-3333', 'Professor', 'Mathematics'),
('Dr. James Wilson', 'james.wilson@university.edu', '(555) 333-4444', 'Professor', 'Physics'),
('Prof. Sarah Miller', 'sarah.miller@university.edu', '(555) 444-5555', 'Associate Professor', 'English'),
('Dr. Sarah Williams', 'sarah.williams@university.edu', '(555) 555-6666', 'Academic Advisor', 'Student Services');

-- Insert students
INSERT INTO students (student_id, name, email, phone, date_of_birth, address, program_id, enrollment_year, advisor_id, bio, password) VALUES
('ST12345', 'Alex Johnson', 'alex.johnson@university.edu', '(555) 123-4567', '1998-05-15', '123 Campus Drive, University City, State 12345', 1, '2022', 5, 'Computer Science student with interests in artificial intelligence and web development. Active member of the Coding Club and Robotics Team.', 'password123');

-- Insert courses
INSERT INTO courses (code, name, description, credits, instructor_id) VALUES
('CS101', 'Introduction to Computer Science', 'Fundamental concepts of computer science and programming.', 3, 1),
('MATH201', 'Calculus II', 'Advanced calculus concepts including integration and series.', 4, 2),
('PHYS150', 'Physics I', 'Introduction to mechanics and thermodynamics.', 4, 3),
('ENG102', 'Academic Writing', 'Principles of effective academic writing and research.', 3, 4);

-- Insert student_courses
INSERT INTO student_courses (student_id, course_id, semester, grade, progress) VALUES
(1, 1, 'Spring 2025', 3.8, 75),
(1, 2, 'Spring 2025', 3.5, 60),
(1, 3, 'Spring 2025', 4.0, 90),
(1, 4, 'Spring 2025', 3.7, 85);

-- Insert attendance records
INSERT INTO attendance (student_id, course_id, date, status, time) VALUES
(1, 1, '2025-04-01', 'present', '09:00 AM - 10:30 AM'),
(1, 1, '2025-04-03', 'present', '09:00 AM - 10:30 AM'),
(1, 1, '2025-04-08', 'absent', '09:00 AM - 10:30 AM'),
(1, 1, '2025-04-10', 'present', '09:00 AM - 10:30 AM'),
(1, 1, '2025-04-15', 'present', '09:00 AM - 10:30 AM'),
(1, 2, '2025-04-02', 'present', '11:00 AM - 12:30 PM'),
(1, 2, '2025-04-04', 'late', '11:00 AM - 12:30 PM'),
(1, 2, '2025-04-09', 'present', '11:00 AM - 12:30 PM'),
(1, 2, '2025-04-11', 'absent', '11:00 AM - 12:30 PM'),
(1, 2, '2025-04-16', 'present', '11:00 AM - 12:30 PM'),
(1, 3, '2025-04-02', 'present', '02:00 PM - 03:30 PM'),
(1, 3, '2025-04-04', 'present', '02:00 PM - 03:30 PM'),
(1, 3, '2025-04-09', 'present', '02:00 PM - 03:30 PM'),
(1, 3, '2025-04-11', 'late', '02:00 PM - 03:30 PM'),
(1, 3, '2025-04-16', 'present', '02:00 PM - 03:30 PM'),
(1, 4, '2025-04-01', 'present', '03:30 PM - 05:00 PM'),
(1, 4, '2025-04-03', 'absent', '03:30 PM - 05:00 PM'),
(1, 4, '2025-04-08', 'present', '03:30 PM - 05:00 PM'),
(1, 4, '2025-04-10', 'present', '03:30 PM - 05:00 PM'),
(1, 4, '2025-04-15', 'present', '03:30 PM - 05:00 PM');

-- Insert announcements
INSERT INTO announcements (title, content, created_by) VALUES
('End of Semester Exams', 'Final exams will begin on May 15th. Please check your timetable for details.', 5),
('Library Hours Extended', 'The university library will remain open until midnight during exam week.', 5),
('Summer Registration Open', 'Registration for summer courses is now open. Early registration ends April 30th.', 5);

-- Insert events
INSERT INTO events (title, description, event_date, event_time, location, created_by) VALUES
('Math Assignment Due', 'Calculus II assignment on integration techniques', '2025-04-20', '11:59 PM', 'Online Submission', 2),
('Physics Lab', 'Laboratory session on thermodynamics', '2025-04-21', '2:00 PM', 'Science Building Room 110', 3),
('Study Group Meeting', 'Computer Science study group for final exam preparation', '2025-04-28', '4:30 PM', 'Library Study Room 3', 1),
('Career Fair', 'Annual university career fair with industry representatives', '2025-05-02', '10:00 AM', 'Student Center', 5);

-- Insert student_events
INSERT INTO student_events (student_id, event_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4);

-- Insert course_schedule
INSERT INTO course_schedule (course_id, date, time, room) VALUES
(1, '2025-04-22', '09:00 AM - 10:30 AM', 'Tech Building 101'),
(2, '2025-04-23', '11:00 AM - 12:30 PM', 'Science Hall 205'),
(3, '2025-04-23', '02:00 PM - 03:30 PM', 'Science Hall 110'),
(4, '2025-04-22', '03:30 PM - 05:00 PM', 'Humanities 305');

-- Insert student_documents
INSERT INTO student_documents (student_id, filename, filepath, filesize, filetype) VALUES
(1, 'Transcript_Spring2025.pdf', '/documents/ST12345/Transcript_Spring2025.pdf', '1.2 MB', 'application/pdf'),
(1, 'Financial_Aid_Form.pdf', '/documents/ST12345/Financial_Aid_Form.pdf', '850 KB', 'application/pdf'),
(1, 'Course_Registration.pdf', '/documents/ST12345/Course_Registration.pdf', '1.5 MB', 'application/pdf');

