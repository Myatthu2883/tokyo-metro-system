<?php
$page_title = 'Manage Schedules';
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
        $train_id     = (int)$_POST['train_id'];
        $dep_station  = (int)$_POST['departure_station_id'];
        $arr_station  = (int)$_POST['arrival_station_id'];
        $dep_time     = mysqli_real_escape_string($conn, $_POST['departure_time']);
        $arr_time     = mysqli_real_escape_string($conn, $_POST['arrival_time']);
        $days         = mysqli_real_escape_string($conn, $_POST['days_of_week']);
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "INSERT INTO schedules (train_id, departure_station_id, arrival_station_id, departure_time, arrival_time, days_of_week, status) 
                VALUES ($train_id, $dep_station, $arr_station, '$dep_time', '$arr_time', '$days', '$status')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Schedule added successfully.';
        } else {
            $error = 'Failed to add schedule.';
        }
    }
    if ($_POST['action'] === 'edit') {
        $schedule_id  = (int)$_POST['schedule_id'];
        $train_id     = (int)$_POST['train_id'];
        $dep_station  = (int)$_POST['departure_station_id'];
        $arr_station  = (int)$_POST['arrival_station_id'];
        $dep_time     = mysqli_real_escape_string($conn, $_POST['departure_time']);
        $arr_time     = mysqli_real_escape_string($conn, $_POST['arrival_time']);
        $days         = mysqli_real_escape_string($conn, $_POST['days_of_week']);
        $status       = mysqli_real_escape_string($conn, $_POST['status']);

        $sql = "UPDATE schedules SET train_id=$train_id, departure_station_id=$dep_station, arrival_station_id=$arr_station,
                departure_time='$dep_time', arrival_time='$arr_time', days_of_week='$days', status='$status' 
                WHERE schedule_id=$schedule_id";
        if (mysqli_query($conn, $sql)) {
            $success = 'Schedule updated.';
        } else {
            $error = 'Failed to update schedule.';
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM schedules WHERE schedule_id=$id")) {
        $success = 'Schedule deleted.';
    } else {
        $error = 'Cannot delete: schedule has linked tickets.';
    }
}

$schedules_list = mysqli_query($conn, "
    SELECT s.*, t.train_number, ml.line_name, ml.color_hex,
           dep.station_name AS dep_name, arr.station_name AS arr_name
    FROM schedules s
    JOIN trains t ON s.train_id = t.train_id
    JOIN metro_lines ml ON t.line_id = ml.line_id
    JOIN stations dep ON s.departure_station_id = dep.station_id
    JOIN stations arr ON s.arrival_station_id = arr.station_id
    ORDER BY ml.line_id, s.departure_time
");

$trains_list = mysqli_query($conn, "SELECT t.*, ml.line_name FROM trains t JOIN metro_lines ml ON t.line_id = ml.line_id ORDER BY ml.line_name, t.train_number");
$stations_list = mysqli_query($conn, "SELECT s.*, ml.line_name FROM stations s JOIN metro_lines ml ON s.line_id = ml.line_id ORDER BY ml.line_name, s.station_order");

$edit_sched = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_sched = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM schedules WHERE schedule_id=$eid"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules | Tokyo Metro</title>
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

<h2 class="section-title">Manage Schedules</h2>

<?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

<!-- Add / Edit Form -->
<div class="form-container" style="max-width:100%;margin-bottom:24px;">
    <h2><?php echo $edit_sched ? '✏️ Edit Schedule' : '➕ Add New Schedule'; ?></h2>
    <form method="POST" action="manage_schedules.php" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;align-items:end;">
        <input type="hidden" name="action" value="<?php echo $edit_sched ? 'edit' : 'add'; ?>">
        <?php if ($edit_sched): ?>
            <input type="hidden" name="schedule_id" value="<?php echo $edit_sched['schedule_id']; ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Train</label>
            <select name="train_id" required>
                <?php 
                mysqli_data_seek($trains_list, 0);
                while ($t = mysqli_fetch_assoc($trains_list)): ?>
                <option value="<?php echo $t['train_id']; ?>" <?php echo ($edit_sched && $edit_sched['train_id'] == $t['train_id']) ? 'selected' : ''; ?>>
                    <?php echo $t['train_number']; ?> (<?php echo $t['line_name']; ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Departure Station</label>
            <select name="departure_station_id" required>
                <?php 
                mysqli_data_seek($stations_list, 0);
                while ($st = mysqli_fetch_assoc($stations_list)): ?>
                <option value="<?php echo $st['station_id']; ?>" <?php echo ($edit_sched && $edit_sched['departure_station_id'] == $st['station_id']) ? 'selected' : ''; ?>>
                    <?php echo $st['station_name']; ?> (<?php echo $st['line_name']; ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Arrival Station</label>
            <select name="arrival_station_id" required>
                <?php 
                mysqli_data_seek($stations_list, 0);
                while ($st = mysqli_fetch_assoc($stations_list)): ?>
                <option value="<?php echo $st['station_id']; ?>" <?php echo ($edit_sched && $edit_sched['arrival_station_id'] == $st['station_id']) ? 'selected' : ''; ?>>
                    <?php echo $st['station_name']; ?> (<?php echo $st['line_name']; ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Departure Time</label>
            <input type="time" name="departure_time" required value="<?php echo $edit_sched ? $edit_sched['departure_time'] : ''; ?>">
        </div>
        <div class="form-group">
            <label>Arrival Time</label>
            <input type="time" name="arrival_time" required value="<?php echo $edit_sched ? $edit_sched['arrival_time'] : ''; ?>">
        </div>
        <div class="form-group">
            <label>Days of Week</label>
            <input type="text" name="days_of_week" value="<?php echo $edit_sched ? $edit_sched['days_of_week'] : 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'; ?>" placeholder="Mon,Tue,...">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <?php foreach (['On Time','Delayed','Cancelled'] as $st): ?>
                <option value="<?php echo $st; ?>" <?php echo ($edit_sched && $edit_sched['status'] === $st) ? 'selected' : ''; ?>><?php echo $st; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><?php echo $edit_sched ? 'Update' : 'Add Schedule'; ?></button>
            <?php if ($edit_sched): ?>
                <a href="manage_schedules.php" class="btn" style="background:#E8EDF5;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Schedules Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Train</th>
                <th>From</th>
                <th>To</th>
                <th>Depart</th>
                <th>Arrive</th>
                <th>Days</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($sc = mysqli_fetch_assoc($schedules_list)): ?>
            <tr>
                <td><?php echo $sc['schedule_id']; ?></td>
                <td><strong><?php echo $sc['train_number']; ?></strong></td>
                <td><?php echo $sc['dep_name']; ?></td>
                <td><?php echo $sc['arr_name']; ?></td>
                <td><?php echo date('H:i', strtotime($sc['departure_time'])); ?></td>
                <td><?php echo date('H:i', strtotime($sc['arrival_time'])); ?></td>
                <td><small><?php echo $sc['days_of_week']; ?></small></td>
                <td><span class="status status-<?php echo strtolower(str_replace(' ','',$sc['status'])); ?>"><?php echo $sc['status']; ?></span></td>
                <td>
                    <a href="manage_schedules.php?edit=<?php echo $sc['schedule_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="manage_schedules.php?delete=<?php echo $sc['schedule_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this schedule?')">Delete</a>
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
