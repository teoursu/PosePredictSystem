<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "polipoza";

$pdo = mysqli_connect($servername, $username, $password, $dbname);
assert($pdo !== false, 'Failed to connect to the database');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user = $_POST["uname"];
    $pass = $_POST["psw"];
    $sql = "SELECT * FROM user WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($pdo, $sql);
    assert($result !== false, 'Failed to execute the SELECT query');

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];

        session_start();
        $_SESSION['user_id'] = $user_id;

        header("Location: main.php?id=$user_id");
        exit;
    } else {
        echo "Invalid username or password.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $user_name = $_GET["uname"];
    $pass = $_GET["psw"];
    $sql = "SELECT * FROM user WHERE username = '$user_name' ";
    $result = mysqli_query($pdo, $sql);
    assert($result !== false, 'Failed to execute the SELECT query');

    if (mysqli_num_rows($result) < 1) {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        assert($conn !== false, 'Failed to connect to the database');

        $stmt = $conn->prepare("INSERT INTO user (username, password) VALUES (:username, :password)");
        assert($stmt !== false, 'Failed to prepare the SQL statement');

        $stmt->bindParam(':username', $user_name);
        $stmt->bindParam(':password', $pass);

        assert($stmt->execute(), 'Failed to execute the INSERT statement');

        $user_id = $conn->lastInsertId();

        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        header("Location: main.php?id=$user_id");
        exit;
    } else {
        echo '<script>alert("User already exists");</script>';
        echo '<script>window.location.href = "'.$_SERVER['HTTP_REFERER'].'";</script>';

    }
}

?>