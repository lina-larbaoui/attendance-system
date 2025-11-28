# Testing Links - Tutorial 3

## Exercise 1: Add Student (JSON)
```
http://localhost/AttendanceSystem/add_student.php
```
✔️ Check: `students.json` file created

---

## Exercise 2: Take Attendance (JSON)
```
http://localhost/AttendanceSystem/take_attendance.php
```
✔️ Check: `attendance_YYYY-MM-DD.json` file created

---

## Exercise 3: Database Connection
```
http://localhost/AttendanceSystem/test_connection.php
```
✔️ Check: "Connection successful" message

---

## Exercise 4: CRUD Operations (MySQL)

### Add Student
```
http://localhost/AttendanceSystem/add_student_db.php
```

### List Students
```
http://localhost/AttendanceSystem/list_students.php
```

### Update Student
```
Click "Edit" button in list
```

### Delete Student
```
Click "Delete" button in list
```

✔️ Check: phpMyAdmin → `attendance_db` → `students` table

---

## Exercise 5: Attendance Sessions

### Create Session
```
http://localhost/AttendanceSystem/create_session.php
```
✔️ Returns session ID

### Close Session
```
http://localhost/AttendanceSystem/close_session.php
```
✔️ Updates status to "closed"

### View All Sessions (Bonus)
```
http://localhost/AttendanceSystem/view_sessions.php
```

✔️ Check: phpMyAdmin → `attendance_db` → `attendance_sessions` table

---

## phpMyAdmin Access
```
http://localhost/phpmyadmin
```
Database: `attendance_db`  
Tables: `students`, `attendance_sessions`