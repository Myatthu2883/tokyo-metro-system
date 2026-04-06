<?php
$page_title = 'Login';
require_once 'includes/db_connect.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];

        if ($user['role'] === 'Admin' || $user['role'] === 'Staff') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>🔐 Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Enter your username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Login</button>
    </form>

    <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-light);">
        Don't have an account? <a href="register.php">Register here</a>
    </p>

    <div style="margin-top:24px;padding:16px;background:#F8F9FB;border-radius:8px;font-size:12px;color:var(--text-light);">
        <strong>Demo Accounts:</strong><br>
        Admin: <code>admin / admin123</code><br>
        Staff: <code>staff01 / staff123</code><br>
        Passenger: <code>passenger1 / pass123</code>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
