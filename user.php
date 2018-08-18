<?php

include "inc/init.php";

if(!$user->islg()){
	header("Location: ".$set->url);
	exit;
}

if(isset($_GET['id']) && $user->group->canedit && $user->exists($_GET['id'])) {
	$uid = (int)$_GET['id'];
	$can_edit = 1;
}else{
	$uid = $user->data->userid;
	$can_edit = 0;
}
$u = $db->getRow("SELECT * FROM `".APP_PREFIX."users` WHERE `userid` = ?i", $uid);

$page->title = "My Profile - ". $options->html($u->username);

if($_POST) {
	if(isset($_GET['password']) && ($user->data->userid == $u->userid)) {
		$opass = $_POST['oldpass'];
		$npass = $_POST['newpass'];
		$npass2 = $_POST['newpass2'];

		if($db->getRow("SELECT `userid` FROM `".APP_PREFIX."users` WHERE `userid` = ?i AND `password` = ?s", $u->userid, sha1($opass))) {

			if(!isset($npass[3]) || isset($npass[30]))
			$page->error = "Password too short or too long!";
			else if($npass != $npass2)
			$page->error = "New passwords don't match!";
			else
			if($db->query("UPDATE `".APP_PREFIX."users` SET `password` = ?s WHERE `userid` = ?i", sha1($npass), $u->userid))
			$page->success = "Password updated successfully!";

		} else
		$page->error = 'Invalid Password !';

	} else {
		$email = $_POST['email'];
		$last_name = $_POST['last_name'];
		$first_name = $_POST['first_name'];
		$middle_name = $_POST['middle_name'];
		$mobile = $_POST['mobile'];
		$age = $_POST['age'];

		$extra = '';
		if(!$can_edit) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			if(isset($_POST['groupid']))
			$groupid = $_POST['groupid'];

			$extra = $db->parse(", `username` = ?s", $username);

			if($user->isAdmin())
			$extra .= $db->parse(", `groupid` = ?i", $groupid);

			if(!empty($password))
			$extra .= $db->parse(", `password` = ?s", sha1($password));

			if(!isset($username[3]) || isset($username[100]))
			$page->error = "Username too short or too long!";

			if($user->isAdmin() && !$db->getRow("SELECT `groupid` FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $groupid))
			$page->error = "The role is Invalid!";
		}

		if(!$options->isValidMail($email))
		$page->error = "E-mail address is not valid.";

		if(!isset($last_name[3]) || isset($last_name[100]))
		$page->error = "Lastname too short or too long!";

		if(!isset($page->error) && $db->query("UPDATE `".APP_PREFIX."users` SET `email` = ?s, `last_name` = ?s, `first_name` = ?s, `middle_name` = ?s ?p WHERE `userid` = ?i", $email, $last_name, $first_name, $middle_name, $u->userid)) {
			$page->success = "Account details successfully saved!";

			// we make sure we show updated data
			$u = $db->getRow("SELECT * FROM `".APP_PREFIX."users` WHERE `userid` = ?i", $u->userid);
		}
	}
}

include 'header.php';

echo "
<div class=\"container\"><div class='span6'>";

if(isset($page->error))
$options->error($page->error);
else if(isset($page->success))
$options->success($page->success);

if(!isset($_GET['password']) && ($user->data->userid == $u->userid)) {
	// change password needs the old account password
	echo "<form class='form-horizontal well' action='#' method='post'>
	<fieldset>
	<legend><h2>Change Password?</h2></legend>

	<div class='control-group'>
	<div class='control-label'>
	<label>Old Password</label>
	</div>
	<div class='controls'>
	<input type='password' name='oldpass' class='input-large'>
	</div>
	</div>
	<div class='control-group'>
	<div class='control-label'>
	<label>New Password</label>
	</div>
	<div class='controls'>
	<input type='password' name='newpass' class='input-large'>
	</div>
	</div>
	<div class='control-group'>
	<div class='control-label'>
	<label>New Password Again</label>
	</div>
	<div class='controls'>
	<input type='password' name='newpass2' class='input-large'>
	</div>
	</div>

	<div class='control-group'>
	<div class='controls'>
	<button type='submit' id='submit' class='btn btn-primary'>Change</button>
	</div>
	</div>
	</fieldset>
	</form>
	<a href='?'>Edit Profile Details</a>";

} else {
	echo "<form class='form-horizontal well' action='#' method='post'>
	<fieldset>
	<legend><h2>Edit Profile</h2></legend>";

	if($can_edit) {
		$groups = $db->getAll("SELECT * FROM `".APP_PREFIX."groups` ORDER BY `type`,`priority`");
		$show_groups = '';
		foreach($groups as $group)
		if($group->groupid != 1)
		if($group->groupid == $u->groupid)
		$show_groups .= "<option value='$group->groupid' selected='1'>".$group->name."</option>";
		else
		$show_groups .= "<option value='$group->groupid'>".$group->name."</option>";

		echo "
		<div class='control-group'>
		<legend style='text-transform:uppercase'><h5><b><i class='icon icon-lock'></i> Login Details</b></h5></legend>
		<div class='control-label'>
		<label>Username</label>
		</div>
		<div class='controls'>
		<input type='text' name='username' class='input-large' value='".$options->html($u->username)."' disabled='disabled'>
		</div>
		</div>

		<div class='control-group'>
		<div class='control-label'>
		<label>Password</label>
		</div>
		<div class='controls'>
		<input type='text' name='password' class='input-large'><br/>
		<small>Leave blank if you don't want to change</small>
		</div>
		</div>

		<div class='control-group'>
		<label class='control-label' for='selectbasic' reui>Role</label>
		<div class='controls'>
		<select id='selectbasic' name='groupid' class='input-xlarge' ".($user->isAdmin() ? "" : "disabled='disabled'").">
		$show_groups
		</select>
		</div>
		</div>
		";
	}

	echo "
	<div class='control-group'>
	<legend style='text-transform:uppercase'><h5><b><i class='icon icon-user'></i> Account Details</b></h5></legend>
	<div class='control-label'>
	<label>Lastname</label>
	</div>
	<div class='controls'>
	<input type='text' name='last_name' class='input-large' value='".$options->html($u->last_name)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='control-label'>
	<label>Firstname</label>
	</div>
	<div class='controls'>
	<input type='text' name='first_name' class='input-large' value='".$options->html($u->first_name)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='control-label'>
	<label>Middle Name</label>
	</div>
	<div class='controls'>
	<input type='text' name='middle_name' class='input-large' value='".$options->html($u->middle_name)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='control-label'>
	<label>Mobile No.</label>
	</div>
	<div class='controls'>
	<input type='text' name='mobile' class='input-large' value='".$options->html($u->mobile)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='control-label'>
	<label>Age</label>
	</div>
	<div class='controls'>
	<input type='text' name='age' class='input-large' value='".$options->html($u->age)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='control-label'>
	<label>E-mail Address</label>
	</div>
	<div class='controls'>
	<input type='text' name='email' class='input-large' value='".$options->html($u->email)."'>
	</div>
	</div>

	<div class='control-group'>
	<div class='controls'>
	<button type='submit' id='submit' class='btn btn-primary'>Save Update</button>
	</div>
	</div>
	</fieldset>
	</form>";

if(!$can_edit)
	echo "<a href='?password=1'>Change Password</a>";
}
echo "
</div>
</div>";

include 'footer.php';
