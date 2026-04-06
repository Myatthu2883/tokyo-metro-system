<?php
$page_title = 'Manage Trains';
require_once '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $train_number = mysqli_real_escape_string($conn, $_POST['train_number']);
        $line_id      = (int)$_POST['line_id'];
        $capacity     = (int)$_POST['capacity'];
        $train_type   = mysqli_real_escape_string($conn, $_POST['train_type']);
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "INSERT INTO trains (train_number, line_id, capacity, train_type, status) 
                VALUES ('$train_number', $line_id, $capacity, '$train_type', '$status')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Train added successfully.';
        } else {
            $error = 'Failed to add train. Number may already exist.';
        }
    }
    if ($_POST['action'] === 'edit') {
        $train_id     = (int)$_POST['train_id'];
        $train_number = mysqli_real_escape_string($conn, $_POST['train_number']);
        $line_id      = (int)$_POST['line_id'];
        $capacity     = (int)$_POST['capacity'];
        $train_type   = mysqli_real_escape_string($conn, $_POST['train_type']);
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "UPDATE trains SET train_number='$train_number', line_id=$line_id, capacity=$capacity, 
                train_type='$train_type', status='$status' WHERE train_id=$train_id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Train updated successfully.';
        } else {
            $error = 'Failed to update train.';
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM trains WHERE train_id=$id")) {
        $success = 'Train deleted successfully.';
    } else {
        $error = 'Cannot delete: train may have linked schedules. Remove schedules first.';
    }
}

// Fetch trains
$trains = mysqli_query($conn, "
    SELECT t.*, ml.line_name, ml.line_code, ml.color_hex 
    FROM trains t 
    JOIN metro_lines ml ON t.line_id = ml.line_id 
    ORDER BY ml.line_id, t.train_number
");
$lines = mysqli_query($conn, "SELECT * FROM metro_lines ORDER BY line_name");

// Edit mode
$edit_train = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_train = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM trains WHERE train_id=$eid"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trains | Tokyo Metro</title>
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
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <a href="manage_users.php">Users</a>
            <?php endif; ?>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<main class="main-content">

<h2 class="section-title">Manage Trains</h2>

<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<!-- Add / Edit Form -->
<div class="form-container" style="max-width:100%;margin-bottom:24px;">
    <h2><?php echo $edit_train ? '✏️ Edit Train' : '➕ Add New Train'; ?></h2>
    <form method="POST" action="manage_trains.php" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;align-items:end;">
        <input type="hidden" name="action" value="<?php echo $edit_train ? 'edit' : 'add'; ?>">
        <?php if ($edit_train): ?>
            <input type="hidden" name="train_id" value="<?php echo $edit_train['train_id']; ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Train Number</label>
            <input type="text" name="train_number" required value="<?php echo $edit_train ? $edit_train['train_number'] : ''; ?>" placeholder="e.g. G-104">
        </div>
        <div class="form-group">
            <label>Line</label>
            <select name="line_id" required>
                <?php 
                mysqli_data_seek($lines, 0);
                while ($l = mysqli_fetch_assoc($lines)): ?>
                <option value="<?php echo $l['line_id']; ?>" <?php echo ($edit_train && $edit_train['line_id'] == $l['line_id']) ? 'selected' : ''; ?>>
                    <?php echo $l['line_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Capacity</label>
            <input type="number" name="capacity" required value="<?php echo $edit_train ? $edit_train['capacity'] : '1000'; ?>">
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="train_type" required>
                <?php foreach (['Local','Express','Rapid'] as $tt): ?>
                <option value="<?php echo $tt; ?>" <?php echo ($edit_train && $edit_train['train_type'] === $tt) ? 'selected' : ''; ?>><?php echo $tt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Running','Idle','Maintenance'] as $st): ?>
                <option value="<?php echo $st; ?>" <?php echo ($edit_train && $edit_train['status'] === $st) ? 'selected' : ''; ?>><?php echo $st; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_train ? 'Update' : 'Add Train'; ?></button>
            <?php if ($edit_train): ?>
                <a href="manage_trains.php" class="btn" style="background:#E8EDF5;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Trains Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Train #</th>
                <th>Line</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($t = mysqli_fetch_assoc($trains)): ?>
            <tr>
                <td><?php echo $t['train_id']; ?></td>
                <td><strong><?php echo $t['train_number']; ?></strong></td>
                <td><span class="line-badge" style="background:<?php echo $t['color_hex']; ?>;"><?php echo $t['line_code']; ?> <?php echo $t['line_name']; ?></span></td>
                <td><?php echo $t['train_type']; ?></td>
                <td><?php echo number_format($t['capacity']); ?></td>
                <td><span class="status status-<?php echo strtolower($t['status']); ?>"><?php echo $t['status']; ?></span></td>
                <td>
                    <a href="manage_trains.php?edit=<?php echo $t['train_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="manage_trains.php?delete=<?php echo $t['train_id']; ?>" class="btn btn-sm btn-danger" data-confirm="Delete train <?php echo $t['train_number']; ?>?" onclick="return confirm('Delete this train?')">Delete</a>
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
