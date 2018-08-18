<?php

include '../inc/init.php';

if(!$user->isAdmin()) {
  header("Location: $set->url");  // admin file location
  exit;
}

$page->title = "Roles Management";  // browser title
$presets->setActive("adminpanel");  // set active menu

$groups_type = array("Guests", "Student", "Teacher", "Administrator");  // role types
$ignored_columns = array("groupid", "name", "type", "priority", "color"); // role types options

$data = $db->getAll("SELECT * FROM `".APP_PREFIX."groups` ORDER BY `type`,`priority`"); // admin database call
$columns = get_object_vars($data[0]); // columns name
$act = isset($_GET['act']) ? $_GET['act'] : NULL;

if($_POST) {
  if( ($act == "add") || ($act == 'edit') ) { // add or edit database query

    if($act == 'edit')
    $sql = "UPDATE `".APP_PREFIX."groups` SET ";
    else
    $sql = "INSERT INTO `".APP_PREFIX."groups` SET ";

    $editable = 0; // 0 = false | 1 = true
    if ($act == 'add') {
      $editable = 1;
    }

    if(($act == 'edit') && ($group = $db->getRow("SELECT * FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $_GET['id'])))
    if($group->groupid > 4)
    $editable = 1;

    $name = $_POST['name'];

    if(isset($_POST['type']))
    $type = $_POST['type'];

    $priority = $_POST['priority'];
    $color = $_POST['color'];

    $sql .= $db->parse(" `name` = ?s, `priority` = ?s, `color` = ?s, ", $name, $priority, $color);

    if($editable)
    $sql .= $db->parse(" `type` = ?s,", $type);

    foreach ($_POST as $key => $value)
    if(!in_array($key, $ignored_columns) && in_array($key, array_keys($columns))) // make sure it's a valid key
    $sql .= $db->parse(" ?n = ?s,", $key, $value);

    if($act == 'edit')
    $sql = trim($sql, ",").$db->parse(" WHERE `groupid` = ?i", $group->groupid);
    else
    $sql = trim($sql, ",");

    if($db->query("?p", $sql)) // query already parsed
    if($act == 'edit')
    $page->success = "Role settings successfully saved !";
    else
    $page->success = "Role successfully created !";
    else
    $page->error = "Some error camed up !";

  }
  else if($act == 'del') {

    if($group = $db->getRow("SELECT * FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $_GET['id'])) {

      $valid_groups = array();

      foreach ($data as $d)
      if($d->groupid != $group->groupid)
      $valid_groups[] = $d->groupid;

      if(in_array($_POST['replace'], $valid_groups)) {
        $db->query("DELETE FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $group->groupid);
        if($db->query("UPDATE `".APP_PREFIX."groups` SET `groupid` = ?i WHERE `groupid` = ?i", $_POST['replace'], $group->groupid))
        $page->success = "Role was successfully deleted !";
      }
    }
    else
    $page->error = "This roles does not exists!";
  }
}
include 'header.php';
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span3">
      <div class="well sidebar-nav sidebar-nav-fixed">
        <ul class="nav nav-list">
          <li class="nav-header">ADMIN OPTIONS</li>
          <li><a href='index.php'>General Settings</a></li>
          <li class='active'><a href='roles.php'>Roles Management</a></li>
        </ul>
      </div>
    </div>
    <div class="span9">

      <?php

      if(isset($page->error))
      $options->error($page->error);
      else if(isset($page->success))
      $options->success($page->success);

      if(($act == "add") || ($act == 'edit')) { // add and edit shows the same form so we only need to set the default values in case it is edit

        $edit = 0; // based on this we determine if it's edit or add
        $editable = 0; // based on this we determine if it's a default group or not

        if(($act == 'edit') && ($group = $db->getRow("SELECT * FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $_GET['id']))) {
          $edit = 1;
          if($group->groupid > 4)
          $editable = 1;
        }

        $show_types = ''; // holds the html for the group types
        $show_can_options = ''; // holds the extra options that are not added by default

        foreach ($groups_type as $k => $v)
        if($k != 0)
        $show_types .= "<option value='$k' ".($edit && ($group->type == $k) ? "selected='1'" : "").">".$options->html($v)."</option>";

        foreach ($columns as $k => $v) {
          $safe_name = $options->html($k);

          if(!in_array($k, $ignored_columns))
          if((strpos($k, "can") !== FALSE)) {
            $show_can_options .= "
            <div class='control-group'>
            <label class='control-label' for='$safe_name'>".$options->prettyPrint(str_ireplace("can", "can ", $safe_name))."</label>
            <div class='controls'>
            <select id='$safe_name' name='$safe_name' class='input-xlarge'>
            <option value='0' ".($edit && ($group->$k == 0) ? "selected='1'" : "").">No</option>
            <option value='1' ".($edit && ($group->$k == 1) ? "selected='1'" : "").">Yes</option>
            </select>
            </div>
            </div>";

          } else {

            $show_can_options .= "
            <div class='control-group'>
            <label class='control-label' for='$safe_name'>".$options->prettyPrint($safe_name)."</label>
            <div class='controls'>
            <input type='text' id='$safe_name' name='$safe_name' ".($edit ? "value='".$options->html($group->$k)."'" : "")." class='input-xlarge'>
            </div>
            </div>
            ";
          }
        }

        echo "
        <form class='form-horizontal' action='#' method='post'>
        <fieldset>

        <legend>".($edit ? "Edit" : "Add")." Role</legend>

        <div class='control-group'>
        <label class='control-label' for='name'>Name</label>
        <div class='controls'>
        <input id='name' name='name' type='text' ".($edit ? "value='".$options->html($group->name)."'" : "")." class='input-xlarge'>
        </div>
        </div>";


        if($editable || !$edit)
        echo "
        <div class='control-group'>
        <label class='control-label' for='type'>Type</label>
        <div class='controls'>
        <select id='type' name='type' class='input-xlarge'>
        $show_types
        </select>
        </div>
        </div>";

        echo "
        <div class='control-group'>
        <label class='control-label' for='priority'>Priority</label>
        <div class='controls'>
        <input id='priority' name='priority' type='text' class='input-small' ".($edit ? "value='".$options->html($group->priority)."'" : "value='1'").">
        <p class='help-block'>the bigger the number the higher the priority it has compared with same type</p>
        </div>
        </div>

        <div class='control-group'>
        <label class='control-label' for='color'>Color</label>
        <div class='controls'>
        <input id='color' name='color' type='text' ".($edit ? "value='".$options->html($group->color)."'" : "")." class='input-small'>
        <p class='help-block'>eg: <b>#00ff00</b> or <b>lime</b></p>
        </div>
        </div>


        $show_can_options

        <div class='control-group'>
        <div class='controls'>
        <input type='submit' value='Save Role' class='btn btn-success'>  <a href='?' class='btn'>Cancel</a>
        </div>
        </div>

        </fieldset>
        </form>";




      } else if($act == 'del') {

        if($group = $db->getRow("SELECT * FROM `".APP_PREFIX."groups` WHERE `groupid` = ?i", $_GET['id'])) {


          $show_groups = '';

          foreach ($data as $d)
          if($d->groupid != $group->groupid)
          $show_groups .= "<option value='$d->groupid'>".$options->html($d->name)."</option>";


          echo "
          <form class='form-horizontal' action='#' method='post'>
          <fieldset>

          <legend>Delete Role</legend>

          ".$options->info("You are about to delete the Role `".$options->html($group->name)."`",1)."

          <div class='control-group'>
          <label class='control-label' for='replace'>Replace role with: </label>
          <div class='controls'>
          <select name='replace' class='input-xlarge'>
          $show_groups
          </select>
          <p class='help-block'>all the users that currently belong to the deleted role will be moved to this one</p>
          </div>
          </div>

          <div class='control-group'>
          <div class='controls'>
          <input type='submit' value='Yes delete' class='btn btn-success'> <a href='?' class='btn'>Cancel</a>
          </div>
          </div>

          </form>
          ";
        }else
        $options->error("This Role doesn't exists !");

      } else {

        echo "<legend><h2>Role Management</h2></legend>
        <hr/>";
        echo "<table class='table table-striped'>
        <tr class='text-primary' style='text-transform:uppercase;'>
        <strong>
        <th>Role Name</th>
        <th>Role Type</th>
        <th>Actions</th>
        </strong>
        </tr>";
        foreach ($data as $d) {

          if($d->groupid > 0) // we only show delete option for roles
          $delbtn = "<a href='?act=del&id=$d->groupid' class='btn btn-danger'>Delete</a>";
          else
          $delbtn = '';

          echo "
          <tr>
          <td>".$options->html($d->name)."</td>
          <td>".$groups_type[$d->type]."</td>
          <td><a href='?act=edit&id=$d->groupid' class='btn btn-primary'>Edit</a> $delbtn</td>
          </tr>";
        }
        echo "</table>
        <a href='?act=add' class='btn btn-danger'>+ Add New Role</a>
        ";
      }
      ?>

    </div>
  </div>
</div><!--/.fluid-container-->

<?php
include 'footer.php';
?>
