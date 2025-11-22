<?php
require_once 'config/database.php';
$db = Database::getInstance();
echo "DB OK! Products: " . count($db->query("SELECT * FROM products"));
?>