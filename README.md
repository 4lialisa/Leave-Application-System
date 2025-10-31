# 🗓️ Leave Application System

## 📘 Overview
The **Leave Application System** is a web-based platform designed to simplify and digitize the employee leave management process.  
It enables employees to **apply for leave**, **track their application status**, and allows administrators to **approve, reject, or manage leave requests** efficiently.  

This project was developed as part of the **Web Techniques and Applications (TWT2231)** course.

---

## 👥 Collaborators
| No | Student ID | Name 
|----|------|-------------
| 1 | 1211102170 | CHANG ZI JIN 
| 2 | 1211103754 | NOOR ALIA ALISA BINTI KAMAL
| 3 | 1211104144 | LIM FANG YIN
| 4 | 1211104759 | WONG YU TING

---

## 🎯 Objectives
1. 🧾 Provide a **dedicated online channel** for leave applications.  
2. ⚙️ Streamline leave application, approval, and record-keeping in a single system.  
3. 🔍 Improve **transparency** by allowing employees to track leave status and balance.  
4. ✅ Automate **validation** of leave eligibility in compliance with Malaysia’s *Employment (Amendment) Act 2022*.  
5. 🔐 Ensure **data security and access control** through login and session validation.  
6. 🔁 Enable easy **reversal** (undo, cancel, reset) actions for both employees and administrators.  

---

## 🧩 System Features

### 👩‍💼 Employee Module
- **Employee Dashboard** displaying profile, leave balance, and unpaid leave records.  
- **Leave Application Form** with validation for leave type, duration, and entitlements.  
- **Application Summary Page** confirming submission details and uploaded documents.  
- **Application History Table** to view all submitted requests and statuses.  
- **Password Reset** feature with real-time validation for strong password criteria.  
- **Cancel Pending Leave** functionality for flexibility.

### 🧑‍💼 Admin Module
- **Admin Dashboard** listing pending applications with approve/reject actions.  
- **Application History Page** with ability to **undo** approval/rejection and view **Productivity Calendar** integrated with Google Calendar API.  
- **View Employees Page** to review employee leave balances.  
- **Reset Leave Functions** — reset for a single employee or all employees (using SQL stored procedures).  
- **Password Reset Page** for admin accounts.  

### 🗄️ Database & Backend
- **Database Name:** `leaveapplicationsystem`  
- **Tables:**  
  - `employee` – stores employee info & leave balances.  
  - `application` – manages all leave requests & status.  
  - `login` – handles authentication data.  
- **Stored Procedures:**  
  - `setLeave()` – reset leave for individual employee based on years of service.  
  - `setAllLeave()` – reset leave for all employees at once.  
- **Technologies:** PHP, MySQL, HTML, CSS, JavaScript, Google Calendar API.  

---

## 🧪 Core Functionalities

### 🔐 Login System
- Username & password validation for both admin and employee.  
- Session management using cookies for authentication.  
- Prevents direct URL access without valid session tokens.  

### 🗓️ Leave Application
- Auto-calculates **paid/unpaid leave**, duration, and entitlements.  
- Validates based on *Employment Act 2022*:  
  - Annual, Medical, Hospitalisation, Maternity, Paternity, and Emergency leave.  
  - Automatic gender-specific options for maternity/paternity leave.  
  - Validation for maximum duration and employment period eligibility.  

### 📅 Google Calendar Integration
- Approved leave automatically creates events on **Google Calendar**.  
- Uses a **service account** and `google-calendar-event.php` for event creation/removal.  
- Admin can view all approved leaves on an embedded productivity calendar.

### 🔁 Leave Reset Function
- Admin can:
  - Reset **all** employees’ leave quotas.
  - Reset **individual** employee leave balances.  
- Based on *Employment (Amendment) Act 2022* entitlements.  

### 🔒 Security & Validation
- Secure login and session control.  
- Prevents unauthorised URL access.  
- Strong password policy enforcement using JavaScript validation.  
- Error handling for invalid inputs, missing cookies, or no internet connection.

---

## ⚙️ Installation & Usage

### 🔧 Requirements
- XAMPP with Apache & MySQL enabled  
- PHP 8.0 or above  
- Composer (for Google API client)  
- Web browser (Chrome, Edge, or Safari)

---

### 🚀 Setup Steps
```bash
# 1️⃣ Clone repository
git clone https://github.com/yourusername/LeaveApplicationSystem.git
Extract and put the file in "htdocs" folder (You may find the folder inside xampp folder).

# 2️⃣ Create a new folder named `attachments` inside the `leaveApplicationSystem` folder.  

# 3️⃣ Import database
Import the file `leaveapplicationsystem_sql.txt`.  

# 4️⃣ Access system
Start Apache and MySQL in XAMPP Control Panel
Go to http://localhost/leaveApplicationSystem

```
### 🧑‍💼 Default Demo Login (Local Testing Only)
| Role | Username | Password |
|------|-----------|-----------|
| Employee (demo) | G4972834 | YuTing!5678 

---

## 📸 Screenshots (Admin View)

### Note!!!
To protect **sensitive code** and **credentials**, there is some code **not uploaded** to this repository. 

Any inquiries feel free to e-mail me.

Below are screenshots of the **Admin Dashboard interface**, demonstrating the key features visually.

<p align="center">
  <strong style="font-size: 22px;">🧭 Admin Dashboard</strong><br><br>
  <em>This is the main interface for the admin after successfully logging into the system. Here, the admin can approve or reject employees’ leave applications. Before making a decision, the admin can view the employee’s full details by clicking the employee’s ID number. This interface also displays three main function buttons and a logout button.</em><br><br>
  <img width="700" height="600" alt="Admin Dashboard" src="https://github.com/user-attachments/assets/daaef91c-73a4-4507-935d-61426e92b890" />
</p>

<br>

<p align="center">
  <strong style="font-size: 22px;">📋 View Application History</strong><br><br>
  <em>This is the Application History interface where the admin can view which employees have been approved or rejected. The admin can also undo previous approval or rejection actions if needed.</em><br><br>
  <img width="700" height="600" alt="Application History" src="https://github.com/user-attachments/assets/f612f7fe-9546-4ffd-9850-2a9a1f86bedf" />
</p>

<br>

<p align="center">
  <strong style="font-size: 22px;">🔄 View Employees</strong><br><br>
  <em>This interface displays the remaining leave balance for each employee. The admin can reset leave days individually for a specific employee or click the “Reset All Leaves” button to reset every employee’s leave record at once.</em><br><br>
  <img width="700" height="600" alt="View Employees" src="https://github.com/user-attachments/assets/242f0f03-66c4-4a13-919f-d90728403ed6" />
</p>

<br>

<p align="center">
  <strong style="font-size: 22px;">📅 Productivity Calendar (Google API)</strong><br><br>
  <em>This calendar helps the admin easily track which employees are currently on leave and which are still working. It provides a clear overview of employee availability through Google Calendar integration.</em><br><br>
  <img width="700" height="600" alt="Productivity Calendar (Google API)" src="https://github.com/user-attachments/assets/7df03275-e7e0-418f-91ad-56adc8ff8f43" />
</p>
