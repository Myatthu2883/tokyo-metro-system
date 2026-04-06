<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Tokyo Metro Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="header-inner">
        <a href="index.php" class="logo">
            <span class="logo-mark">◆</span>
            <span class="logo-text">Tokyo Metro</span>
            <span class="logo-sub">Railway Management System</span>
        </a>
        <nav class="main-nav">
            <a href="index.php">Home</a>
            <a href="schedules.php">Schedules</a>
            <a href="stations.php">Stations</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Passenger'): ?>
                <a href="book_ticket.php">Book Ticket</a>
                <a href="my_tickets.php">My Tickets</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Staff'])): ?>
                <a href="admin/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-user">
                    <span class="user-badge <?php echo strtolower($_SESSION['role']); ?>"><?php echo $_SESSION['role']; ?></span>
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
