<?php
require_once '../../common/connect.php';

guardAgainstWrongHttpMethod();
$body = getBody();
guardAgainstMissingParameters($body);
$result = updateEmail($body["email"], $body["previous_email"], $body["code"]);
createResponse($result);

function createResponse($result) {
  if ($result === 0) {
      exitWithError("could not change the email");
  }
  http_response_code(200);
  echo json_encode(
    array("status"=>"ok")
  );
}

function updateEmail($email, $previousEmail, $code) {
  if($email === $previousEmail){
    return true;
  }
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    exitWithError("Not an email");
  }
  $db = createConnection();
  $emailAlreadySet = findByContactAndCode($email, $code, $db);
  if($emailAlreadySet == 1) {
    return true;
  }
  return updateGestorContact($email, $previousEmail, $code, $db);
}

function createConnection() {
  $connection = new connect("myphotocode_web");
  if (mysqli_connect_errno()) {
    exitWithFatalError();
  }
  return $connection->connection();
}

function updateGestorContact($email, $previousEmail, $code, mysqli $db){
  $query = "UPDATE gestor SET contact = '$email' WHERE code = '$code' AND contact='$previousEmail'";
  $result = $db->query($query);
  return ($result === true && mysqli_affected_rows($db) == 1);
}

function findByContactAndCode($email, $code, mysqli $db) {
  $query = "SELECT code FROM gestor WHERE code='$code' AND contact='$email'";
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
  if (!array_key_exists("email", $body) || empty($body["email"])) {
    exitWithError("missing email");
  }
  if (!array_key_exists("previous_email", $body) || empty($body["previous_email"])) {
    exitWithError("missing previous email");
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


