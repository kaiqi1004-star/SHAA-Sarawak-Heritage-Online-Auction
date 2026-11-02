<?php
require 'config.php';
$pageTitle = 'Bids - SHAA';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO bid (Bid_ID, Item_ID, User_ID, Bid_Date_Time, Winning_Bid, Bid_Amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['Bid_ID'], $_POST['Item_ID'], $_POST['User_ID'], $_POST['Bid_Date_Time'], $_POST['Winning_Bid'], $_POST['Bid_Amount']]);
        $message = 'Bid added successfully.';
    } elseif ($_POST['action'] === 'delete') {
        try { $pdo->prepare("DELETE FROM bid WHERE Bid_ID=?")->execute([$_POST['Bid_ID']]); $message='Bid deleted successfully.'; }
        catch (PDOException $e) { $message='Cannot delete this bid because related order exists.'; }
    }
}
$bids = $pdo->query("SELECT b.*, u.Username AS Bidder, i.Item_Name FROM bid b LEFT JOIN `user` u ON b.User_ID=u.User_ID LEFT JOIN item i ON b.Item_ID=i.Item_ID ORDER BY b.Bid_Date_Time DESC")->fetchAll();
$items = $pdo->query("SELECT Item_ID, Item_Name FROM item ORDER BY Item_ID")->fetchAll();
$buyers = $pdo->query("SELECT User_ID, Username FROM `user` WHERE User_Type IN ('Buyer','Both') ORDER BY Username")->fetchAll();
include 'header.php';
?>
<h2>Manage Bids</h2>
<?php if ($message): ?><p class="message"><?= e($message) ?></p><?php endif; ?>
<section class="panel">
<h3>Add Bid</h3>
<form method="post" class="form-grid">
    <input type="hidden" name="action" value="add">
    <label>Bid ID <input name="Bid_ID" required placeholder="B006"></label>
    <label>Item <select name="Item_ID"><?php foreach($items as $i): ?><option value="<?= e($i['Item_ID']) ?>"><?= e($i['Item_ID']) ?> - <?= e($i['Item_Name']) ?></option><?php endforeach; ?></select></label>
    <label>Bidder <select name="User_ID"><?php foreach($buyers as $b): ?><option value="<?= e($b['User_ID']) ?>"><?= e($b['Username']) ?> (<?= e($b['User_ID']) ?>)</option><?php endforeach; ?></select></label>
    <label>Date Time <input type="datetime-local" name="Bid_Date_Time" required></label>
    <label>Winning Bid <select name="Winning_Bid"><option value="0">No</option><option value="1">Yes</option></select></label>
    <label>Bid Amount <input type="number" step="0.01" name="Bid_Amount" required></label>
    <button>Add Bid</button>
</form>
</section>
<section class="panel">
<h3>Bidding Records</h3>
<table><tr><th>ID</th><th>Item</th><th>Bidder</th><th>Date Time</th><th>Winning</th><th>Amount</th><th>Action</th></tr>
<?php foreach($bids as $b): ?><tr><td><?= e($b['Bid_ID']) ?></td><td><?= e($b['Item_Name']) ?></td><td><?= e($b['Bidder']) ?></td><td><?= e($b['Bid_Date_Time']) ?></td><td><?= $b['Winning_Bid'] == '1' ? 'Yes' : 'No' ?></td><td>RM <?= e($b['Bid_Amount']) ?></td><td><form method="post" onsubmit="return confirm('Delete this bid?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="Bid_ID" value="<?= e($b['Bid_ID']) ?>"><button class="danger small">Delete</button></form></td></tr><?php endforeach; ?>
</table>
</section>
<?php include 'footer.php'; ?>
