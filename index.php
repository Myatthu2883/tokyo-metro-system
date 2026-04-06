<?php
$page_title = 'Home';
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Get line info
$lines = mysqli_query($conn, "SELECT * FROM metro_lines ORDER BY line_id");

// Get counts
$station_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM stations"))['c'];
$train_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM trains"))['c'];
$schedule_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM schedules"))['c'];
$user_count    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='Passenger'"))['c'];
?>

<!-- Hero Section -->
<div class="hero">
    <h1>Welcome to Tokyo Metro Management System</h1>
    <p>A centralized web-based system for managing train schedules, stations, passengers, and ticket bookings across all 9 Tokyo Metro lines.</p>
    <div class="line-colors-bar" style="margin-top: 28px;">
        <span style="background:#FF9500"></span>
        <span style="background:#F62E36"></span>
        <span style="background:#B5B5AC"></span>
        <span style="background:#009BBF"></span>
        <span style="background:#00BB85"></span>
        <span style="background:#C1A470"></span>
        <span style="background:#8F76D6"></span>
        <span style="background:#00AC9B"></span>
        <span style="background:#9C5E31"></span>
    </div>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card">
        <div class="card-label">Metro Lines</div>
        <div class="card-value">9</div>
    </div>
    <div class="stat-card">
        <div class="card-label">Stations</div>
        <div class="card-value"><?php echo $station_count; ?></div>
    </div>
    <div class="stat-card">
        <div class="card-label">Active Trains</div>
        <div class="card-value"><?php echo $train_count; ?></div>
    </div>
    <div class="stat-card">
        <div class="card-label">Schedules</div>
        <div class="card-value"><?php echo $schedule_count; ?></div>
    </div>
</div>

<!-- Metro Lines -->
<h2 class="section-title">Metro Lines</h2>
<div class="lines-grid">
    <?php while ($line = mysqli_fetch_assoc($lines)): ?>
    <div class="line-card">
        <div class="line-circle" style="background:<?php echo $line['color_hex']; ?>">
            <?php echo $line['line_code']; ?>
        </div>
        <div class="line-info">
            <div class="line-name"><?php echo htmlspecialchars($line['line_name']); ?></div>
            <div class="line-stations"><?php echo $line['total_stations']; ?> stations &bull;
                <span class="status status-<?php echo strtolower($line['status']); ?>">
                    <?php echo $line['status']; ?>
                </span>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Quick Links -->
<h2 class="section-title">Quick Access</h2>
<div class="card-grid">
    <a href="schedules.php" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-header">
            <div class="card-icon" style="background:var(--tozai);">🕐</div>
            <div class="card-title">View Schedules</div>
        </div>
        <p style="font-size:13px;color:var(--text-light);">Browse all train schedules with departure and arrival times across all metro lines.</p>
    </a>
    <a href="stations.php" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-header">
            <div class="card-icon" style="background:var(--chiyoda);">🚉</div>
            <div class="card-title">Station Directory</div>
        </div>
        <p style="font-size:13px;color:var(--text-light);">Explore all stations organized by metro line with status information.</p>
    </a>
    <a href="<?php echo isset($_SESSION['user_id']) ? 'book_ticket.php' : 'login.php'; ?>" class="card" style="text-decoration:none;color:inherit;">
        <div class="card-header">
            <div class="card-icon" style="background:var(--ginza);">🎫</div>
            <div class="card-title">Book a Ticket</div>
        </div>
        <p style="font-size:13px;color:var(--text-light);">Search routes, check fares, and book your tickets online.</p>
    </a>
</div>

<p style="text-align:center;color:var(--text-light);font-size:13px;margin-top:16px;">
    Reference: <a href="https://www.tokyometro.jp/en/index.html" target="_blank">Tokyo Metro Official Website</a>
</p>

<?php require_once 'includes/footer.php'; ?>
