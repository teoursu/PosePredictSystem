<?php
$host = "localhost";
$username = "root";
$password = "usbw";
$dbname = "polipoza";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $patientId = $_GET['id'];

    assert(isset($patientId), 'Patient ID is missing');
    $pdo->prepare('DELETE FROM predicted_pose_six_months WHERE patient_id = ?')->execute([$patientId]);
    $pdo->prepare('DELETE FROM predicted_pose_twelve_months WHERE patient_id = ?')->execute([$patientId]);
    $pdo->prepare('DELETE FROM six_months_data WHERE patient_id = ?')->execute([$patientId]);
    $pdo->prepare('DELETE FROM patient WHERE id = ?')->execute([$patientId]);

    $pdo = null;
    header('Location: main.php');
    exit();
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>