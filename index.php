<?php
session_start();

$users = [
    'admin' => ['password' => 'admin123', 'role' => 'Admin'],
    'editor' => ['password' => 'editor123', 'role' => 'Editor'],
    'viewer' => ['password' => 'viewer123', 'role' => 'Viewer']
];

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0754, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $users[$username]['role'];
    } else {
        $error = "Invalid username or password.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if ($_SESSION['role'] === 'Admin') {
        $allowedMimeTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'video/mp4',
            'audio/mpeg'
        ];
        
        $allowedExtensions = ['pdf', 'docx', 'mp4', 'mp3'];
        $maxFileSize = 100 * 1024 * 1024;
        
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === 0) {
            $file = $_FILES['file_upload'];
            $fileName = $file['name'];
            $fileSize = $file['size'];
            $fileTmpName = $file['tmp_name'];
            $fileMimeType = $file['type'];
            
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if ($fileSize > $maxFileSize) {
                $message = "Error: File size exceeds 100MB limit.";
            }
            elseif (!in_array($fileMimeType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $message = "Error: Only PDF, DOCX, MP4, and MP3 files are allowed.";
            }
            else {
                $destination = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmpName, $destination)) {
                    $message = "File uploaded successfully!";
                } else {
                    $message = "Error: Failed to upload file.";
                }
            }
        } else {
            $message = "Error: No file selected or upload error occurred.";
        }
    } else {
        $message = "Error: Only Admin users can upload files.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Editor') {
        $fileToDelete = $_POST['file_to_delete'];
        $filePath = $uploadDir . $fileToDelete;
        
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                $message = "File deleted successfully!";
            } else {
                $message = "Error: Failed to delete file.";
            }
        } else {
            $message = "Error: File does not exist.";
        }
    } else {
        $message = "Error: You don't have permission to delete files.";
    }
}

$files = [];
if (is_dir($uploadDir)) {
    $allFiles = scandir($uploadDir);
    $files = array_filter($allFiles, function($file) {
        return $file !== '.' && $file !== '..';
    });
}
?>

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
