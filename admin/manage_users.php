<?php
$page_title = 'Manage Users';
require_once '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id == $_SESSION['user_id']) {
        $error = 'Cannot delete your own account.';
    } else {
        if (mysqli_query($conn, "DELETE FROM users WHERE user_id=$id")) {
            $success = 'User deleted.';
        } else {
            $error = 'Cannot delete: user has linked tickets.';
        }
    }
}

// Handle Role Change
if (isset($_GET['change_role']) && isset($_GET['uid'])) {
    $uid = (int)$_GET['uid'];
    $new_role = mysqli_real_escape_string($conn, $_GET['change_role']);
    if (in_array($new_role, ['Admin', 'Staff', 'Passenger'])) {
        mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE user_id=$uid");
        $success = 'User role updated.';
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role, full_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Tokyo Metro</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="header-inner">
        <a href="../index.php" class="logo">
            <span class="logo-mark">◆</span>
            <span class="logo-text">Tokyo Metro</span>
            <span class="logo-sub">Admin Panel</span>
        </a>
        <nav class="main-nav">
            <a href="../index.php">Public Site</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_trains.php">Trains</a>
            <a href="manage_stations.php">Stations</a>
            <a href="manage_schedules.php">Schedules</a>
            <a href="manage_users.php">Users</a>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<main class="main-content">

<h2 class="section-title">Manage Users</h2>

<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<div class="table-container">
    <div class="table-header">
        <h2>All Users (<?php echo mysqli_num_rows($users); ?>)</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $u['user_id']; ?></td>
                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['phone']); ?></td>
                <td>
                    <span class="user-badge <?php echo strtolower($u['role']); ?>"><?php echo $u['role']; ?></span>
                </td>
                <td><small><?php echo date('M j, Y', strtotime($u['created_at'])); ?></small></td>
                <td>
                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                        <select onchange="if(this.value) window.location='manage_users.php?uid=<?php echo $u['user_id']; ?>&change_role='+this.value" style="padding:4px 8px;font-size:12px;border-radius:4px;border:1px solid var(--border);">
                            <option value="">Change Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                            <option value="Passenger">Passenger</option>
                        </select>
                        <a href="manage_users.php?delete=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user <?php echo $u['username']; ?>?')">Delete</a>
                    <?php else: ?>
                        <small style="color:var(--text-light);">Current user</small>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>

<footer class="main-footer">
    <div class="footer-inner">
        <div class="footer-brand"><span class="logo-mark">◆</span> Tokyo Metro Railway Management System</div>
        <div class="footer-info"><p>&copy; 2026 Team Shinkansen</p></div>
    </div>
</footer>
<script src="../js/main.js"></script>
</body>
</html>
