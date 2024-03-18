<?php
$host = "localhost";
$username = "root";
$password = "usbw";
$dbname = "polipoza";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $patientId = $_GET['patient_id'];

    assert(isset($patientId), 'Patient ID is missing');

    $patientStmt = $pdo->prepare('SELECT * FROM patient WHERE id = ?');
    $patientStmt->execute([$patientId]);
    $patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

    assert($patient !== false, 'Patient not found');

    $sixMonthsStmt = $pdo->prepare('SELECT * FROM six_months_data WHERE patient_id = ?');
    $sixMonthsStmt->execute([$patientId]);
    $sixMonthsData = $sixMonthsStmt->fetch(PDO::FETCH_ASSOC);

    assert($sixMonthsData !== false, 'Six months data not found');

    $pdo = null;
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" href="edit_patient.css">

<head>
    <title>Edit Patient Data</title>
</head>

<body>

    <h2>Patient Details</h2>

    <h3>Patient Details</h3>
    <form method="POST" action="save_patient.php">
        <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo $patient['name']; ?>" required><br>
        <label for="cnp">CNP:</label>
        <input type="text" name="cnp" id="cnp" value="<?php echo $patient['cnp']; ?>" required><br>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?php echo $patient['age']; ?>" required><br>


        <label for="sex">Sex:</label>
        <select name="sex" id="sex" required>
            <option value="1" <?php if ($patient['sex'] == 'Female')
                echo 'selected'; ?>>Female</option>
            <option value="0" <?php if ($patient['sex'] == 'Male')
                echo 'selected'; ?>>Male</option>
        </select><br>

        <label for="date_of_surgery">Date of Surgery:</label>
        <input type="date" name="date_of_surgery" id="date_of_surgery"
            value="<?php echo $patient['date_of_surgery']; ?>" required><br>
        <label for="asthma">Asthma:</label>
        <input type="checkbox" name="asthma" id="asthma" <?php echo ($patient['asthma'] == '1') ? 'checked' : ''; ?>><br>
        <label for="aspirin_allergy">Aspirin Allergy:</label>
        <select name="aspirin_allergy" id="aspirin_allergy" required>
            <option value="0" <?php if ($patient['aspirin_allergy'] == '0')
                echo 'selected'; ?>>0</option>
            <option value="1" <?php if ($patient['aspirin_allergy'] == '1')
                echo 'selected'; ?>>1</option>
            <option value="2" <?php if ($patient['aspirin_allergy'] == '2')
                echo 'selected'; ?>>2</option>
        </select><br> <label for="eo">EO:</label>
        <input type="checkbox" name="eo" id="eo" <?php echo ($patient['eo'] == '1') ? 'checked' : ''; ?>><br>
        <label for="smoker">Smoker:</label>
        <input type="checkbox" name="smoker" id="smoker" <?php echo ($patient['smoker'] == '1') ? 'checked' : ''; ?>><br>
        <label for="SNOT">SNOT:</label>
        <input type="number" name="SNOT" id="SNOT" min="1" max="100" value="<?php echo $patient['SNOT']; ?>" required
            min="0" max="100"><br>
        <label for="HPQ-9">HPQ-9:</label>
        <input type="number" name="HPQ-9" id="HPQ-9" value="<?php echo $patient['HPQ-9']; ?>" required min="0"
            max="27"><br>
        <label for="Lund-Mackay">Lund-Mackay:</label>
        <input type="number" name="Lund-Mackay" id="Lund-Mackay" value="<?php echo $patient['Lund-Mackay']; ?>" required
            min="0" max="24"><br>
        <label for="endoscopy">Endoscopy:</label>
        <input type="number" name="endoscopy" id="endoscopy" value="<?php echo $patient['endoscopy']; ?>" required
            min="0" max="6"><br>
        <label for="mir125">mir125:</label>
        <input type="text" name="mir125" id="mir125" value="<?php echo $patient['mir125']; ?>"><br>
        <label for="mir203">mir203:</label>
        <input type="text" name="mir203" id="mir203" value="<?php echo $patient['mir203']; ?>"><br>
        <label for="follow_treatment">Follow Treatment:</label>
        <input type="checkbox" name="follow_treatment" id="follow_treatment" <?php echo ($patient['follow_treatment'] == '1') ? 'checked' : ''; ?>><br>
  
        <h3>Six Months Data</h3>
        <label for="snot_six_months">SNOT:</label>
        <input type="number" name="snot_six_months" id="snot_six_months" value="<?php echo $sixMonthsData['snot']; ?>"
            min="0" max="100"><br>
        <label for="pose_six_months">POSE:</label>
        <input type="number" name="pose_six_months" id="pose_six_months" value="<?php echo $sixMonthsData['pose']; ?>"
            min="0" max="32"><br>
        <label for="hpq9_six_months">HPQ-9:</label>
        <input type="number" name="hpq9_six_months" id="hpq9_six_months" value="<?php echo $sixMonthsData['hpq9']; ?>"
            min="0" max="27"><br>
        <input type="submit" value="Save" class="button">
    </form>
</body>

</html>