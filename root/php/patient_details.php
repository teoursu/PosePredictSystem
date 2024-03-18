<!DOCTYPE html>
<html>

<head>
    <title>Patient Details</title>
    <link rel="stylesheet" href="patient_details.css">
</head>

<body>
    <div class="container">
        <h1>Patient Details</h1>

        <?php
        if (isset($_GET['id'])) {
            $patientId = $_GET['id'];

            $host = "localhost";
            $username = "root";
            $password = "usbw";
            $dbname = "polipoza";
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

            assert($pdo !== false, 'Failed to connect to the database');

            $stmt = $pdo->prepare('SELECT name, cnp, age, sex, date_of_surgery, asthma, aspirin_allergy, eo, smoker, SNOT, `HPQ-9`, `Lund-Mackay`, mir125, mir203, follow_treatment FROM patient WHERE id = ?');
            assert($stmt !== false, 'Failed to prepare the SQL statement');

            $stmt->execute([$patientId]);

            $patient = $stmt->fetch(PDO::FETCH_ASSOC);

            assert($patient !== false, 'Failed to fetch patient data');
            echo '<div class="button-container">';
            echo '<a href="main.php" class="button">Back to patient list</a>';
            echo '</div>';

            if ($patient) {
                echo '<div class="button-container">';
                echo '<a href="edit_patient.php?patient_id=' . $patientId . '" class="button">Edit Patient</a>';

                echo '</div>';

                echo '<div class="general-info">';
                echo '<h2>General Information</h2>';
                echo '<p><strong>Name:</strong> ' . $patient['name'] . '</p>';
                echo '<p><strong>CNP:</strong> ' . $patient['cnp'] . '</p>';
                echo '<p><strong>Age:</strong> ' . $patient['age'] . '</p>';
                echo '<p><strong>Sex:</strong> ' . ($patient['sex'] == '0' ? 'Male' : 'Female') . '</p>';
                echo '<p><strong>Date of Surgery:</strong> ' . $patient['date_of_surgery'] . '</p>';
                echo '<p><strong>Asthma:</strong> ' . ($patient['asthma'] == '1' ? 'Yes' : 'No') . '</p>';
                echo '<p><strong>Aspirin Allergy:</strong> ' . $patient['aspirin_allergy'] . '</p>';
                echo '<p><strong>EO:</strong> ' . ($patient['eo'] == '1' ? 'Yes' : 'No') . '</p>';
                echo '<p><strong>Smoker:</strong> ' . ($patient['smoker'] == '1' ? 'Yes' : 'No') . '</p>';
                echo '<p><strong>SNOT:</strong> ' . $patient['SNOT'] . '</p>';
                echo '<p><strong>Lund-Mackay:</strong> ' . $patient['Lund-Mackay'] . '</p>';
                echo '<p><strong>HPQ-9:</strong> ' . $patient['HPQ-9'] . '</p>';
                echo '<p><strong>mir125:</strong> ' . $patient['mir125'] . '</p>';
                echo '<p><strong>mir203:</strong> ' . $patient['mir203'] . '</p>';
                echo '<p><strong>Follow Treatment:</strong> ' . ($patient['follow_treatment'] == 1 ? 'Yes' : 'No') . '</p>';
                echo '</div>';

                $stmt = $pdo->prepare('SELECT * FROM six_months_data WHERE patient_id = ?');
                assert($stmt !== false, 'Failed to prepare the SQL statement');

                $stmt->execute([$patientId]);
                $sixMonthsData = $stmt->fetch(PDO::FETCH_ASSOC);

                echo '<div class="six-months">';
                echo '<h2>6 Months</h2>';

                if ($sixMonthsData) {
                    echo '<p><strong>SNOT:</strong> ' . $sixMonthsData['snot'] . '</p>';
                    echo '<p><strong>POSE:</strong> ' . $sixMonthsData['pose'] . '</p>';
                    echo '<p><strong>HPQ-9:</strong> ' . $sixMonthsData['hpq9'] . '</p>';
                } else {
                    echo '<form method="POST" action="save_six_months.php">';
                    echo '<input type="hidden" name="patient_id" value="' . $patientId . '">';
                    echo '<label for="snot_six_months">SNOT:</label>';
                    echo '<input type="number" name="snot_six_months" id="snot_six_months" required min="0" max="100"><br>';
                    echo '<label for="pose_six_months">POSE:</label>';
                    echo '<input type="number" name="pose_six_months" id="pose_six_months" required min="0" max="32"><br>';
                    echo '<label for="hpq9_six_months">HPQ-9:</label>';
                    echo '<input type="number" name="hpq9_six_months" id="hpq9_six_months" required min="0" max="27"><br>';
                    echo '<input type="submit" value="Save" class="button">';
                    echo '</form>';
                }

                echo '</div>';

                $stmt = $pdo->prepare('SELECT * FROM predicted_pose_six_months WHERE patient_id = ?');
                assert($stmt !== false, 'Failed to prepare the SQL statement');

                $stmt->execute([$patientId]);
                $poseSixMonths = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $pdo->prepare('SELECT * FROM predicted_pose_twelve_months WHERE patient_id = ?');
                assert($stmt !== false, 'Failed to prepare the SQL statement');

                $stmt->execute([$patientId]);
                $poseTwelveMonths = $stmt->fetch(PDO::FETCH_ASSOC);

                echo '<div class="predicted-pose">';
                echo '<h2>Predicted Pose - 6 Months</h2>';

                if ($poseSixMonths) {
                    echo '<p><strong>Pose:</strong> ' . $poseSixMonths['pose_six_months'] . '</p>';
                } else {
                    echo 'No predicted pose data available for 6 months.<br>';
                }

                echo '<h2>Predicted Pose - 12 Months</h2>';

                if ($poseTwelveMonths) {
                    echo '<p><strong>Pose:</strong> ' . $poseTwelveMonths['pose_twelve_months'] . '</p>';
                } else {
                    echo 'No predicted pose data available for 12 months.<br>';
                }

                echo '</div>';
            } else {
                echo '<p>Patient not found.</p>';
            }

            $pdo = null;
        } else {
            echo '<p>Invalid request.</p>';
        }
        ?>
    </div>
</body>

</html>