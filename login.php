<?php
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if (isset($_POST['logoff']) && $_POST['logoff'] === 'true') {
    $_SESSION = array();
    session_destroy();
  }

  $mysqli = new mysqli("oniddb.cws.oregonstate.edu", 'mcwhorta-db', 'OOunihvLeeTmSdmf', 'mcwhorta-db');
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MYSQL <br>";
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST' and count($_POST) > 0) {
    if (isset($_POST['registerUser']) && isset($_POST['username'])
      && isset($_POST['password']) && isset($_POST['passwordRepeated'])
      && isset($_POST['birthday'])) {

      if ($_POST['username'] !== '' && $_POST['password'] !== ''
        && $_POST['passwordRepeated'] !== '' && $_POST['birthday'] !== '') {
        registerUser($_POST['username'], $_POST['password'], $_POST['passwordRepeated'], $_POST['birthday']);
        die();
      }
      else {
        echo 'emptyParams';
        die();
      }
    }
    
    if (isset($_POST['validateSignOn']) && isset($_POST['username'])
      && isset($_POST['password'])) {

      if ($_POST['username'] !== '' && $_POST['password'] !== '') {
        if (validateSignOn($_POST['username'], $_POST['password']) 
          && session_status() == PHP_SESSION_ACTIVE) {

          $_SESSION['username'] = $_POST['username'];
        }
        die();
      }
      else {
        echo 'emptyParams';
        die();
      }
    die();
    }

    if (isset($_POST['checkIfSignedIn']) && session_status() == PHP_SESSION_ACTIVE) {
      $returnArr = array('status' => "", 'username' => "");
      if (!isset($_SESSION['username'])) {
        $returnArr['status'] = "notLoggedIn";
      }
      else {
        $returnArr['status'] = "loggedIn";
        $returnArr['username'] = $_SESSION['username'];
      }
      $jsonReturn = json_encode($returnArr);
      echo $jsonReturn;
      die();
    }
  }

  function validateSignOn($username, $password) {
    global $mysqli;

    if (!($stmt = $mysqli->prepare("SELECT id from users where username=? and password=?;"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      return false;
    }
    if (!$stmt->bind_param("ss", $username, $password)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
      return false;
    }
    if (!$stmt->execute()) {
      echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
      return false;
    }
    if (!($res = $stmt->get_result())) {
      echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
      return false;
    }

    if ($res->num_rows === 0) {
      echo "authenFailed";
      return false;
    }
    echo "loginSuccessful";
    return true;
  }

  function registerUser($username, $password, $passwordRepeated, $birthday) {
    global $mysqli;

    if ($password !== $passwordRepeated) {
      echo "passwordsNotMatching";
      die();
    } 
	if ($birthday < '1994') {
		echo "illegalUnderage";
		die();
	}
    if (usernameExists($username)) {
      echo "usernameDuplicate";
      die();
    }

    if (!($stmt = $mysqli->prepare("INSERT INTO users(username, password, birthday)
      VALUES (?, ?, ?);"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      die();
    }

    if (!$stmt->bind_param("sss", $username, $password, $birthday)) {
      echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
      die();
    }

    if (!$stmt->execute()) {
      echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
      die();
    }
    echo "registrationSuccessful";
  }

  function usernameExists($username) {
    global $mysqli;
    if (!($stmt = $mysqli->prepare("SELECT id from users where username=?;"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      die();
    }
    if (!$stmt->bind_param("s", $username)) {
          echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
      echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!($res = $stmt->get_result())) {
      echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if ($res->num_rows === 0) {
      return false;
    }
    
    return true;
  }
?>