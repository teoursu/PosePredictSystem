<?php
session_start();

$host = "localhost";
$username = "root";
$password = "usbw";
$dbname = "polipoza";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    assert($pdo !== false, 'Failed to connect to the database');
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('SELECT id, name FROM patient WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    assert($patients !== false, 'Failed to fetch patients');

    $pdo = null;
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Patients</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <h1>Patients</h1>

    <div class="patient-list">
        <?php if (count($patients) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td>
                                <?php echo $patient['id']; ?>
                            </td>
                            <td>
                                <?php echo $patient['name']; ?>
                            </td>
                            <td>
                                <a href="patient_details.php?id=<?php echo $patient['id']; ?>">View Details</a>
                                <button class="delete-button" data-patient-id="<?php echo $patient['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No patients found.</p>
        <?php endif; ?>
    </div>

    <a href="add_patient.php" class="add-patient">Add New Patient</a>

    <script>
        var deleteButtons = document.getElementsByClassName("delete-button");
        for (var i = 0; i < deleteButtons.length; i++) {
            deleteButtons[i].addEventListener("click", confirmDelete);
        }

        function confirmDelete() {
            var patientId = this.getAttribute("data-patient-id");
            var confirmDelete = confirm("Are you sure you want to delete this patient?");
            if (confirmDelete) {
                window.location.href = "delete_patient.php?id=" + patientId;
            }
        }
    </script>
</body>

</html>