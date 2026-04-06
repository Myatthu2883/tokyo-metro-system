<?php
$page_title = 'Manage Stations';
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
        $station_name = mysqli_real_escape_string($conn, $_POST['station_name']);
        $station_code = mysqli_real_escape_string($conn, $_POST['station_code']);
        $line_id      = (int)$_POST['line_id'];
        $station_order = (int)$_POST['station_order'];
        $is_transfer  = isset($_POST['is_transfer']) ? 1 : 0;
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "INSERT INTO stations (station_name, station_code, line_id, station_order, is_transfer, status) 
                VALUES ('$station_name', '$station_code', $line_id, $station_order, $is_transfer, '$status')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Station added successfully.';
        } else {
            $error = 'Failed to add station. Code may already exist.';
        }
    }
    if ($_POST['action'] === 'edit') {
        $station_id   = (int)$_POST['station_id'];
        $station_name = mysqli_real_escape_string($conn, $_POST['station_name']);
        $station_code = mysqli_real_escape_string($conn, $_POST['station_code']);
        $line_id      = (int)$_POST['line_id'];
        $station_order = (int)$_POST['station_order'];
        $is_transfer  = isset($_POST['is_transfer']) ? 1 : 0;
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "UPDATE stations SET station_name='$station_name', station_code='$station_code', line_id=$line_id, 
                station_order=$station_order, is_transfer=$is_transfer, status='$status' WHERE station_id=$station_id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Station updated successfully.';
        } else {
            $error = 'Failed to update station.';
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM stations WHERE station_id=$id")) {
        $success = 'Station deleted.';
    } else {
        $error = 'Cannot delete: station is used in schedules or fares.';
    }
}

$stations = mysqli_query($conn, "
    SELECT s.*, ml.line_name, ml.line_code, ml.color_hex 
    FROM stations s JOIN metro_lines ml ON s.line_id = ml.line_id 
    ORDER BY ml.line_id, s.station_order
");
$lines = mysqli_query($conn, "SELECT * FROM metro_lines ORDER BY line_name");

$edit_station = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_station = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM stations WHERE station_id=$eid"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stations | Tokyo Metro</title>
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

<h2 class="section-title">Manage Stations</h2>

<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<!-- Add / Edit Form -->
<div class="form-container" style="max-width:100%;margin-bottom:24px;">
    <h2><?php echo $edit_station ? '✏️ Edit Station' : '➕ Add New Station'; ?></h2>
    <form method="POST" action="manage_stations.php" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;align-items:end;">
        <input type="hidden" name="action" value="<?php echo $edit_station ? 'edit' : 'add'; ?>">
        <?php if ($edit_station): ?>
            <input type="hidden" name="station_id" value="<?php echo $edit_station['station_id']; ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Station Name</label>
            <input type="text" name="station_name" required value="<?php echo $edit_station ? $edit_station['station_name'] : ''; ?>" placeholder="e.g. Shibuya">
        </div>
        <div class="form-group">
            <label>Station Code</label>
            <input type="text" name="station_code" required value="<?php echo $edit_station ? $edit_station['station_code'] : ''; ?>" placeholder="e.g. G01">
        </div>
        <div class="form-group">
            <label>Line</label>
            <select name="line_id" required>
                <?php 
                mysqli_data_seek($lines, 0);
                while ($l = mysqli_fetch_assoc($lines)): ?>
                <option value="<?php echo $l['line_id']; ?>" <?php echo ($edit_station && $edit_station['line_id'] == $l['line_id']) ? 'selected' : ''; ?>>
                    <?php echo $l['line_name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Order</label>
            <input type="number" name="station_order" required value="<?php echo $edit_station ? $edit_station['station_order'] : '1'; ?>" min="1">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Open','Closed','Under Maintenance'] as $st): ?>
                <option value="<?php echo $st; ?>" <?php echo ($edit_station && $edit_station['status'] === $st) ? 'selected' : ''; ?>><?php echo $st; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:8px;padding-top:20px;">
            <input type="checkbox" name="is_transfer" id="is_transfer" <?php echo ($edit_station && $edit_station['is_transfer']) ? 'checked' : ''; ?>>
            <label for="is_transfer" style="margin:0;">Transfer Station</label>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_station ? 'Update' : 'Add Station'; ?></button>
            <?php if ($edit_station): ?>
                <a href="manage_stations.php" class="btn" style="background:#E8EDF5;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Stations Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Station Name</th>
                <th>Line</th>
                <th>Order</th>
                <th>Transfer</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($s = mysqli_fetch_assoc($stations)): ?>
            <tr>
                <td><?php echo $s['station_id']; ?></td>
                <td><span class="line-badge" style="background:<?php echo $s['color_hex']; ?>;"><?php echo $s['station_code']; ?></span></td>
                <td><strong><?php echo htmlspecialchars($s['station_name']); ?></strong></td>
                <td><?php echo $s['line_name']; ?></td>
                <td><?php echo $s['station_order']; ?></td>
                <td><?php echo $s['is_transfer'] ? '🔄 Yes' : '—'; ?></td>
                <td><span class="status status-<?php echo strtolower(str_replace(' ', '', $s['status'])); ?>"><?php echo $s['status']; ?></span></td>
                <td>
                    <a href="manage_stations.php?edit=<?php echo $s['station_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="manage_stations.php?delete=<?php echo $s['station_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this station?')">Delete</a>
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
