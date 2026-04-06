<?php
$page_title = 'Train Schedules';
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Filter by line
$line_filter = isset($_GET['line']) ? (int)$_GET['line'] : 0;
$where = $line_filter ? "WHERE t.line_id = $line_filter" : "";

$sql = "SELECT s.*, 
        t.train_number, t.train_type,
        ml.line_name, ml.line_code, ml.color_hex,
        dep.station_name AS dep_name, dep.station_code AS dep_code,
        arr.station_name AS arr_name, arr.station_code AS arr_code
        FROM schedules s
        JOIN trains t ON s.train_id = t.train_id
        JOIN metro_lines ml ON t.line_id = ml.line_id
        JOIN stations dep ON s.departure_station_id = dep.station_id
        JOIN stations arr ON s.arrival_station_id = arr.station_id
        $where
        ORDER BY ml.line_id, s.departure_time";

$schedules = mysqli_query($conn, $sql);
$lines = mysqli_query($conn, "SELECT * FROM metro_lines ORDER BY line_id");
?>

<h2 class="section-title">Train Schedules</h2>

<!-- Filter -->
<div style="margin-bottom:20px;display:flex;gap:8px;flex-wrap:wrap;">
    <a href="schedules.php" class="btn btn-sm <?php echo !$line_filter ? 'btn-primary' : ''; ?>" style="<?php echo $line_filter ? 'background:#E8EDF5;color:var(--text);' : ''; ?>">All Lines</a>
    <?php while ($l = mysqli_fetch_assoc($lines)): ?>
    <a href="schedules.php?line=<?php echo $l['line_id']; ?>" 
       class="btn btn-sm"
       style="background:<?php echo $line_filter == $l['line_id'] ? $l['color_hex'] : '#E8EDF5'; ?>;color:<?php echo $line_filter == $l['line_id'] ? '#fff' : 'var(--text)'; ?>;">
        <?php echo $l['line_code']; ?> <?php echo $l['line_name']; ?>
    </a>
    <?php endwhile; ?>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Line</th>
                <th>Train</th>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Days</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($schedules) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($schedules)): ?>
            <tr>
                <td>
                    <span class="line-badge" style="background:<?php echo $row['color_hex']; ?>;">
                        <?php echo $row['line_code']; ?>
                    </span>
                </td>
                <td><strong><?php echo $row['train_number']; ?></strong></td>
                <td><?php echo $row['train_type']; ?></td>
                <td><?php echo $row['dep_name']; ?> <small style="color:var(--text-light);">(<?php echo $row['dep_code']; ?>)</small></td>
                <td><?php echo $row['arr_name']; ?> <small style="color:var(--text-light);">(<?php echo $row['arr_code']; ?>)</small></td>
                <td><strong><?php echo date('H:i', strtotime($row['departure_time'])); ?></strong></td>
                <td><strong><?php echo date('H:i', strtotime($row['arrival_time'])); ?></strong></td>
                <td><small><?php echo $row['days_of_week']; ?></small></td>
                <td>
                    <?php 
                    $sc = strtolower(str_replace(' ', '', $row['status']));
                    ?>
                    <span class="status status-<?php echo $sc; ?>"><?php echo $row['status']; ?></span>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-light);">No schedules found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
