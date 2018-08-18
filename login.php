<?php
include "inc/init.php"; // initialize

if($user->islg()) {
  header("Location: $set->url");
  exit;
}

$page->title = "Login - ". $set->site_name;

if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token'])) {

  if(isset($_GET['forget'])) {
    $email = $_POST['email'];
    if(!$options->isValidMail($email))
    $page->error = "E-mail address is not valid.";

    if(!isset($page->error) && !($usr = $db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `email` = ?s", $email)))
    $page->error = "This e-mail address doesn't exist in our database !";

    if(!isset($page->error)) {
      $key = sha1(rand());

      $db->query("UPDATE `".APP_PREFIX."users` SET `key` = ?s WHERE `userid` = ?i", $key, $usr->userid);

      $link = $set->url."/login.php?key=".$key."&userid=".$usr->userid;

      $from ="From: noreply@".$set->url;
      $sub = "New Password !";
      $msg = "Hello,<br> You requested for a new password. To confirm <a href='$link'>click here</a>.<br>If you can't access copy this to your browser<br/>$link  <br><br>Regards<br><small>Note: Dont reply to this email. If you got this email by mistake then ignore this email.</small>";
      if($options->sendMail($email, $sub, $msg, $from))
      $page->success = "An e-mail with instructions was sent !";
    }

  } else if(isset($_GET['key'])) {
    if($_GET['key'] == '0') {
      header("Location: $set->url");
      exit;
    }
    if($usr = $db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `key` = ?s", $_GET['key'])) {
      if($db->query("UPDATE `".APP_PREFIX."users` SET `password` = ?s WHERE `userid` = ?i", sha1($_POST['password']), $usr->userid)) {
        $db->query("UPDATE `".APP_PREFIX."users` SET `key` = '0' WHERE `userid` = ?i", $usr->userid);
        $page->success = "Password successfully changed!";
      }
    }

  } else {
    $name = $_POST['name'];
    $password = $_POST['password'];

    if(!($usr = $db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `username` = ?s AND `password` = ?s", $name, sha1($password))))
    $page->error = "Username or password are incorrect!";
    else {
      if($_POST['r'] == 1){
        $path_info = parse_url($set->url);
        setcookie("user", $name, time() + 3600 * 24 * 30, $path_info['path']); // set
        setcookie("pass", sha1($password), time() + 3600 * 24 * 30, $path_info['path']); // set
      }
      $_SESSION['user'] = $usr->userid;
      header("Location: $set->url");
      exit;
    }
  }
} else if($_POST)
$page->error = "Invalid Request !";

include 'header.php'; // header area

$_SESSION['token'] = sha1(rand()); // random token

echo "<div class='container'>
<div class='row'>
<div class='span3 hidden-phone'></div>
<div class='span6' id='form-login'>";

if(isset($page->error))
$options->error($page->error);
else if(isset($page->success))
$options->success($page->success);

if(isset($_GET['forget'])) {

  echo "<form class='form-horizontal well' action='#' method='post'>
  <fieldset>
  <center>
  <img src='img/logo.jpg'>
  <h1>Forgot Password?</h1>
  <p>Enter you e-mail address to recover your password</p><br /><br />
  </center>
  <div class='control-group'>
  <div class='control-label'>
  <label>E-mail Address</label>
  </div>
  <div class='controls'>
  <input type='text' placeholder='user@localhost' name='email' class='input-large'>
  </div>
  </div>

  <input type='hidden' name='token' value='".$_SESSION['token']."'>

  <div class='control-group'>
  <div class='controls'>
  <button type='submit' id='submit' class='btn btn-primary'>Reest Password</button>
  </div>
  </div>
  </fieldset>";

} else if(isset($_GET['key']) && !isset($page->success)) {
  if($_GET['key'] == '0') {
    echo "<div class=\"alert alert-error\">Error !</div>";
    exit;
  }
  if($usr = $db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `key` = ?s AND `userid` = ?i", $_GET['key'], $_GET['userid'])) {
    echo "<form class='form-horizontal well' action='#' method='post'>
    <fieldset>
    <center>
    <img src='img/logo.jpg'>
    <h1>Reset Password</h1><br /><br />
    </center>
    <div class='control-group'>
    <div class='control-label'>
    <label>New password</label>
    </div>
    <div class='controls'>
    <input type='password' name='password' class='input-large'>
    </div>
    </div>

    <input type='hidden' name='token' value='".$_SESSION['token']."'>

    <div class='control-group'>
    <div class='controls'>
    <button type='submit' id='submit' class='btn btn-primary'>Save</button>
    </div>
    </div>
    </fieldset>";

  } else {
    echo "<div class=\"alert alert-error\">Error bad key !</div>";
  }

}else {
  echo "<form class='form-horizontal well' action='?' method='post'>
  <fieldset>
  <center>
  <img src='img/logo.jpg'>
  <h1>Login</h1>
  <p>Login area for Administrators & Students</p><br /><br />
  </center>
  <div class='control-group'>
  <div class='control-label'>
  <label>Username</label>
  </div>
  <div class='controls'>
  <input type='text' placeholder='admin' name='name' class='input-large'>
  </div>
  </div>

  <div class='control-group'>
  <div class='control-label'>
  <label>Password</label>
  </div>
  <div class='controls'>
  <input type='password' placeholder='type your password' name='password' class='input-large'>
  <!-- <span class='help-block'>Example block-level help text here.</span> -->
  </div>

  </div>
  <div class='control-group'>
  <div class='control-label'>
  <label for='r'>Remember Me</label>
  </div>
  <div class='controls'>
  <input type='checkbox' name='r' value='1' id='r'>
  </div>
  </div>

  <input type='hidden' name='token' value='".$_SESSION['token']."'>

  <div class='control-group'>
  <div class='controls'>
  <button type='submit' id='submit' class='btn btn-success'>Login Me</button>
  <a href='?forget=1' class='btn btn-secondary'>Forgot Password</a>
  </div>
  </div>
  </fieldset>";
}
echo "  </form>
</div>
</div>";

include "footer.php"; // footer area
