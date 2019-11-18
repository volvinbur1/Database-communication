<?php
$request = json_decode(file_get_contents('php://input'), true);

$email = $request["email"];
$name = $request["name"];
$surname= $request["surname"];
$username = $request["username"];
$password = $request["password"];

require "DBCommunication.php";

$db = new DBCommunication();
$result = array("status" => "", "error" => "");
$back = $db->add_user($email, $username, $password, $name, $surname);
if ($back === true) {
    $result["status"] = "OK";
    $result["error"] = "OK";
    echo json_encode($result);
}
else {
    $result["status"] = "BAD";
    $result["error"] = $back;
    echo json_encode($result);
}
unset($db);
?>
