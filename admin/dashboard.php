<?php
$page_title = 'Admin Dashboard';
require_once '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    header('Location: ../login.php');
    exit;
}

// Stats
$total_lines    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM metro_lines"))['c'];
$total_stations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM stations"))['c'];
$total_trains   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM trains"))['c'];
$total_tickets  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$active_trains  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM trains WHERE status='Running'"))['c'];
$booked_tickets = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tickets WHERE status='Booked'"))['c'];
$revenue        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(price),0) as r FROM tickets WHERE status IN ('Booked','Used')"))['r'];

// Recent tickets
$recent_tickets = mysqli_query($conn, "
    SELECT tk.*, u.full_name,
           dep.station_name AS dep_name, arr.station_name AS arr_name,
           ml.line_name, ml.color_hex
    FROM tickets tk
    JOIN users u ON tk.user_id = u.user_id
    JOIN schedules s ON tk.schedule_id = s.schedule_id
    JOIN trains t ON s.train_id = t.train_id
    JOIN metro_lines ml ON t.line_id = ml.line_id
    JOIN stations dep ON s.departure_station_id = dep.station_id
    JOIN stations arr ON s.arrival_station_id = arr.station_id
    ORDER BY tk.created_at DESC LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Tokyo Metro Management</title>
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
            <span class="nav-user">
                <span class="user-badge <?php echo strtolower($_SESSION['role']); ?>"><?php echo $_SESSION['role']; ?></span>
                <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </nav>
    </div>
</header>

<main class="main-content">

<h2 class="section-title">Dashboard Overview</h2>

<!-- Stats Row -->
<div class="stats-row">
    <div class="stat-card">
        <div class="card-label">Total Lines</div>
        <div class="card-value"><?php echo $total_lines; ?></div>
    </div>
    <div class="stat-card">
        <div class="card-label">Stations</div>
        <div class="card-value"><?php echo $total_stations; ?></div>
    </div>
    <div class="stat-card">
        <div class="card-label">Trains Running</div>
        <div class="card-value"><?php echo $active_trains; ?> / <?php echo $total_trains; ?></div>
    </div>
    <div class="stat-card">
        <div class="card-label">Revenue (¥)</div>
        <div class="card-value">¥<?php echo number_format($revenue); ?></div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="card-grid">
    <div class="card">
        <div class="card-header">
            <div class="card-icon" style="background:var(--tozai);">👥</div>
            <div>
                <div class="card-title">Total Users</div>
                <div class="card-value"><?php echo $total_users; ?></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-icon" style="background:var(--ginza);">🎫</div>
            <div>
                <div class="card-title">Total Tickets</div>
                <div class="card-value"><?php echo $total_tickets; ?></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-icon" style="background:var(--chiyoda);">✅</div>
            <div>
                <div class="card-title">Active Bookings</div>
                <div class="card-value"><?php echo $booked_tickets; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tickets -->
<div class="table-container">
    <div class="table-header">
        <h2>Recent Ticket Bookings</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Passenger</th>
                <th>Line</th>
                <th>Route</th>
                <th>Type</th>
                <th>Price</th>
                <th>Travel Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($tk = mysqli_fetch_assoc($recent_tickets)): ?>
            <tr>
                <td>#<?php echo $tk['ticket_id']; ?></td>
                <td><?php echo htmlspecialchars($tk['full_name']); ?></td>
                <td>
                    <span class="line-badge" style="background:<?php echo $tk['color_hex']; ?>;">
                        <?php echo $tk['line_name']; ?>
                    </span>
                </td>
                <td><?php echo $tk['dep_name']; ?> → <?php echo $tk['arr_name']; ?></td>
                <td><?php echo $tk['ticket_type']; ?></td>
                <td><strong>¥<?php echo number_format($tk['price']); ?></strong></td>
                <td><?php echo date('M j, Y', strtotime($tk['travel_date'])); ?></td>
                <td><span class="status status-<?php echo strtolower($tk['status']); ?>"><?php echo $tk['status']; ?></span></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>

<footer class="main-footer">
    <div class="footer-inner">
        <div class="footer-brand"><span class="logo-mark">◆</span> Tokyo Metro Railway Management System</div>
        <div class="footer-info">
            <p>&copy; 2026 Team Shinkansen</p>
        </div>
    </div>
</footer>

<script src="../js/main.js"></script>
</body>
</html>
