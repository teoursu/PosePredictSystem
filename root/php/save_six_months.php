<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = $_POST['patient_id'];
    $snotSixMonths = $_POST['snot_six_months'];
    $poseSixMonths = $_POST['pose_six_months'];
    $hpq9SixMonths = $_POST['hpq9_six_months'];

    $host = "localhost";
    $username = "root";
    $password = "usbw";
    $dbname = "polipoza";

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $stmt = $pdo->prepare('INSERT INTO six_months_data (patient_id, snot, pose, hpq9) VALUES (?, ?, ?, ?)');
        assert($stmt !== false, 'Failed to prepare the INSERT statement for six_months_data table');
        $stmt->execute([$patientId, $snotSixMonths, $poseSixMonths, $hpq9SixMonths]);
        assert($stmt->rowCount() > 0, 'Failed to insert data into six_months_data table');

        echo 'Data for 6 months saved successfully.';
    } catch (PDOException $e) {
        echo 'Failed to save data for 6 months: ' . $e->getMessage();
    }
    include 'update_predict_table.php';
    updatePredictedPose($patientId, $pdo);
    $pdo = null;

    // Redirect to patient details page
    header('Location: patient_details.php?id=' . $patientId);
    exit();
}
?>