<?php
$page_title = 'My Tickets';
require_once 'includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Passenger') {
    header('Location: login.php');
    exit;
}

// Handle cancel
if (isset($_GET['cancel'])) {
    $tid = (int)$_GET['cancel'];
    mysqli_query($conn, "UPDATE tickets SET status='Cancelled' WHERE ticket_id=$tid AND user_id={$_SESSION['user_id']}");
    header('Location: my_tickets.php');
    exit;
}

$sql = "SELECT tk.*, 
        s.departure_time, s.arrival_time,
        t.train_number, t.train_type,
        ml.line_name, ml.color_hex,
        dep.station_name AS dep_name,
        arr.station_name AS arr_name
        FROM tickets tk
        JOIN schedules s ON tk.schedule_id = s.schedule_id
        JOIN trains t ON s.train_id = t.train_id
        JOIN metro_lines ml ON t.line_id = ml.line_id
        JOIN stations dep ON s.departure_station_id = dep.station_id
        JOIN stations arr ON s.arrival_station_id = arr.station_id
        WHERE tk.user_id = {$_SESSION['user_id']}
        ORDER BY tk.travel_date DESC, s.departure_time";
$tickets = mysqli_query($conn, $sql);

require_once 'includes/header.php';
?>

<h2 class="section-title">My Tickets</h2>

<?php if (mysqli_num_rows($tickets) === 0): ?>
    <div class="alert alert-info">You haven't booked any tickets yet. <a href="book_ticket.php">Book one now!</a></div>
<?php else: ?>
    <?php while ($tk = mysqli_fetch_assoc($tickets)): ?>
    <div class="ticket-card">
        <div class="ticket-color-bar" style="background:<?php echo $tk['color_hex']; ?>;"></div>
        <div class="ticket-body">
            <div>
                <div class="ticket-route">
                    <span><?php echo $tk['dep_name']; ?></span>
                    <span class="ticket-arrow">→</span>
                    <span><?php echo $tk['arr_name']; ?></span>
                </div>
                <div class="ticket-details">
                    <?php echo $tk['line_name']; ?> &bull; 
                    Train <?php echo $tk['train_number']; ?> (<?php echo $tk['train_type']; ?>) &bull; 
                    <?php echo date('H:i', strtotime($tk['departure_time'])); ?> - <?php echo date('H:i', strtotime($tk['arrival_time'])); ?>
                </div>
            </div>
            <div>
                <div class="ticket-details">
                    📅 <?php echo date('D, M j, Y', strtotime($tk['travel_date'])); ?> &bull; 
                    <?php echo $tk['ticket_type']; ?>
                </div>
                <span class="status status-<?php echo strtolower($tk['status']); ?>"><?php echo $tk['status']; ?></span>
            </div>
            <div style="text-align:right;">
                <div class="ticket-price">¥<?php echo number_format($tk['price']); ?></div>
                <?php if ($tk['status'] === 'Booked'): ?>
                    <a href="my_tickets.php?cancel=<?php echo $tk['ticket_id']; ?>" class="btn btn-danger btn-sm cancel-ticket" style="margin-top:6px;">Cancel</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
