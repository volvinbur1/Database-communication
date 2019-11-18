<?php

require "DBCommunication.php";
$db = new DBCommunication();
$back = $db->get_data_from_table("LogPas");
//var_dump($back);
echo json_encode($back);
unset($back);
?>