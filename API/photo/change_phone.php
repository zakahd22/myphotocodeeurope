<?php
require_once '../../common/connect.php';

guardAgainstWrongHttpMethod();
$body = getBody();
guardAgainstMissingParameters($body);
$result = updatePhone($body["phone"], $body["previous_phone"], $body["code"]);
createResponse($result);

function createResponse($result) {
  if ($result === 0) {
      exitWithError("could not change the phone");
  }
  http_response_code(200);
  echo json_encode(
    array("status"=>"ok")
  );
}

function updatePhone($phone, $previousPhone, $code) {
  if($phone === $previousPhone){
    return true;
  }
  if(!preg_match("/^\+([0-9]+)$/", $phone)){
    exitWithError("Not a phone number: $phone");
  }
  $db = createConnection();
  $phoneAlreadySet = findByContactAndCode($phone, $code, $db);
  if($phoneAlreadySet == 1) {
    return true;
  }
  return updateGestorContact($phone, $previousPhone, $code, $db);
}

function createConnection() {
  $connection = new connect("myphotocode_web");
  if (mysqli_connect_errno()) {
    exitWithFatalError();
  }
  return $connection->connection();
}

function updateGestorContact($phone, $previousPhone, $code, mysqli $db){
    //print "UPDATE gestor SET contact = '$phone' WHERE code = '$code' AND contact='$previousPhone'";
  $query = "UPDATE gestor SET contact = '$phone' WHERE code = '$code' AND contact='$previousPhone'";
  
  $result = $db->query($query);
  return ($result === true && mysqli_affected_rows($db) == 1);
}

function findByContactAndCode($phone, $code, mysqli $db) {
  $query = "SELECT code FROM gestor WHERE code='$code' AND contact='$phone'";
  $result = $db->query($query);
  return mysqli_num_rows($result);
}

function guardAgainstWrongHttpMethod() {
  if ($_SERVER['REQUEST_METHOD'] != PUT) {
    exitWithError("invalid method");
  }
}

function getBody() {
  $bodyJson = file_get_contents('php://input');
  if (!isset($bodyJson)) {
    exitWithError("missing body");
  }
  $body = json_decode($bodyJson, true);
  if (!isset($body)) {
    echo $bodyJson;
    exitWithError("wrong json format");
  }
  return $body;
}

function guardAgainstMissingParameters($body) {
  if (!array_key_exists("code", $body) || empty($body["code"])) {
    exitWithError("missing code");
  }
  if (!array_key_exists("phone", $body) || empty($body["phone"])) {
    exitWithError("missing phone");
  }
  if (!array_key_exists("previous_phone", $body) || empty($body["previous_phone"])) {
    exitWithError("missing previous phone");
  }
}

function exitWithError($message){
  http_response_code(400);
  echo json_encode(
    array("status"=>"ko","error" => $message)
  );
  exit(0);
}

function exitWithFatalError(){
  http_response_code(500);
  exit(0);
}


