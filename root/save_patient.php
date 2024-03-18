<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$host = "localhost";
$username = "root";
$password = "usbw";
$dbname = "polipoza";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$patientId = $_POST['patient_id'];

$patientName = $_POST['name'];
$patientCNP = $_POST['cnp'];
$patientAge = $_POST['age'];
$patientSex = $_POST['sex'];
$patientSurgeryDate = $_POST['date_of_surgery'];
$asthma = isset($_POST['asthma']) ? '1' : '0';
$aspirinAllergy = $_POST['aspirin_allergy'];
$eo = isset($_POST['eo']) ? '1' : '0';
$smoker = isset($_POST['smoker']) ? '1' : '0';
$SNOT = $_POST['SNOT'];
$HPQ9 = $_POST['HPQ-9'];
$LundMackay = $_POST['Lund-Mackay'];
$endoscopy = $_POST['endoscopy'];
$mir125 = $_POST['mir125'];
$mir203 = $_POST['mir203'];
$followTreatment = isset($_POST['follow_treatment']) ? '1' : '0';

$updatePatientStmt = $pdo->prepare('UPDATE patient SET name = ?, cnp = ?, age = ?, sex = ?, date_of_surgery = ?, asthma = ?, aspirin_allergy = ?, eo = ?, smoker = ?, SNOT = ?, `HPQ-9` = ?, `Lund-Mackay` = ?, endoscopy = ?, mir125 = ?, mir203 = ?, follow_treatment = ? WHERE id = ?');
assert($updatePatientStmt !== false, 'Failed to prepare the UPDATE statement for patient table');

$updatePatientStmt->execute([$patientName, $patientCNP, $patientAge, $patientSex, $patientSurgeryDate, $asthma, $aspirinAllergy, $eo, $smoker, $SNOT, $HPQ9, $LundMackay, $endoscopy, $mir125, $mir203, $followTreatment, $patientId]);

$sixMonthsSnot = $_POST['snot_six_months'];
$sixMonthsPose = $_POST['pose_six_months'];
$sixMonthsHPQ9 = $_POST['hpq9_six_months'];

$updateSixMonthsStmt = $pdo->prepare('UPDATE six_months_data SET snot = ?, pose = ?, hpq9 = ? WHERE patient_id = ?');

$updateSixMonthsStmt->execute([$sixMonthsSnot, $sixMonthsPose, $sixMonthsHPQ9, $patientId]);

header("Location: patient_details.php?id=$patientId");
include 'update_predict_table.php';
updatePredictedPose($patientId, $pdo);

$pdo = null;
exit();
?>