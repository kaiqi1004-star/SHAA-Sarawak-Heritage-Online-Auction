<?php
require 'config.php';
$pageTitle = 'Users - SHAA';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO `user` (User_ID, Username, User_Email, User_Phone, User_Region, User_Registration_Date, User_Type)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['User_ID'], $_POST['Username'], $_POST['User_Email'], $_POST['User_Phone'], $_POST['User_Region'], $_POST['User_Registration_Date'], $_POST['User_Type']]);
        $message = 'User added successfully.';
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE `user` SET Username=?, User_Email=?, User_Phone=?, User_Region=?, User_Registration_Date=?, User_Type=? WHERE User_ID=?");
        $stmt->execute([$_POST['Username'], $_POST['User_Email'], $_POST['User_Phone'], $_POST['User_Region'], $_POST['User_Registration_Date'], $_POST['User_Type'], $_POST['User_ID']]);
        $message = 'User updated successfully.';
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM `user` WHERE User_ID=?");
            $stmt->execute([$_POST['User_ID']]);
            $message = 'User deleted successfully.';
        } catch (PDOException $e) {
            $message = 'Cannot delete this user because related records exist.';
        }
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM `user` WHERE User_ID=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}
$users = $pdo->query("SELECT * FROM `user` ORDER BY User_ID")->fetchAll();
include 'header.php';
?>
<h2>Manage Users</h2>
<?php if ($message): ?><p class="message"><?= e($message) ?></p><?php endif; ?>
<section class="panel">
    <h3><?= $edit ? 'Update User' : 'Add New User' ?></h3>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
        <label>User ID <input name="User_ID" required value="<?= e($edit['User_ID'] ?? '') ?>" <?= $edit ? 'readonly' : '' ?>></label>
        <label>Username <input name="Username" required value="<?= e($edit['Username'] ?? '') ?>"></label>
        <label>Email <input type="email" name="User_Email" value="<?= e($edit['User_Email'] ?? '') ?>"></label>
        <label>Phone <input name="User_Phone" value="<?= e($edit['User_Phone'] ?? '') ?>"></label>
        <label>Region <input name="User_Region" value="<?= e($edit['User_Region'] ?? '') ?>"></label>
        <label>Registration Date <input type="date" name="User_Registration_Date" value="<?= e($edit['User_Registration_Date'] ?? date('Y-m-d')) ?>"></label>
        <label>User Type
            <select name="User_Type">
                <?php foreach(['Buyer','Seller','Both'] as $type): ?>
                    <option value="<?= $type ?>" <?= (($edit['User_Type'] ?? '') === $type) ? 'selected' : '' ?>><?= $type ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit"><?= $edit ? 'Update User' : 'Add User' ?></button>
        <?php if ($edit): ?><a class="button secondary" href="users.php">Cancel</a><?php endif; ?>
    </form>
</section>
<section class="panel">
    <h3>User List</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Region</th><th>Date</th><th>Type</th><th>Action</th></tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['User_ID']) ?></td><td><?= e($u['Username']) ?></td><td><?= e($u['User_Email']) ?></td>
            <td><?= e($u['User_Phone']) ?></td><td><?= e($u['User_Region']) ?></td><td><?= e($u['User_Registration_Date']) ?></td><td><?= e($u['User_Type']) ?></td>
            <td class="actions">
                <a class="button small" href="users.php?edit=<?= e($u['User_ID']) ?>">Edit</a>
                <form method="post" onsubmit="return confirm('Delete this user?');">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="User_ID" value="<?= e($u['User_ID']) ?>">
                    <button class="danger small" type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php include 'footer.php'; ?>
