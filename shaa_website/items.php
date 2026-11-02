<?php
require 'config.php';
$pageTitle = 'Items - SHAA';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO item (Item_ID, User_ID, Category_ID, Item_Name, Item_Description, Item_Status, Item_Buy_Now_Price, Auction_Item_Starting_Price, Auction_Item_Reserve_Price, Auction_Item_Start_Date, Auction_Item_End_Date)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['Item_ID'], $_POST['User_ID'], $_POST['Category_ID'], $_POST['Item_Name'], $_POST['Item_Description'], $_POST['Item_Status'], nullIfEmpty($_POST['Item_Buy_Now_Price']), nullIfEmpty($_POST['Auction_Item_Starting_Price']), nullIfEmpty($_POST['Auction_Item_Reserve_Price']), $_POST['Auction_Item_Start_Date'], $_POST['Auction_Item_End_Date']]);
        $message = 'Item added successfully.';
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE item SET User_ID=?, Category_ID=?, Item_Name=?, Item_Description=?, Item_Status=?, Item_Buy_Now_Price=?, Auction_Item_Starting_Price=?, Auction_Item_Reserve_Price=?, Auction_Item_Start_Date=?, Auction_Item_End_Date=? WHERE Item_ID=?");
        $stmt->execute([$_POST['User_ID'], $_POST['Category_ID'], $_POST['Item_Name'], $_POST['Item_Description'], $_POST['Item_Status'], nullIfEmpty($_POST['Item_Buy_Now_Price']), nullIfEmpty($_POST['Auction_Item_Starting_Price']), nullIfEmpty($_POST['Auction_Item_Reserve_Price']), $_POST['Auction_Item_Start_Date'], $_POST['Auction_Item_End_Date'], $_POST['Item_ID']]);
        $message = 'Item updated successfully.';
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM item WHERE Item_ID=?");
            $stmt->execute([$_POST['Item_ID']]);
            $message = 'Item deleted successfully.';
        } catch (PDOException $e) {
            $message = 'Cannot delete this item because related records exist.';
        }
    }
}
function nullIfEmpty($v) { return $v === '' ? null : $v; }

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM item WHERE Item_ID=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}
$items = $pdo->query("SELECT i.*, u.Username, c.Category_Description AS Category_Name
                      FROM item i
                      LEFT JOIN `user` u ON i.User_ID = u.User_ID
                      LEFT JOIN category c ON i.Category_ID = c.Category_ID
                      ORDER BY i.Item_ID")->fetchAll();
$sellers = $pdo->query("SELECT User_ID, Username FROM `user` WHERE User_Type IN ('Seller','Both') ORDER BY Username")->fetchAll();
$categories = $pdo->query("SELECT Category_ID, Category_Description FROM category ORDER BY Category_ID")->fetchAll();
include 'header.php';
?>
<h2>Manage Auction Items</h2>
<?php if ($message): ?><p class="message"><?= e($message) ?></p><?php endif; ?>
<section class="panel">
    <h3><?= $edit ? 'Update Item' : 'Add New Item' ?></h3>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
        <label>Item ID <input name="Item_ID" required value="<?= e($edit['Item_ID'] ?? '') ?>" <?= $edit ? 'readonly' : '' ?>></label>
        <label>Seller
            <select name="User_ID" required>
                <?php foreach($sellers as $s): ?><option value="<?= e($s['User_ID']) ?>" <?= (($edit['User_ID'] ?? '') == $s['User_ID']) ? 'selected' : '' ?>><?= e($s['Username']) ?> (<?= e($s['User_ID']) ?>)</option><?php endforeach; ?>
            </select>
        </label>
        <label>Category
            <select name="Category_ID" required>
                <?php foreach($categories as $c): ?><option value="<?= e($c['Category_ID']) ?>" <?= (($edit['Category_ID'] ?? '') == $c['Category_ID']) ? 'selected' : '' ?>><?= e($c['Category_Description']) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Item Name <input name="Item_Name" required value="<?= e($edit['Item_Name'] ?? '') ?>"></label>
        <label>Description <input name="Item_Description" value="<?= e($edit['Item_Description'] ?? '') ?>"></label>
        <label>Status <select name="Item_Status"><option>Active</option><option <?= (($edit['Item_Status'] ?? '')=='Sold out')?'selected':'' ?>>Sold out</option></select></label>
        <label>Buy Now Price <input type="number" step="0.01" name="Item_Buy_Now_Price" value="<?= e($edit['Item_Buy_Now_Price'] ?? '') ?>"></label>
        <label>Starting Price <input type="number" step="0.01" name="Auction_Item_Starting_Price" value="<?= e($edit['Auction_Item_Starting_Price'] ?? '') ?>"></label>
        <label>Reserve Price <input type="number" step="0.01" name="Auction_Item_Reserve_Price" value="<?= e($edit['Auction_Item_Reserve_Price'] ?? '') ?>"></label>
        <label>Start Date <input type="date" name="Auction_Item_Start_Date" value="<?= e($edit['Auction_Item_Start_Date'] ?? date('Y-m-d')) ?>"></label>
        <label>End Date <input type="date" name="Auction_Item_End_Date" value="<?= e($edit['Auction_Item_End_Date'] ?? date('Y-m-d')) ?>"></label>
        <button type="submit"><?= $edit ? 'Update Item' : 'Add Item' ?></button>
        <?php if ($edit): ?><a class="button secondary" href="items.php">Cancel</a><?php endif; ?>
    </form>
</section>
<section class="panel wide">
    <h3>Item List</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Seller</th><th>Category</th><th>Status</th><th>Buy Now</th><th>Start Price</th><th>Period</th><th>Action</th></tr>
        <?php foreach ($items as $i): ?>
        <tr>
            <td><?= e($i['Item_ID']) ?></td><td><?= e($i['Item_Name']) ?></td><td><?= e($i['Username']) ?></td><td><?= e($i['Category_Name']) ?></td>
            <td><span class="badge"><?= e($i['Item_Status']) ?></span></td><td><?= e($i['Item_Buy_Now_Price']) ?></td><td><?= e($i['Auction_Item_Starting_Price']) ?></td>
            <td><?= e($i['Auction_Item_Start_Date']) ?> to <?= e($i['Auction_Item_End_Date']) ?></td>
            <td class="actions"><a class="button small" href="items.php?edit=<?= e($i['Item_ID']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this item?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="Item_ID" value="<?= e($i['Item_ID']) ?>"><button class="danger small" type="submit">Delete</button></form></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php include 'footer.php'; ?>
