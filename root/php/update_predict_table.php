<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function updatePredictedPose($patientId, $pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM patient WHERE id = :patientId");
    $stmt->bindParam(':patientId', $patientId);
    assert($stmt !== false, 'Failed to prepare the SELECT statement for patient table');
    $stmt->execute();

    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$patient) {
        exit;
    }

    $patientJson = json_encode($patient);
    $pythonScript = 'model.py';
    assert(file_put_contents('data.json', $patientJson) !== false, 'Failed to write patient data to data.json');

    $command = 'python ' . escapeshellarg($pythonScript);

    $descriptors = [
        1 => ['pipe', 'w'],
        // stdout
        2 => ['pipe', 'w'], // stderr
    ];

    $process = proc_open($command, $descriptors, $pipes);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    if (!empty($error)) {
        exit;
    }

    if (!empty($output)) {
        $result = json_decode($output, true);

        if (isset($result["y_pred"])) {
            $y_pred_6 = $result["y_pred"];
            $stmt = $pdo->prepare('INSERT INTO predicted_pose_six_months (patient_id, pose_six_months) VALUES (?, ?) ON DUPLICATE KEY UPDATE pose_six_months = ?');
            assert($stmt !== false, 'Failed to prepare the INSERT statement for predicted_pose_six_months table');
            $stmt->execute([$patientId, $y_pred_6[0], $y_pred_6[0]]);
        }
    }

    proc_close($process);

    // ...

    $stmt2 = $pdo->prepare("SELECT hpq9, snot, pose FROM six_months_data WHERE patient_id = :patientId");
    $stmt2->bindParam(':patientId', $patientId);
    assert($stmt2 !== false, 'Failed to prepare the SELECT statement for six_months_data table');
    $stmt2->execute();
    $data = $stmt2->fetch(PDO::FETCH_ASSOC);

    // ...

    if (!empty($data['snot'])) {
        $patient['HPQ-9.1'] = $data['hpq9'];
        $patient['SNOT.1'] = $data['snot'];
        $patient['POSE'] = $data['pose'];
        $pythonScript = 'model-12m.py';
        $patientJson = json_encode($patient);
        assert(file_put_contents('data-12m.json', $patientJson) !== false, 'Failed to write patient data to data-12m.json');
    } else {
        $pythonScript = 'model-12m-original.py';
        $patientJson = json_encode($patient);
        assert(file_put_contents('data-12m-original.json', $patientJson) !== false, 'Failed to write patient data to data-12m-original.json');
    }

    $command = 'python ' . escapeshellarg($pythonScript);

    $descriptors = [
        1 => ['pipe', 'w'],
        // stdout
        2 => ['pipe', 'w'], // stderr
    ];

    $process = proc_open($command, $descriptors, $pipes);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    if (!empty($error)) {
        exit;
    }

    if (!empty($output)) {
        $result = json_decode($output, true);

        if (isset($result["y_pred"])) {
            $y_pred = $result["y_pred"];
            $stmt2 = $pdo->prepare('INSERT INTO predicted_pose_twelve_months (patient_id, pose_twelve_months) VALUES (?, ?) ON DUPLICATE KEY UPDATE pose_twelve_months = ?');
            assert($stmt2 !== false, 'Failed to prepare the INSERT statement for predicted_pose_twelve_months table');
            $stmt2->execute([$patientId, $y_pred[0], $y_pred[0]]);
        }
    }

    proc_close($process);

    $pdo = null;
}
?>