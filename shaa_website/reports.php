<?php
require 'config.php';
$pageTitle = 'Reports - SHAA';
include 'header.php';

function rows($pdo, $sql) { return $pdo->query($sql)->fetchAll(); }

$registeredUsers = rows($pdo, "SELECT u.User_ID, u.Username, u.User_Type,
       COUNT(DISTINCT i.Item_ID) AS Total_Items_Listed,
       COUNT(DISTINCT b.Bid_ID) AS Total_Bids_Placed,
       COUNT(DISTINCT CASE WHEN o.User_ID = u.User_ID AND o.Order_Status='Completed' THEN o.Order_ID END) AS Total_Successful_Purchases,
       COUNT(DISTINCT CASE WHEN sellerItem.User_ID = u.User_ID AND sellerOrder.Order_Status='Completed' THEN sellerOrder.Order_ID END) AS Total_Successful_Sales,
       'Active' AS Account_Status
FROM `user` u
LEFT JOIN item i ON u.User_ID = i.User_ID
LEFT JOIN bid b ON u.User_ID = b.User_ID
LEFT JOIN orders o ON u.User_ID = o.User_ID
LEFT JOIN item sellerItem ON u.User_ID = sellerItem.User_ID
LEFT JOIN orders sellerOrder ON sellerItem.Item_ID = sellerOrder.Item_ID
GROUP BY u.User_ID, u.Username, u.User_Type
ORDER BY u.User_Type, u.User_ID");

$auctionItems = rows($pdo, "SELECT i.Item_ID, i.Item_Name, c.Category_Description AS Category, u.Username AS Seller_Name,
       i.Auction_Item_Starting_Price AS Starting_Price,
       CONCAT(i.Auction_Item_Start_Date, ' to ', i.Auction_Item_End_Date) AS Auction_Period,
       i.Item_Status AS Auction_Status
FROM item i
LEFT JOIN category c ON i.Category_ID = c.Category_ID
LEFT JOIN `user` u ON i.User_ID = u.User_ID
ORDER BY i.Item_ID");

$biddingRecords = rows($pdo, "SELECT b.Bid_ID, u.Username AS Bidder_Name, i.Item_Name AS Auction_Item,
       b.Bid_Amount, b.Bid_Date_Time, IF(b.Winning_Bid='1','Yes','No') AS Winning_Bid_Status
FROM bid b
LEFT JOIN `user` u ON b.User_ID = u.User_ID
LEFT JOIN item i ON b.Item_ID = i.Item_ID
ORDER BY b.Bid_Date_Time");

$completedOrders = rows($pdo, "SELECT YEAR(o.Order_Date_Time) AS Year, QUARTER(o.Order_Date_Time) AS Quarter,
       buyer.Username AS Winning_Buyer, seller.Username AS Seller, i.Item_Name AS Auction_Item,
       o.Order_Total_Amount AS Final_Price, o.Order_Status, p.Payment_Status, o.Order_Date_Time AS Order_Date
FROM orders o
LEFT JOIN `user` buyer ON o.User_ID = buyer.User_ID
LEFT JOIN item i ON o.Item_ID = i.Item_ID
LEFT JOIN `user` seller ON i.User_ID = seller.User_ID
LEFT JOIN payment p ON o.Order_ID = p.Order_ID
WHERE o.Order_Status = 'Completed'
ORDER BY Year, Quarter, o.Order_Date_Time");

$topItems = rows($pdo, "SELECT i.Item_ID, i.Item_Name, c.Category_Description AS Category,
       COUNT(b.Bid_ID) AS Total_Bids_Received,
       MAX(CASE WHEN b.Winning_Bid='1' THEN u.Username END) AS Winning_Bidder
FROM item i
LEFT JOIN category c ON i.Category_ID = c.Category_ID
LEFT JOIN bid b ON i.Item_ID = b.Item_ID
LEFT JOIN `user` u ON b.User_ID = u.User_ID
GROUP BY i.Item_ID, i.Item_Name, c.Category_Description
ORDER BY Total_Bids_Received DESC, i.Item_ID
LIMIT 5");

function printTable($title, $data) {
    echo '<section class="panel"><h3>'.e($title).'</h3>';
    if (!$data) { echo '<p>No data found.</p></section>'; return; }
    echo '<table><tr>';
    foreach (array_keys($data[0]) as $col) echo '<th>'.e(str_replace('_',' ', $col)).'</th>';
    echo '</tr>';
    foreach ($data as $row) { echo '<tr>'; foreach ($row as $value) echo '<td>'.e($value).'</td>'; echo '</tr>'; }
    echo '</table></section>';
}
?>
<h2>Required Project Reports</h2>
<?php
printTable('1. Registered Users by Role', $registeredUsers);
printTable('2. All Auction Items', $auctionItems);
printTable('3. All Bidding Records', $biddingRecords);
printTable('4. Quarterly and Annual Completed Orders', $completedOrders);
printTable('5. Top 5 Most Popular Auction Items', $topItems);
include 'footer.php';
?>
