<?php
$query = $_REQUEST['request'];

require "DBCommunication.php";

$db = new DBCommunication();
echo $db->send_sql_request($query);
unset($db);
?>