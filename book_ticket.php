<?php
$page_title = 'Book Ticket';
require_once 'includes/db_connect.php';
session_start();

// Must be logged in as Passenger
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Passenger') {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Get all schedules for dropdown
$schedules = mysqli_query($conn, "
    SELECT s.schedule_id, s.departure_time, s.arrival_time, s.days_of_week,
           t.train_number, t.train_type,
           ml.line_name, ml.color_hex,
           dep.station_name AS dep_name, 
           arr.station_name AS arr_name
    FROM schedules s
    JOIN trains t ON s.train_id = t.train_id
    JOIN metro_lines ml ON t.line_id = ml.line_id
    JOIN stations dep ON s.departure_station_id = dep.station_id
    JOIN stations arr ON s.arrival_station_id = arr.station_id
    WHERE s.status = 'On Time'
    ORDER BY ml.line_id, s.departure_time
");

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = (int)$_POST['schedule_id'];
    $ticket_type = mysqli_real_escape_string($conn, $_POST['ticket_type']);
    $travel_date = mysqli_real_escape_string($conn, $_POST['travel_date']);
    $user_id = $_SESSION['user_id'];
    $booking_date = date('Y-m-d');

    // Calculate price
    $prices = ['Single' => 170, 'Return' => 340, 'Day Pass' => 600];
    $price = $prices[$ticket_type] ?? 170;

    $sql = "INSERT INTO tickets (user_id, schedule_id, ticket_type, price, booking_date, travel_date, status) 
            VALUES ($user_id, $schedule_id, '$ticket_type', $price, '$booking_date', '$travel_date', 'Booked')";
    
    if (mysqli_query($conn, $sql)) {
        $success = 'Ticket booked successfully! View your tickets in <a href="my_tickets.php">My Tickets</a>.';
    } else {
        $error = 'Booking failed. Please try again.';
    }
}

require_once 'includes/header.php';
?>

<div class="booking-card">
    <h2>🎫 Book a Ticket</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Select Route</label>
            <select name="schedule_id" required>
                <option value="">-- Choose a route --</option>
                <?php 
                mysqli_data_seek($schedules, 0);
                while ($s = mysqli_fetch_assoc($schedules)): ?>
                <option value="<?php echo $s['schedule_id']; ?>">
                    [<?php echo $s['line_name']; ?>] <?php echo $s['dep_name']; ?> → <?php echo $s['arr_name']; ?> 
                    (<?php echo date('H:i', strtotime($s['departure_time'])); ?> - <?php echo date('H:i', strtotime($s['arrival_time'])); ?>) 
                    <?php echo $s['train_type']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Ticket Type</label>
            <select name="ticket_type" required id="ticket-type">
                <option value="Single">Single Trip — ¥170</option>
                <option value="Return">Return Trip — ¥340</option>
                <option value="Day Pass">Day Pass — ¥600</option>
            </select>
        </div>
        <div class="form-group">
            <label>Travel Date</label>
            <input type="date" name="travel_date" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="fare-display">
            <div class="fare-label">Estimated Fare</div>
            <div class="fare-amount" id="fare-display">¥170</div>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Confirm Booking</button>
    </form>
</div>

<script>
document.getElementById('ticket-type').addEventListener('change', function() {
    var prices = { 'Single': 170, 'Return': 340, 'Day Pass': 600 };
    document.getElementById('fare-display').textContent = '¥' + prices[this.value];
});
</script>

<?php require_once 'includes/footer.php'; ?>
