<!DOCTYPE html>
<html>
<head>
    <title>Multimedia Archive</title>
</head>
<body>
    <h1>Multimedia Archive</h1>

    <?php if (!isset($_SESSION['username'])): ?>
        <h2>Login</h2>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label><br>
            <label>Password: <input type="password" name="password" required></label><br>
            <button type="submit" name="login">Login</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php else: ?>
        <h2>Welcome, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</h2>
       
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <h3>Upload File</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="file_upload" required>
                <button type="submit" name="upload">Upload</button>
            </form>
        <?php endif; ?>

        <h3>Uploaded Files</h3>
        <table border="1">
            <tr>
                <th>File Name</th>
                <th>Action</th>
            </tr>
            <?php if (empty($files)): ?>
                <tr>
                    <td colspan="2">No files uploaded yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file); ?></td>
                        <td>
                            <?php if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Editor'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="file_to_delete" value="<?php echo htmlspecialchars($file); ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
        
        <p><?php if (isset($message)) echo $message; ?></p>
        
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</body>
</html>



<?php
session_start();
session_destroy();
header("Location: index.php");
exit();
?>