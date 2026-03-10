<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* (Keep all your existing styles – unchanged) */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
       
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #1a252f 100%);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
       
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            border-radius: 0;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
       
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--secondary-color);
        }
       
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
       
        .main-content {
            padding: 30px;
        }
       
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
       
        .card:hover {
            transform: translateY(-5px);
        }
       
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
       
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
       
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
       
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
       
        .student-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
       
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
       
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
       
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
       
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px;
        }
       
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
       
        .modal-content {
            border-radius: 20px;
            border: none;
        }
       
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 20px 20px 0 0;
        }
       
        .fee-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
       
        .fee-paid {
            background-color: #d4edda;
            color: #155724;
        }
       
        .fee-pending {
            background-color: #fff3cd;
            color: #856404;
        }
       
        .fee-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
       
        .photo-upload {
            border: 3px dashed #ddd;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
       
        .photo-upload:hover {
            border-color: var(--secondary-color);
            background-color: #f8f9fa;
        }
       
        .id-upload {
            border: 3px dashed #ddd;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #fafafa;
        }
       
        .id-upload:hover {
            border-color: var(--success-color);
            background-color: #f0f8f0;
        }
       
        .id-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: none;
        }
       
        .upload-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
       
        .upload-text {
            color: #6c757d;
            font-weight: 500;
        }
       
        .file-info {
            margin-top: 10px;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 8px;
            display: none;
        }
       
        .search-box {
            position: relative;
        }
       
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
       
        .search-box input {
            padding-left: 45px;
            border-radius: 25px;
        }
       
        .badge-course {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
       
        .progress-fee {
            height: 8px;
            border-radius: 10px;
        }
       
        .doc-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
       
        .doc-badge i {
            margin-right: 5px;
        }
       
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            .student-photo {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4 text-center border-bottom border-secondary">
                    <h4 class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i>EduManager</h4>
                    <small class="text-muted">Student Management</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link active" href="#" onclick="showSection('dashboard',event)" data-section="dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('students',event)" data-section="students">
                        <i class="bi bi-people-fill"></i> Students
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('admission',event)" data-section="admission">
                        <i class="bi bi-person-plus-fill"></i> Admission
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('courses',event)" data-section="courses">
                        <i class="bi bi-book-fill"></i> Courses
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('batches',event)" data-section="batches">
                        <i class="bi bi-collection-fill"></i> Batches
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('fees',event)" data-section="fees">
                        <i class="bi bi-cash-stack"></i> Fee Management
                    </a>
                    <a class="nav-link" href="#" onclick="showSection('reports',event)" data-section="reports">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">Dashboard</h2>
                        <button class="btn btn-primary btn-custom" onclick="showSection('admission')">
                            <i class="bi bi-plus-lg me-2"></i>New Admission
                        </button>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-2 opacity-75">Total Students</h6>
                                            <h3 class="mb-0" id="totalStudents">0</h3>
                                        </div>
                                        <i class="bi bi-people-fill fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-2 opacity-75">Active Courses</h6>
                                            <h3 class="mb-0" id="totalCourses">0</h3>
                                        </div>
                                        <i class="bi bi-book-fill fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-2 opacity-75">Pending Fees</h6>
                                            <h3 class="mb-0" id="pendingFees">0</h3>
                                        </div>
                                        <i class="bi bi-exclamation-triangle-fill fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card info h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-2 opacity-75">Revenue</h6>
                                            <h3 class="mb-0" id="totalRevenue">$0</h3>
                                        </div>
                                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h5 class="fw-bold">Recent Admissions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Course</th>
                                                    <th>Date</th>
                                                    <th>ID Doc</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recentAdmissions">
                                                <!-- Dynamic content -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h5 class="fw-bold">Fee Collection</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="feeChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Students Section -->
                <div id="students-section" class="section-content" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">Student Management</h2>
                        <div class="d-flex gap-2">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" id="searchStudent" placeholder="Search students..." onkeyup="searchStudents()">
                            </div>
                            <button class="btn btn-primary btn-custom" onclick="showSection('admission')">
                                <i class="bi bi-plus-lg me-2"></i>Add Student
                            </button>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Photo</th>
                                        <th>Student Info</th>
                                        <th>Course & Batch</th>
                                        <th>Contact</th>
                                        <th>ID Document</th>
                                        <th>Fee Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Admission Section -->
                <div id="admission-section" class="section-content" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">New Admission</h2>
                        <button class="btn btn-outline-secondary btn-custom" onclick="showSection('students')">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body p-4">
                            <form id="admissionForm" onsubmit="handleAdmission(event)">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-4">
                                        <div class="photo-upload" onclick="document.getElementById('photoInput').click()">
                                            <img id="photoPreview" src="https://via.placeholder.com/150" class="student-photo mb-3" style="display:none;">
                                            <div id="uploadPlaceholder">
                                                <i class="bi bi-camera fs-1 text-muted"></i>
                                                <p class="text-muted mt-2">Click to upload photo</p>
                                            </div>
                                            <input type="file" id="photoInput" accept="image/*" style="display:none;" onchange="previewPhoto(event)">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">First Name</label>
                                                <input type="text" class="form-control" name="firstName" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Last Name</label>
                                                <input type="text" class="form-control" name="lastName" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Email</label>
                                                <input type="email" class="form-control" name="email" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Phone</label>
                                                <input type="tel" class="form-control" name="phone" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Date of Birth</label>
                                                <input type="date" class="form-control" name="dob" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Gender</label>
                                                <select class="form-select" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Select Course</label>
                                        <select class="form-select" name="course" id="courseSelect" onchange="updateFeeStructure()" required>
                                            <option value="">Choose Course</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Select Batch</label>
                                        <select class="form-select" name="batch" id="batchSelect" required>
                                            <option value="">Choose Batch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Address</label>
                                        <textarea class="form-control" name="address" rows="3" required></textarea>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <!-- ID Document Upload Section -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <h5 class="fw-bold text-primary mb-3">
                                            <i class="bi bi-card-text me-2"></i>ID Document Upload
                                        </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ID Document Type</label>
                                        <select class="form-select" name="idType" id="idTypeSelect" required>
                                            <option value="">Select ID Type</option>
                                            <option value="passport">Passport</option>
                                            <option value="national_id">National ID Card</option>
                                            <option value="drivers_license">Driver's License</option>
                                            <option value="student_id">Student ID Card</option>
                                            <option value="birth_certificate">Birth Certificate</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ID Number</label>
                                        <input type="text" class="form-control" name="idNumber" id="idNumberInput" placeholder="Enter ID document number" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Upload ID Document</label>
                                        <div class="id-upload" onclick="document.getElementById('idDocumentInput').click()">
                                            <input type="file" id="idDocumentInput" name="idDocument" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="previewIdDocument(event)" required>
                                            <div id="idUploadContent">
                                                <i class="bi bi-file-earmark-text upload-icon"></i>
                                                <p class="upload-text mb-1">Click to upload ID document</p>
                                                <small class="text-muted">Accepted: PDF, JPG, PNG (Max 2MB)</small>
                                            </div>
                                            <img id="idPreview" class="id-preview mt-2" alt="ID Preview">
                                            <div id="idFileInfo" class="file-info">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span id="idFileName"></span>
                                                <small class="text-muted d-block mt-1" id="idFileSize"></small>
                                            </div>
                                        </div>
                                        <div class="form-text text-muted mt-2">
                                            <i class="bi bi-shield-check me-1"></i>
                                            Document will be securely stored and encrypted. Only authorized personnel can access.
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="row g-3" id="feeSection">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Total Fee</label>
                                        <input type="number" class="form-control" name="totalFee" id="totalFee" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Paid Amount</label>
                                        <input type="number" class="form-control" name="paidAmount" id="paidAmount" onchange="calculateBalance()" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Balance</label>
                                        <input type="number" class="form-control" name="balance" id="balance" readonly>
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-secondary btn-custom me-2" onclick="resetForm()">Reset</button>
                                    <button type="submit" class="btn btn-primary btn-custom">Submit Admission</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Courses Section -->
                <div id="courses-section" class="section-content" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">Course Management</h2>
                        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#courseModal">
                            <i class="bi bi-plus-lg me-2"></i>Add Course
                        </button>
                    </div>
                    <div class="row g-4" id="coursesGrid">
                        <!-- Dynamic content -->
                    </div>
                </div>
                <!-- Batches Section -->
                <div id="batches-section" class="section-content" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">Batch Management</h2>
                        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#batchModal">
                            <i class="bi bi-plus-lg me-2"></i>Create Batch
                        </button>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch Name</th>
                                        <th>Course</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Capacity</th>
                                        <th>Enrolled</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="batchesTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Fees Section -->
                <div id="fees-section" class="section-content" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold text-primary">Fee Management</h2>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-custom" onclick="exportFeeReport()">
                                <i class="bi bi-download me-2"></i>Export Report
                            </button>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Collected</h5>
                                    <h3 id="collectedFees">$0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5>Pending</h5>
                                    <h3 id="pendingFeesTotal">$0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5>Overdue</h5>
                                    <h3 id="overdueFees">$0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Total Fee</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="feesTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Reports Section -->
                <div id="reports-section" class="section-content" style="display:none;">
                    <h2 class="fw-bold text-primary mb-4">Reports & Analytics</h2>
                   
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Monthly Admissions</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="admissionChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Course Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="courseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Course Modal -->
    <div class="modal fade" id="courseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="courseForm" onsubmit="saveCourse(event)">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="courseName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (Months)</label>
                            <input type="number" class="form-control" name="duration" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Fee ( ₹)</label>
                            <input type="number" class="form-control" name="courseFee" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Batch Modal -->
    <div class="modal fade" id="batchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Batch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="batchForm" onsubmit="saveBatch(event)">
                        <div class="mb-3">
                            <label class="form-label">Batch Name</label>
                            <input type="text" class="form-control" name="batchName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select class="form-select" name="courseId" id="batchCourseSelect" required>
                                <option value="">Choose Course</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Batch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" onsubmit="recordPayment(event)">
                        <input type="hidden" id="paymentStudentId">
                        <div class="mb-3">
                            <label class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="paymentStudentName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Balance</label>
                            <input type="text" class="form-control" id="currentBalance" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Amount</label>
                            <input type="number" class="form-control" name="paymentAmount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" name="paymentDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="paymentMethod" required>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Online">Online Payment</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Record Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ID Document View Modal -->
    <div class="modal fade" id="idViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-card-text me-2"></i>ID Document Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Document Type:</strong> <span id="viewIdType"></span></p>
                            <p><strong>ID Number:</strong> <span id="viewIdNumber"></span></p>
                            <p><strong>Upload Date:</strong> <span id="viewUploadDate"></span></p>
                        </div>
                        <div class="col-md-6 text-center">
                            <img id="viewIdImage" class="img-fluid rounded shadow" style="max-height: 300px;" alt="ID Document">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadIdDocument()">
                        <i class="bi bi-download me-2"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Storage
        let students = JSON.parse(localStorage.getItem('students')) || [];
        let courses = JSON.parse(localStorage.getItem('courses')) || [
            { id: 1, name: 'Computer Science', duration: 24, fee: 5000, description: 'Bachelor of Computer Science' },
            { id: 2, name: 'Business Administration', duration: 18, fee: 4500, description: 'MBA Program' },
            { id: 3, name: 'Data Science', duration: 12, fee: 6000, description: 'Data Science Certification' }
        ];
        let batches = JSON.parse(localStorage.getItem('batches')) || [
            { id: 1, name: 'CS-2024-A', courseId: 1, startDate: '2024-01-15', endDate: '2025-01-15', capacity: 30, enrolled: 0 },
            { id: 2, name: 'MBA-2024-B', courseId: 2, startDate: '2024-02-01', endDate: '2025-08-01', capacity: 25, enrolled: 0 }
        ];

        // ---------- FIX: Image compression helper ----------
        async function compressImage(dataUrl, maxWidth = 300, maxHeight = 300, quality = 0.7) {
            return new Promise((resolve) => {
                let img = new Image();
                img.src = dataUrl;
                img.onload = () => {
                    let canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/jpeg', quality));
                };
            });
        }

        // ---------- FIX: Photo preview with compression ----------
        window.previewPhoto = async function(event) {
            let file = event.target.files[0];
            if(file) {
                let reader = new FileReader();
                reader.onload = async function(e) {
                    // Compress the image before displaying
                    let compressed = await compressImage(e.target.result);
                    document.getElementById('photoPreview').src = compressed;
                    document.getElementById('photoPreview').style.display = 'block';
                    document.getElementById('uploadPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        };

        // ---------- FIX: ID Document preview with size limit 2MB ----------
        window.previewIdDocument = function(event) {
            let file = event.target.files[0];
            if (!file) return;

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB limit. Please choose a smaller file.');
                event.target.value = '';
                return;
            }

            // Validate file type
            let allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Please upload PDF, JPG, or PNG files only.');
                event.target.value = '';
                return;
            }

            let reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('idPreview');
                let fileInfo = document.getElementById('idFileInfo');
                let uploadContent = document.getElementById('idUploadContent');

                if (file.type.startsWith('image/')) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                } else {
                    // For PDF, show icon instead of preview
                    preview.style.display = 'none';
                    uploadContent.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger fs-1"></i><p class="mt-2 mb-0">PDF Document Selected</p>';
                }

                // Show file info
                document.getElementById('idFileName').textContent = file.name;
                document.getElementById('idFileSize').textContent = formatFileSize(file.size);
                fileInfo.style.display = 'block';

                // Store file data
                document.getElementById('idDocumentInput').dataset.fileData = e.target.result;
                document.getElementById('idDocumentInput').dataset.fileName = file.name;
                document.getElementById('idDocumentInput').dataset.fileType = file.type;
            };
            reader.readAsDataURL(file);
        };

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            let k = 1024;
            let sizes = ['Bytes', 'KB', 'MB', 'GB'];
            let i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboard();
            renderStudents();
            renderCourses();
            renderBatches();
            renderFees();
            updateCourseSelects();
            initCharts();
        });

        // Navigation
        window.showSection = function(sectionName, event = null) {
            document.querySelectorAll('.section-content').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(sectionName + '-section').style.display = 'block';

            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
            });
            let activeLink = document.querySelector(`.sidebar .nav-link[data-section="${sectionName}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }

            if(sectionName === 'dashboard') updateDashboard();
            if(sectionName === 'students') renderStudents();
            if(sectionName === 'fees') renderFees();
        };

        // Dashboard Functions
        function updateDashboard() {
            document.getElementById('totalStudents').textContent = students.length;
            document.getElementById('totalCourses').textContent = courses.length;

            let pendingCount = students.filter(s => s.balance > 0).length;
            document.getElementById('pendingFees').textContent = pendingCount;

            let totalRevenue = students.reduce((sum, s) => sum + parseFloat(s.paidAmount || 0), 0);
            document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toLocaleString();

            // Recent admissions
            let recentHtml = '';
            students.slice(-5).reverse().forEach(student => {
                let course = courses.find(c => c.id == student.course) || { name: 'Unknown' };
                let hasIdDoc = student.idDocument ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>' : '<span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Pending</span>';
                recentHtml += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${student.photo || 'https://via.placeholder.com/40'}" class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <div class="fw-bold">${student.firstName} ${student.lastName}</div>
                                    <small class="text-muted">${student.email}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge-course">${course.name}</span></td>
                        <td>${new Date(student.admissionDate).toLocaleDateString()}</td>
                        <td>${hasIdDoc}</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                `;
            });
            document.getElementById('recentAdmissions').innerHTML = recentHtml || '<tr><td colspan="5" class="text-center">No recent admissions</td></tr>';
        }

        // Student Management
        function renderStudents() {
            let html = '';
            students.forEach((student, index) => {
                let course = courses.find(c => c.id == student.course) || { name: 'Unknown' };
                let batch = batches.find(b => b.id == student.batch) || { name: 'Unknown' };
                let feeStatus = student.balance == 0 ? 'paid' : (student.balance > 0 ? 'pending' : 'overdue');

                // ID Document badge
                let idDocBadge = '';
                if (student.idDocument) {
                    idDocBadge = `<span class="doc-badge" style="cursor:pointer" onclick="viewIdDocument(${index})">
                        <i class="bi bi-card-text"></i> ${student.idType ? student.idType.replace('_', ' ').toUpperCase() : 'ID'}
                    </span>`;
                } else {
                    idDocBadge = '<span class="badge bg-secondary">No ID</span>';
                }

                html += `
                    <tr>
                        <td>
                            <img src="${student.photo || 'https://via.placeholder.com/120'}" class="student-photo" style="width:60px;height:60px;">
                        </td>
                        <td>
                            <div class="fw-bold">${student.firstName} ${student.lastName}</div>
                            <small class="text-muted">${student.email}</small>
                            <div class="small text-muted">ID: STD${String(index + 1).padStart(3, '0')}</div>
                        </td>
                        <td>
                            <div class="badge-course mb-1">${course.name}</div>
                            <div class="small text-muted"><i class="bi bi-people me-1"></i>${batch.name}</div>
                        </td>
                        <td>
                            <div><i class="bi bi-telephone me-1"></i>${student.phone}</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>${student.address}</div>
                        </td>
                        <td>
                            ${idDocBadge}
                            ${student.idNumber ? `<div class="small text-muted mt-1">No: ${student.idNumber}</div>` : ''}
                        </td>
                        <td>
                            <span class="fee-status fee-${feeStatus}">
                                ${feeStatus === 'paid' ? 'Paid' : feeStatus === 'pending' ? 'Pending' : 'Overdue'}
                            </span>
                            <div class="small mt-1">Balance: ₹${student.balance}</div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1" onclick="editStudent(${index})"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger me-1" onclick="deleteStudent(${index})"><i class="bi bi-trash"></i></button>
                            <button class="btn btn-sm btn-success" onclick="openPaymentModal(${index})"><i class="bi bi-cash"></i></button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('studentsTableBody').innerHTML = html || '<tr><td colspan="7" class="text-center py-5">No students found</td></tr>';
        }

        window.viewIdDocument = function(index) {
            let student = students[index];
            if (!student.idDocument) return;

            document.getElementById('viewIdType').textContent = student.idType ? student.idType.replace('_', ' ').toUpperCase() : 'N/A';
            document.getElementById('viewIdNumber').textContent = student.idNumber || 'N/A';
            document.getElementById('viewUploadDate').textContent = new Date(student.admissionDate).toLocaleDateString();

            let img = document.getElementById('viewIdImage');
            if (student.idDocumentType === 'application/pdf') {
                img.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjZGMzNTQ1IiB2aWV3Qm94PSIwIDAgMTYgMTYiPjxwYXRoIGQ9Ik00IDBoOGEyIDIgMCAwIDEgMiAydjEyaC0xMmEyIDIgMCAwIDEgMi0ydj0xMGEyIDIgMCAwIDEgMi0yem0yIDVhMSAxIDAgMCAwIDAgMnY1YTEgMSAwIDAgMCAxIDFoN2ExIDEgMCAwIDAgMC0yaC0zdi01YTEgMSAwIDAgMC0xLTF6Ii8+PC9zdmc+';
                img.style.width = '100px';
            } else {
                img.src = student.idDocument;
                img.style.width = 'auto';
            }

            // Store current document for download
            document.getElementById('idViewModal').dataset.currentDoc = JSON.stringify({
                data: student.idDocument,
                name: student.idFileName || 'id_document',
                type: student.idDocumentType || 'image/jpeg'
            });

            new bootstrap.Modal(document.getElementById('idViewModal')).show();
        };

        window.downloadIdDocument = function() {
            let docData = JSON.parse(document.getElementById('idViewModal').dataset.currentDoc);
            let link = document.createElement('a');
            link.href = docData.data;
            link.download = docData.name;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        window.searchStudents = function() {
            let query = document.getElementById('searchStudent').value.toLowerCase();
            let rows = document.querySelectorAll('#studentsTableBody tr');
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        };

        window.deleteStudent = function(index) {
            if(confirm('Are you sure you want to delete this student?')) {
                students.splice(index, 1);
                localStorage.setItem('students', JSON.stringify(students));
                renderStudents();
                updateDashboard();
            }
        };

        window.editStudent = function(index) {
            let student = students[index];
            document.querySelector('[name="firstName"]').value = student.firstName;
            document.querySelector('[name="lastName"]').value = student.lastName;
            document.querySelector('[name="email"]').value = student.email;
            document.querySelector('[name="phone"]').value = student.phone;
            document.querySelector('[name="course"]').value = student.course;
            document.querySelector('[name="batch"]').value = student.batch;
            document.querySelector('[name="dob"]').value = student.dob;
            document.querySelector('[name="gender"]').value = student.gender;
            document.querySelector('[name="address"]').value = student.address;
            document.querySelector('[name="totalFee"]').value = student.totalFee;
            document.querySelector('[name="paidAmount"]').value = student.paidAmount;
            document.querySelector('[name="balance"]').value = student.balance;
            if (student.idType) document.getElementById('idTypeSelect').value = student.idType;
            if (student.idNumber) document.getElementById('idNumberInput').value = student.idNumber;

            // Show existing photo
            if (student.photo) {
                document.getElementById('photoPreview').src = student.photo;
                document.getElementById('photoPreview').style.display = 'block';
                document.getElementById('uploadPlaceholder').style.display = 'none';
            }

            showSection('admission');
            document.getElementById('admissionForm').dataset.editIndex = index;
        };

        // Admission Form
        function updateCourseSelects() {
            let options = '<option value="">Choose Course</option>';
            courses.forEach(course => {
                options += `<option value="${course.id}" data-fee="${course.fee}">${course.name}</option>`;
            });
            document.getElementById('courseSelect').innerHTML = options;
            document.getElementById('batchCourseSelect').innerHTML = options;

            let batchOptions = '<option value="">Choose Batch</option>';
            batches.forEach(batch => {
                batchOptions += `<option value="${batch.id}">${batch.name}</option>`;
            });
            document.getElementById('batchSelect').innerHTML = batchOptions;
        }

        window.updateFeeStructure = function() {
            let select = document.getElementById('courseSelect');
            let fee = select.options[select.selectedIndex]?.dataset.fee || 0;
            document.getElementById('totalFee').value = fee;
            calculateBalance();
        };

        window.calculateBalance = function() {
            let total = parseFloat(document.getElementById('totalFee').value) || 0;
            let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
            document.getElementById('balance').value = total - paid;
        };

        // ---------- FIX: Main admission handler with error handling and compression ----------
        window.handleAdmission = async function(e) {
            e.preventDefault();
            let formData = new FormData(e.target);

            // Get ID document data
            let idInput = document.getElementById('idDocumentInput');
            let idDocumentData = idInput.dataset.fileData || null;
            let idFileName = idInput.dataset.fileName || null;
            let idFileType = idInput.dataset.fileType || null;

            // Photo may have been compressed already in preview
            let photoSrc = document.getElementById('photoPreview').src;
            // If photo is still placeholder, set to empty
            if (photoSrc === 'https://via.placeholder.com/150' || photoSrc.includes('placeholder')) {
                photoSrc = '';
            }

            let student = {
                id: Date.now(),
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                dob: formData.get('dob'),
                gender: formData.get('gender'),
                course: formData.get('course'),
                batch: formData.get('batch'),
                address: formData.get('address'),
                totalFee: formData.get('totalFee'),
                paidAmount: formData.get('paidAmount'),
                balance: formData.get('balance'),
                photo: photoSrc,
                idType: formData.get('idType'),
                idNumber: formData.get('idNumber'),
                idDocument: idDocumentData,
                idFileName: idFileName,
                idDocumentType: idFileType,
                admissionDate: new Date().toISOString()
            };

            let editIndex = e.target.dataset.editIndex;
            let selectedBatch = null;

            if (editIndex !== undefined) {
                // Preserve existing ID document if not uploading new one
                if (!idDocumentData && students[editIndex].idDocument) {
                    student.idDocument = students[editIndex].idDocument;
                    student.idFileName = students[editIndex].idFileName;
                    student.idDocumentType = students[editIndex].idDocumentType;
                }
                students[editIndex] = { ...students[editIndex], ...student };
                delete e.target.dataset.editIndex;
            } else {
                students.push(student);
                // Update batch enrolled count for new admissions
                selectedBatch = batches.find(b => b.id == student.batch);
                if (selectedBatch) {
                    selectedBatch.enrolled++;
                }
            }

            // Save to localStorage with error handling
            try {
                localStorage.setItem('students', JSON.stringify(students));
                if (selectedBatch) {
                    localStorage.setItem('batches', JSON.stringify(batches));
                }
            } catch (err) {
                // If save fails, revert changes
                if (editIndex === undefined) {
                    students.pop(); // remove newly added student
                    if (selectedBatch) {
                        selectedBatch.enrolled--; // revert batch count
                        localStorage.setItem('batches', JSON.stringify(batches));
                    }
                }
                alert('Unable to save student data. The files may be too large. Please use smaller images or try again.');
                return;
            }

            alert('Admission successful!');
            resetForm();
            showSection('students');
            updateDashboard();
        };

        window.resetForm = function() {
            document.getElementById('admissionForm').reset();
            document.getElementById('photoPreview').style.display = 'none';
            document.getElementById('uploadPlaceholder').style.display = 'block';

            // Reset ID upload
            document.getElementById('idPreview').style.display = 'none';
            document.getElementById('idFileInfo').style.display = 'none';
            document.getElementById('idUploadContent').innerHTML = `
                <i class="bi bi-file-earmark-text upload-icon"></i>
                <p class="upload-text mb-1">Click to upload ID document</p>
                <small class="text-muted">Accepted: PDF, JPG, PNG (Max 2MB)</small>
            `;
            delete document.getElementById('admissionForm').dataset.editIndex;
        };

        // Course Management
        function renderCourses() {
            let html = '';
            courses.forEach(course => {
                html += `
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-book fs-2 text-primary"></i>
                                    </div>
                                    <span class="badge bg-success"> ₹{course.fee}</span>
                                </div>
                                <h5 class="card-title">${course.name}</h5>
                                <p class="card-text text-muted">${course.description}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-muted"><i class="bi bi-clock me-1"></i>${course.duration} Months</span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editCourse(${course.id})">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('coursesGrid').innerHTML = html;
        }

        window.saveCourse = function(e) {
            e.preventDefault();
            let formData = new FormData(e.target);
            let course = {
                id: Date.now(),
                name: formData.get('courseName'),
                duration: formData.get('duration'),
                fee: formData.get('courseFee'),
                description: formData.get('description')
            };
            courses.push(course);
            localStorage.setItem('courses', JSON.stringify(courses));
            renderCourses();
            updateCourseSelects();
            bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide();
            e.target.reset();
        };

        // Batch Management
        function renderBatches() {
            let html = '';
            batches.forEach(batch => {
                let course = courses.find(c => c.id == batch.courseId) || { name: 'Unknown' };
                let status = batch.enrolled >= batch.capacity ? 'Full' : 'Open';
                let statusClass = batch.enrolled >= batch.capacity ? 'danger' : 'success';

                html += `
                    <tr>
                        <td class="fw-bold">${batch.name}</td>
                        <td>${course.name}</td>
                        <td>${batch.startDate}</td>
                        <td>${batch.endDate}</td>
                        <td>${batch.capacity}</td>
                        <td>${batch.enrolled}</td>
                        <td><span class="badge bg-${statusClass}">${status}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('batchesTableBody').innerHTML = html;
        }

        window.saveBatch = function(e) {
            e.preventDefault();
            let formData = new FormData(e.target);
            let batch = {
                id: Date.now(),
                name: formData.get('batchName'),
                courseId: formData.get('courseId'),
                startDate: formData.get('startDate'),
                endDate: formData.get('endDate'),
                capacity: formData.get('capacity'),
                enrolled: 0
            };
            batches.push(batch);
            localStorage.setItem('batches', JSON.stringify(batches));
            renderBatches();
            updateCourseSelects();
            bootstrap.Modal.getInstance(document.getElementById('batchModal')).hide();
            e.target.reset();
        };

        // Fee Management
        function renderFees() {
            let collected = 0, pending = 0, overdue = 0;
            let html = '';

            students.forEach((student, index) => {
                let course = courses.find(c => c.id == student.course) || { name: 'Unknown' };
                let status = student.balance == 0 ? 'paid' : (student.balance > 0 ? 'pending' : 'overdue');
                let total = parseFloat(student.totalFee) || 0;
                let paid = parseFloat(student.paidAmount) || 0;
                let balance = parseFloat(student.balance) || 0;

                collected += paid;
                if(balance > 0) pending += balance;

                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${student.photo || 'https://via.placeholder.com/40'}" class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <div class="fw-bold">${student.firstName} ${student.lastName}</div>
                                </div>
                            </div>
                        </td>
                        <td>${course.name}</td>
                        <td>₹{total}</td>
                        <td> ₹{paid}</td>
                        <td class="fw-bold ${balance > 0 ? 'text-danger' : 'text-success'}"> ₹{balance}</td>
                        <td><span class="fee-status fee-${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span></td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="openPaymentModal(${index})">Record Payment</button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('feesTableBody').innerHTML = html || '<tr><td colspan="7" class="text-center">No fee records</td></tr>';
            document.getElementById('collectedFees').textContent = '₹' + collected.toLocaleString();
            document.getElementById('pendingFeesTotal').textContent = '₹' + pending.toLocaleString();
            document.getElementById('overdueFees').textContent = '₹ ' + overdue.toLocaleString();
        }

        window.openPaymentModal = function(index) {
            let student = students[index];
            document.getElementById('paymentStudentId').value = index;
            document.getElementById('paymentStudentName').value = student.firstName + ' ' + student.lastName;
            document.getElementById('currentBalance').value = '$' + student.balance;
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        };

        window.recordPayment = function(e) {
            e.preventDefault();
            let index = document.getElementById('paymentStudentId').value;
            let amount = parseFloat(e.target.paymentAmount.value);
            let student = students[index];

            student.paidAmount = parseFloat(student.paidAmount) + amount;
            student.balance = parseFloat(student.totalFee) - student.paidAmount;

            localStorage.setItem('students', JSON.stringify(students));
            renderFees();
            renderStudents();
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            e.target.reset();
            alert('Payment recorded successfully!');
        };

        window.exportFeeReport = function() {
            let csv = 'Student Name,Course,Total Fee,Paid,Balance,Status,ID Type,ID Number\n';
            students.forEach(student => {
                let course = courses.find(c => c.id == student.course) || { name: 'Unknown' };
                let status = student.balance == 0 ? 'Paid' : 'Pending';
                let idType = student.idType ? student.idType.replace('_', ' ').toUpperCase() : 'N/A';
                csv += `${student.firstName} ${student.lastName},${course.name},${student.totalFee},${student.paidAmount},${student.balance},${status},${idType},${student.idNumber || 'N/A'}\n`;
            });

            let blob = new Blob([csv], { type: 'text/csv' });
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'fee_report.csv';
            a.click();
        };

        // Charts
        function initCharts() {
            // Fee Chart
            let feeCtx = document.getElementById('feeChart').getContext('2d');
            new Chart(feeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Collected', 'Pending'],
                    datasets: [{
                        data: [75, 25],
                        backgroundColor: ['#27ae60', '#f39c12']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Admission Chart
            let admCtx = document.getElementById('admissionChart').getContext('2d');
            new Chart(admCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Admissions',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#3498db',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Course Chart
            let courseCtx = document.getElementById('courseChart').getContext('2d');
            new Chart(courseCtx, {
                type: 'bar',
                data: {
                    labels: courses.map(c => c.name),
                    datasets: [{
                        label: 'Students',
                        data: courses.map(c => students.filter(s => s.course == c.id).length),
                        backgroundColor: '#667eea'
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }
    </script>
</body>
</html>