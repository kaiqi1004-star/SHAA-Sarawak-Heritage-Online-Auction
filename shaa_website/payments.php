<?php
require 'config.php';
$pageTitle = 'Payments - SHAA';
$payments = $pdo->query("SELECT p.*, o.Item_ID, i.Item_Name FROM payment p LEFT JOIN orders o ON p.Order_ID=o.Order_ID LEFT JOIN item i ON o.Item_ID=i.Item_ID ORDER BY p.Payment_Date_Time DESC")->fetchAll();
include 'header.php';
?>
<h2>Payments</h2>
<section class="panel">
<table><tr><th>Payment ID</th><th>Order ID</th><th>Item</th><th>Method</th><th>Reference</th><th>Amount</th><th>Status</th><th>Date</th></tr>
<?php foreach($payments as $p): ?><tr><td><?= e($p['Payment_ID']) ?></td><td><?= e($p['Order_ID']) ?></td><td><?= e($p['Item_Name']) ?></td><td><?= e($p['Payment_Method']) ?></td><td><?= e($p['Payment_Transaction_Reference']) ?></td><td>RM <?= e($p['Payment_Amount']) ?></td><td><span class="badge"><?= e($p['Payment_Status']) ?></span></td><td><?= e($p['Payment_Date_Time']) ?></td></tr><?php endforeach; ?>
</table>
</section>
<?php include 'footer.php'; ?>
