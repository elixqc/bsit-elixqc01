<?php
session_start();

$User = "yourname";
$Pass = "BSIT";

if (isset($_GET['logout'])) {
    session_destroy();
    setcookie("username", "", time() - (86400 * 2), "/");
    header("Location: students.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username === $User && $password === $Pass) {
        $_SESSION['username'] = $username;
        
        if (isset($_POST['remember'])) {
            setcookie("username", $username, time() + (86400 * 2), "/");
        }
        
        header("Location: students.php");
        exit();
    } else {
        $error = "Invalid Credentials.";
    }
}

$isLoggedIn = false;
if (isset($_SESSION['username'])) {
    $isLoggedIn = true;
    $currentUser = $_SESSION['username'];
} elseif (isset($_COOKIE['username'])) {
    $isLoggedIn = true;
    $currentUser = $_COOKIE['username'];
    $_SESSION['username'] = $_COOKIE['username'];
}

$students = [
    "Nico Barredo",
    "Jonel Aquino",
    "Marcus aristain",
    "Bayani agbayani",
    "Nestor Valdez",
    "Tim tan",
    "seph coronado",
    "JM itim",
    "williamson aristain",
    "Gab Riel"
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management System</title>
</head>
<body>
    <h1>Student Management System</h1>

    <?php if (!$isLoggedIn): ?>
        <h2>Login</h2>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label><br><br>
            <label>Password: <input type="password" name="password" required></label><br><br>
            <label><input type="checkbox" name="remember"> Remember Me</label><br><br>
            <button type="submit" name="login" method = "POST">Login</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php else: ?>
        <h2>Welcome, <?php echo $currentUser; ?>!</h2>
        
        <div>
            
            <h4>List of Student names:</h4>
            <ol>
                <?php foreach ($students as $student): ?>
                    <li><?php echo $student; ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        
        <a href="?logout=1">Logout</a>
    <?php endif; ?>
</body>
</html>