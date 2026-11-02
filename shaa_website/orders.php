<?php
require 'config.php';
$pageTitle = 'Orders - SHAA';
$orders = $pdo->query("SELECT o.*, buyer.Username AS Buyer, i.Item_Name, seller.Username AS Seller
                       FROM orders o
                       LEFT JOIN `user` buyer ON o.User_ID = buyer.User_ID
                       LEFT JOIN item i ON o.Item_ID = i.Item_ID
                       LEFT JOIN `user` seller ON i.User_ID = seller.User_ID
                       ORDER BY o.Order_Date_Time DESC")->fetchAll();
include 'header.php';
?>
<h2>Orders</h2>
<section class="panel">
<table><tr><th>Order ID</th><th>Buyer</th><th>Seller</th><th>Item</th><th>Type</th><th>Total</th><th>Status</th><th>Date</th></tr>
<?php foreach($orders as $o): ?><tr><td><?= e($o['Order_ID']) ?></td><td><?= e($o['Buyer']) ?></td><td><?= e($o['Seller']) ?></td><td><?= e($o['Item_Name']) ?></td><td><?= e($o['Order_Type']) ?></td><td>RM <?= e($o['Order_Total_Amount']) ?></td><td><span class="badge"><?= e($o['Order_Status']) ?></span></td><td><?= e($o['Order_Date_Time']) ?></td></tr><?php endforeach; ?>
</table>
</section>
<?php include 'footer.php'; ?>
