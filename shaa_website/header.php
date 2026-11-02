<?php if (!isset($pageTitle)) { $pageTitle = 'SHAA Database System'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <div>
        <h1>Sarawak Heritage Online Auction</h1>
        <p>Simple PHP + MySQL database application</p>
    </div>
</header>
<nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="users.php">Users</a>
    <a href="items.php">Items</a>
    <a href="bids.php">Bids</a>
    <a href="orders.php">Orders</a>
    <a href="payments.php">Payments</a>
    <a href="reports.php">Reports</a>
</nav>
<main class="container">
