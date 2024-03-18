<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $host = "localhost";
    $username = "root";
    $password = "usbw";
    $dbname = "polipoza";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    assert($pdo !== false, 'Failed to connect to the database');

    $sex = $_POST['sex'] == 'Female' ? 1 : 0;
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare('INSERT INTO patient ( name, cnp, age, sex, date_of_surgery, asthma, aspirin_allergy, eo, smoker, SNOT, `HPQ-9`, `Lund-Mackay`, endoscopy, mir125, mir203, follow_treatment,user_id) 
    VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)');
    echo $user_id;

    $stmt->execute([
        $_POST['name'],
        $_POST['cnp'],
        $_POST['age'],
        $sex,
        $_POST['date_of_surgery'],
        isset($_POST['asthma']) ? 1 : 0,
        isset($_POST['aspirin_allergy']) ? $_POST['aspirin_allergy'] : null,
        isset($_POST['eo']) ? 1 : 0,
        isset($_POST['smoker']) ? 1 : 0,
        $_POST['SNOT'],
        $_POST['HPQ-9'],
        $_POST['Lund-Mackay'],
        $_POST['endoscopy'],
        $_POST['mir125'],
        $_POST['mir203'],
        isset($_POST['follow_treatment']) ? 1 : 0,
        $user_id
    ]);
    echo $user_id;

    assert($stmt->rowCount() !== false, 'Failed to execute the SQL statement');

    if ($stmt->rowCount() > 0) {
        $newPatientId = $pdo->lastInsertId();

        include 'update_predict_table.php';
        assert(function_exists('updatePredictedPose'), 'updatePredictedPose function not found');

        updatePredictedPose($newPatientId, $pdo);
    } else {
        echo 'Failed to add patient.';
    }

    $pdo = null;
    header('Location: main.php');
    exit;
}
?>


<!DOCTYPE html>
<html>
<link rel="stylesheet" href="add_patient.css">

<body>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="cnp">CNP:</label>
        <input type="text" name="cnp" id="cnp" required><br>
        <label for="age">Age:</label>
        <input type="number" name="age" id="age" required min='0'><br>
        <label for="sex">Sex:</label>
        <select name="sex" id="sex" required>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
        </select><br>

        <label for="date_of_surgery">Date of Surgery:</label>
        <input type="date" name="date_of_surgery" id="date_of_surgery" required><br>

        <label for="asthma">Asthma:</label>
        <input type="checkbox" name="asthma" id="asthma"><br>

        <label for="aspirin_allergy">Aspirin Allergy:</label>
        <select name="aspirin_allergy" id="aspirin_allergy" required>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select><br>

        <label for="eo">EO:</label>
        <input type="checkbox" name="eo" id="eo"><br>

        <label for="smoker">Smoker:</label>
        <input type="checkbox" name="smoker" id="smoker"><br>

        <label for="SNOT">SNOT:</label>
        <input type="number" name="SNOT" id="SNOT" required min="1" max="100"><br>

        <label for="HPQ-9">HPQ-9:</label>
        <input type="number" name="HPQ-9" id="HPQ-9" required min="0" max="27"><br>

        <label for="Lund-Mackay">Lund-Mackay:</label>
        <input type="number" name="Lund-Mackay" id="Lund-Mackay" required min="0" max="24"><br>

        <label for="endoscopy">Endoscopy:</label>
        <input type="number" name="endoscopy" id="endoscopy" required min="1" max="6"><br>

        <label for="mir125">mir125:</label>
        <input type="text" name="mir125" id="mir125" required><br>

        <label for="mir203">mir203:</label>
        <input type="text" name="mir203" id="mir203" required><br>

        <label for="follow_treatment">Follow Treatment:</label>
        <input type="checkbox" name="follow_treatment" id="follow_treatment"><br>


        <input type="submit" value="Add Patient">
    </form>

</body>

</html>