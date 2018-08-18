<?php

include '../inc/init.php';

if(!$user->isAdmin()) {
  header("Location: $set->url");
  exit;
}

$page->title = "Admin Panel";
$presets->setActive("adminpanel");

if($_POST) {

  $data = $db->getRow("SELECT * FROM `".APP_PREFIX."settings` LIMIT 1");
  $columns = get_object_vars($data);

  $sql = "UPDATE `".APP_PREFIX."settings` SET ";

  foreach ($columns as $k => $v)
  if($k != 'userid')
  $sql .= $db->parse(" ?n = ?s,", $k, $_POST[$k]);
  $sql = trim($sql, ",")." LIMIT 1";

  if($db->query(" ?p ", $sql))
  $page->success = "Settings Saved!";
  else
  $page->error = "Some error camed up!";
}

// we grab the settings and we merge them into $set
$set = (object)array_merge((array)$set,(array)$db->getRow("SELECT * FROM `".APP_PREFIX."settings` LIMIT 1"));

include 'header.php';
?>
<div class='container-fluid'>
  <div class='row-fluid'>
    <div class='span3'>
      <div class='well sidebar-nav sidebar-nav-fixed'>
        <ul class='nav nav-list'>
          <li class='nav-header'>ADMIN OPTIONS</li>
          <li class='active'><a href='?'>General Settings</a></li>
          <li><a href='roles.php'>Role Management</a></li>
        </ul>
      </div>
    </div>

    <div class='span8'>

      <?php
      // we make sure we get the leatest data
      $data = $db->getRow("SELECT * FROM `".APP_PREFIX."settings` LIMIT 1");
      $columns = get_object_vars($data);

      if(isset($page->error))
      $options->error($page->error);
      else if(isset($page->success))
      $options->success($page->success);

      echo "
      <form class='form-horizontal' action='#' method='post'>
      <fieldset>
      <legend><h2>General Settings</h2></legend>";

      foreach ($columns as $key => $value) {
        $safe_name = $options->html($key);
        $safe_val = $options->html($value);

        if(in_array($key, array("register", "email_validation", "captcha"))) // add in this array columns that you want to have enabled/disabled select menu
        echo "
        <div class='control-group'>
        <label class='control-label' for='$safe_name'>".$options->prettyPrint($safe_name)."</label>
        <div class='controls'>
        <select id='$safe_name' name='$safe_name' class='input-xlarge'>
        <option value='1' ".($value == 1 ? "selected='1'" : "").">Enabled</option>
        <option value='0' ".($value == 0 ? "selected='1'" : "").">Disabled</option>
        </select>
        </div>
        </div>";
        else if(strpos($value, "\n") !== FALSE)
        echo "
        <div class='control-group'>
        <label class='control-label' for='$safe_name'>".$options->prettyPrint($safe_name)."</label>
        <div class='controls'>
        <textarea id='$safe_name' name='$safe_name' class='input-xlarge'>$safe_val</textarea>
        </div>
        </div>";
        else
        echo "
        <div class='control-group'>
        <label class='control-label' for='$safe_name'>".$options->prettyPrint($safe_name)."</label>
        <div class='controls'>
        <input id='$safe_name' name='$safe_name' type='text' value='$safe_val' class='input-xlarge'>
        </div>
        </div>";
      }

      echo "<div class='control-group'>
      <div class='controls'>
      <button class='btn btn-primary'>Save</button>
      </div>
      </div>

      </fieldset>
      </form>";
      ?>

    </div>
  </div>
</div>

<?php
include 'footer.php';
?>
