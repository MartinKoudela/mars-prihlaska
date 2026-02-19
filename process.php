<?php
require_once 'db_connect.php';

$errors = [];

// Collect & trim inputs
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$date_of_birth = $_POST['date_of_birth'] ?? '';
$gender = $_POST['gender'] ?? '';
$blood_type = $_POST['blood_type'] ?? '';
$physical_fitness = (int)($_POST['physical_fitness'] ?? 5);
$education = $_POST['education'] ?? '';
$field_of_study = trim($_POST['field_of_study'] ?? '');
$years_of_experience = (int)($_POST['years_of_experience'] ?? 0);
$current_position = trim($_POST['current_position'] ?? '');
$skills = $_POST['skills'] ?? [];
$preferred_role = $_POST['preferred_role'] ?? '';
$motivation = trim($_POST['motivation'] ?? '');
$agreement = isset($_POST['agreement']) ? 1 : 0;

// --- Validation ---

// First name
if (empty($first_name)) {
    $errors[] = 'First name is required.';
} elseif (strlen($first_name) < 2) {
    $errors[] = 'First name must be at least 2 characters.';
}

// Last name
if (empty($last_name)) {
    $errors[] = 'Last name is required.';
} elseif (strlen($last_name) < 2) {
    $errors[] = 'Last name must be at least 2 characters.';
}

// Email
if (empty($email)) {
    $errors[] = 'E-mail is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid e-mail address.';
}

// Date of birth
if (empty($date_of_birth)) {
    $errors[] = 'Date of birth is required.';
} else {
    $dob = new DateTime($date_of_birth);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
    if ($age < 18) {
        $errors[] = 'You must be at least 18 years old.';
    } elseif ($age > 60) {
        $errors[] = 'Maximum age for applicants is 60 years.';
    }
}

// Gender
$valid_genders = ['male', 'female', 'other'];
if (empty($gender) || !in_array($gender, $valid_genders)) {
    $errors[] = 'Please select a gender.';
}

// Blood type
$valid_blood = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', '0+', '0-'];
if (empty($blood_type) || !in_array($blood_type, $valid_blood)) {
    $errors[] = 'Please select a valid blood type.';
}

// Physical fitness
if ($physical_fitness < 1 || $physical_fitness > 10) {
    $errors[] = 'Physical fitness must be between 1 and 10.';
}

// Education
$valid_education = ['elementary', 'high_school', 'associate', 'bachelors', 'masters', 'doctorate'];
if (empty($education) || !in_array($education, $valid_education)) {
    $errors[] = 'Please select your education level.';
}

// Field of study
if (empty($field_of_study)) {
    $errors[] = 'Field of study is required.';
}

// Years of experience
if ($years_of_experience < 0 || $years_of_experience > 50) {
    $errors[] = 'Years of experience must be between 0 and 50.';
}

// Skills
if (empty($skills) || !is_array($skills)) {
    $errors[] = 'Please select at least one skill.';
}

// Preferred role
$valid_roles = ['pilot', 'engineer', 'scientist', 'medic', 'botanist'];
if (empty($preferred_role) || !in_array($preferred_role, $valid_roles)) {
    $errors[] = 'Please select a preferred role.';
}

// Motivation
if (empty($motivation)) {
    $errors[] = 'Motivation is required.';
} elseif (strlen($motivation) < 20) {
    $errors[] = 'Motivation must be at least 20 characters.';
}

// Agreement
if (!$agreement) {
    $errors[] = 'You must agree to the mission terms.';
}

// --- If errors, redirect back ---
if (!empty($errors)) {
    $error_string = implode('|', array_map('urlencode', $errors));
    header("Location: index.php?errors=" . $error_string);
    exit;
}

// --- Save to database ---
$skills_string = implode(', ', $skills);

$stmt = $conn->prepare("
    INSERT INTO applications
    (first_name, last_name, email, phone, date_of_birth, gender, blood_type, physical_fitness,
     education, field_of_study, years_of_experience, current_position, skills, preferred_role,
     motivation, agreement)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssississssi",
    $first_name,
    $last_name,
    $email,
    $phone,
    $date_of_birth,
    $gender,
    $blood_type,
    $physical_fitness,
    $education,
    $field_of_study,
    $years_of_experience,
    $current_position,
    $skills_string,
    $preferred_role,
    $motivation,
    $agreement
);

if ($stmt->execute()) {
    $application_id = $conn->insert_id;
    header("Location: success.php?id=" . $application_id);
    exit;
} else {
    $errors[] = 'Database error: ' . $stmt->error;
    $error_string = implode('|', array_map('urlencode', $errors));
    header("Location: index.php?errors=" . $error_string);
    exit;
}
