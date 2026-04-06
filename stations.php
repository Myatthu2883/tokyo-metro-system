<?php
$page_title = 'Stations';
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$sql = "SELECT s.*, ml.line_name, ml.line_code, ml.color_hex 
        FROM stations s 
        JOIN metro_lines ml ON s.line_id = ml.line_id 
        ORDER BY ml.line_id, s.station_order";
$stations = mysqli_query($conn, $sql);

// Group by line
$grouped = [];
while ($row = mysqli_fetch_assoc($stations)) {
    $grouped[$row['line_name']][] = $row;
}
?>

<h2 class="section-title">Station Directory</h2>
<p style="color:var(--text-light);margin-bottom:24px;">All stations organized by metro line. Referenced from <a href="https://www.tokyometro.jp/en/route_station/index.html" target="_blank">Tokyo Metro Route/Station Information</a>.</p>

<?php foreach ($grouped as $line_name => $stns): ?>
    <?php $first = $stns[0]; ?>
    <div class="table-container">
        <div class="table-header">
            <h2 style="display:flex;align-items:center;gap:10px;">
                <span class="line-circle" style="background:<?php echo $first['color_hex']; ?>;width:28px;height:28px;font-size:12px;">
                    <?php echo $first['line_code']; ?>
                </span>
                <?php echo htmlspecialchars($line_name); ?>
            </h2>
            <span style="font-size:13px;color:var(--text-light);"><?php echo count($stns); ?> stations listed</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Station Name</th>
                    <th>Order</th>
                    <th>Transfer</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stns as $s): ?>
                <tr>
                    <td>
                        <span class="line-badge" style="background:<?php echo $s['color_hex']; ?>;">
                            <?php echo $s['station_code']; ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($s['station_name']); ?></strong></td>
                    <td><?php echo $s['station_order']; ?></td>
                    <td><?php echo $s['is_transfer'] ? '🔄 Yes' : '—'; ?></td>
                    <td>
                        <span class="status status-<?php echo strtolower(str_replace(' ', '', $s['status'])); ?>">
                            <?php echo $s['status']; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>
