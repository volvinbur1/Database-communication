<?php
session_start();

$request = json_decode(file_get_contents('php://input'), true);

$email = $request["email"];
$password = $request["psw"];

require "DBCommunication.php";

$db = new DBCommunication();
$result = array("status" => "", "info" => "", "error" => "");
$back = $db->get_user_password($email);

//var_dump($back);

function create_info_array($id)
{
  global $db;
  global $result;
  $back = $db->get_user_info($id);
  if (is_array($back)) {
    $result["info"] = $back;
  }
  else {
    $result["info"] = "ERROR";
    $result["error"] = "$back";
  }
}

if (is_array($back)) {
  if ($back["password"] === $password) {
    $result["status"] = "OK";
    create_info_array($back["userId"]);
  }
  else {
    $result["status"] = "BAD";
    $result["error"] = "Passwords don`t match.";
  }
}
else {
  $result["status"] = "BAD";
  $result["error"] = $back;
}

echo json_encode($result);

unset($db);
?>
