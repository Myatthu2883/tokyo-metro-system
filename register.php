<?php
$page_title = 'Register';
require_once 'includes/db_connect.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password  = md5($_POST['password']);
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));

    // Check if username or email exists
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Username or email already exists.';
    } else {
        $sql = "INSERT INTO users (username, password, full_name, email, phone, role) 
                VALUES ('$username', '$password', '$full_name', '$email', '$phone', 'Passenger')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Registration successful! You can now <a href="login.php">login</a>.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>📝 Register</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required placeholder="Your full name">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Choose a username">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="your@email.com">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" placeholder="Phone number (optional)">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Choose a password" minlength="4">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Register as Passenger</button>
    </form>

    <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-light);">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>
