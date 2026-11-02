<?php
require 'config.php';
$pageTitle = 'Dashboard - SHAA';
include 'header.php';

$cards = [
    'Users' => $pdo->query("SELECT COUNT(*) FROM `user`")->fetchColumn(),
    'Items' => $pdo->query("SELECT COUNT(*) FROM item")->fetchColumn(),
    'Bids' => $pdo->query("SELECT COUNT(*) FROM bid")->fetchColumn(),
    'Orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'Payments' => $pdo->query("SELECT COUNT(*) FROM payment")->fetchColumn(),
    'Deliveries' => $pdo->query("SELECT COUNT(*) FROM delivery")->fetchColumn(),
];

$latestItems = $pdo->query("SELECT i.Item_ID, i.Item_Name, c.Category_Description AS Category_Name, u.Username AS Seller, i.Item_Status
                           FROM item i
                           LEFT JOIN category c ON i.Category_ID = c.Category_ID
                           LEFT JOIN `user` u ON i.User_ID = u.User_ID
                           ORDER BY i.Item_ID DESC LIMIT 5")->fetchAll();
?>
<section class="hero">
    <h2>Database Dashboard</h2>
    <p>Manage users, auction items, bids, orders, payments and view required reports.</p>
</section>

<div class="cards">
    <?php foreach ($cards as $label => $count): ?>
        <div class="card">
            <h3><?= e($count) ?></h3>
            <p><?= e($label) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<section class="panel">
    <h2>Latest Items</h2>
    <table>
        <tr><th>ID</th><th>Item</th><th>Category</th><th>Seller</th><th>Status</th></tr>
        <?php foreach ($latestItems as $row): ?>
            <tr>
                <td><?= e($row['Item_ID']) ?></td>
                <td><?= e($row['Item_Name']) ?></td>
                <td><?= e($row['Category_Name']) ?></td>
                <td><?= e($row['Seller']) ?></td>
                <td><span class="badge"><?= e($row['Item_Status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php include 'footer.php'; ?>
