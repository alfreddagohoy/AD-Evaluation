<?php

include "inc/init.php";

if(!$user->isAdmin()) {
  header("Location: $set->url");
  exit;
}

$page->title = "Register - ". $set->site_name;

if(isset($_GET['id']) && $user->group->canedit && $user->exists($_GET['id'])) {
  $uid = (int)$_GET['id'];
  $can_edit = 1;
}else{
  $uid = $user->data->userid;
  $can_edit = 0;
}
$u = $db->getRow("SELECT * FROM `".APP_PREFIX."users` WHERE `userid` = ?i", $uid);

if($_POST && isset($_SESSION['token']) && ($_SESSION['token'] == $_POST['token']) && $set->register) {

  // we validate the data
  $name = $_POST['name'];
  $last_name = $_POST['last_name'];
  $first_name = $_POST['first_name'];
  $middle_name = $_POST['middle_name'];
  $mobile = $_POST['mobile'];
  $age = $_POST['age'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // status messages
  if(!isset($name[3]) || isset($name[30]))
  $page->error = "Username too short or too long !";

  if(!$options->validUsername($name))
  $page->error = "Invalid Username!";

  if(!isset($last_name[3]) || isset($last_name[50]))
  $page->error = "Lastname too short or too long!";

  if(!isset($first_name[3]) || isset($first_name[50]))
  $page->error = "Firstname too short or too long!";

  if(!isset($middle_name[3]) || isset($middle_name[50]))
  $page->error = "Firstname too short or too long!";

  if(!isset($password[3]) || isset($password[30]))
  $page->error = "Password too short or too long!";

  if(!$options->isValidMail($email))
  $page->error = "E-mail address is not Valid.";

  if($db->getRow("SELECT `*` FROM `".APP_PREFIX."users` WHERE `username` = ?s", $name))
  $page->error = "Username already in use!";

  if($db->getRow("SELECT `*` FROM `".APP_PREFIX."users` WHERE `email` = ?s", $email))
  $page->error = "E-mail already in use!";


  if(!isset($page->error)){
    $user_data = array(
      "username" => $name,
      "last_name" => $last_name,
      "first_name" => $first_name,
      "middle_name" => $middle_name,
      "mobile" => $mobile,
      "password" => sha1($password),
      "email" => $email,
      "lastactive" => time(),
      "regtime" => time(),
      "validated" => 1
    );

    if($set->email_validation == 1) {

      $user_data["validated"] = $key = sha1(rand());

      $link = $set->url."/validate.php?key=".$key."&username=".urlencode($name);

      $url_info = parse_url($set->url);
      $from ="From: noreply@".$url_info['host'];
      $sub = "Activate your account !";
      $msg = "Hello ".$options->html($name).",<br> Thank you for choosing to be a member of out community.<br/><br/> To confirm your account <a href='$link'>click here</a>.<br>If you can't access copy this to your browser<br/>$link  <br><br>Regards<br><small>Note: Dont reply to this email. If you got this email by mistake then ignore this email.</small>";
      if(!$options->sendMail($email, $sub, $msg, $from))
      // if we can't send the mail by some reason we automatically activate the account
      $user_data["validated"] = 1;
    }

    if(($db->query("INSERT INTO `".APP_PREFIX."users` SET ?u", $user_data)) && ($id = $db->insertId()) && $db->query("INSERT INTO `".APP_PREFIX."privacy` SET `userid` = ?i", $id)) {
      $page->success = 1;
      $_SESSION['user'] = $id; // we automatically login the user
      $user = new User($db);
    } else
    $page->error = "There was an error ! Please try again !";

  }
}

include 'header.php';

if(!$set->register) // we check if the registration is enabled
$options->error("We are sorry registration is blocked momentarily please try again later!");

$_SESSION['token'] = sha1(rand()); // random token

$extra_content = ''; // holds success or error message

if(isset($page->error))
$extra_content = $options->error($page->error);

if(isset($page->success)) {

  echo "<div class='container'>
  <div class='span3 hidden-phone'></div>
  <div class='span6 well'>
  <h1>Congratulations!</h1>";
  $options->success("<p><strong>Your account was successfully registered !</strong></p>");
  echo " <a class='btn btn-primary' href='$set->url'>Login Now!</a>
  </div>
  </div>";
}
else {
  if(!$can_edit) {
    $groups = $db->getAll("SELECT * FROM `".APP_PREFIX."groups` ORDER BY `type`,`priority`");
    $show_groups = '';

    foreach($groups as $group)
    if($group->groupid != 0)
    if($group->groupid == $u->groupid)
    $show_groups .= "<option value='$group->groupid' selected='1'>".$group->name."</option>";
    else
    $show_groups .= "<option value='$group->groupid'>".$group->name."</option>";
  }
  echo "<div class='container'>
  <div class='span3 hidden-phone'></div>
  <div class='span6'>

  ".$extra_content."

  <form action='#' id='contact-form' class='form-horizontal well' method='post'>
  <fieldset>
  <center>
  <img src='img/logo.jpg'>
  <h1>Create Account</h1>
  <p>Create student & teacher account here</p><br /><br />
  </center>

  <div class='control-group'>
  <legend style='text-transform:uppercase'><h3><b><i class='icon icon-user' style='margin-top:10px;'></i> Account Details</b></h3></legend>
  <label class='control-label' for='name'>Username</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='name' id='name'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='last_name'>Lastname</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='last_name' id='last_name'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='first_name'>Firstname</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='first_name' id='first_name'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='middle_name'>Middlename</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='middle_name' id='middle_name'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='mobile'>Mobile No.</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='mobile' id='mobile'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='selectbasic' required >Gender</label>
  <div class='controls'>
  <select id='gender' name='gender' class='input-xlarge' ".($user->isAdmin() ? "" : "enabled='enabled'").">
  <option>select gender</option>
  <option value='male'>Male</option>
  <option value='female'>Female</option>
  </select>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='age'>Age</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='age' id='age'>
  </div>
  </div>

  <div class='control-group'>
  <legend style='text-transform:uppercase'><h3><b><i class='icon icon-pencil' style='margin-top:10px;'></i> School Details</b></h3></legend>
  <label class='control-label' for='year'>Year Level</label>
  <div class='controls'>
  <select type='select' class='input-xlarge' name='year' id='year'>
  <option value='0'>select year level</option>
  <option value='1'>1st Year</option>
  <option value='2'>2nd Year</option>
  <option value='3'>3rd Year</option>
  <option value='4'>4th Year</option>
  </select>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='teacher'>Section</label>
  <div class='controls'>
  <select type='select' class='input-xlarge' name='section' id='section'>
  <option value='0'>select section</option>
  <option value='1'>Gumamela</option>
  <option value='2'>Sun Flower</option>
  </select>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='teacher'>Teacher</label>
  <div class='controls'>
  <select type='select' class='input-xlarge' name='teacher' id='teacher'>
  <option value='0'>select teacher</option>
  <option value='1'>Mr. Dagohoy</option>
  <option value='2'>Mr. Cabahug</option>
  </select>
  </div>
  </div>

  <div class='control-group'>
  <legend style='text-transform:uppercase'><h3><b><i class='icon icon-lock' style='margin-top:10px;'></i> Login Details</b></h3></legend>
  <label class='control-label' for='email'>E-mail Address</label>
  <div class='controls'>
  <input type='text' class='input-xlarge' name='email' id='email'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='password'>Password</label>
  <div class='controls'>
  <input type='password' class='input-xlarge' name='password' id='password'>
  </div>
  </div>

  <div class='control-group'>
  <label class='control-label' for='selectbasic' required >Role</label>
  <div class='controls'>
  <select id='selectbasic' name='groupid' class='input-xlarge' ".($user->isAdmin() ? "" : "enabled='enabled'").">
  $show_groups
  </select>
  </div>
  </div>

  <input type='hidden' name='token' value='".$_SESSION['token']."'>

  <div class='form-actions'>
  <button type='submit' class='btn btn-danger'>Create</button>
  <button type='reset' class='btn'>Reset</button>
  </div>
  </fieldset>
  </form>
  </div>
  </div>";
}
?>
<?php
include "footer.php";
?>
