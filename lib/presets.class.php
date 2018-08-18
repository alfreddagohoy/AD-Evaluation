<?php

class presets {

  var $active = '';
  /*
  * admin top navbar
  */
  function GenerateNavbar() {
    global $set, $user;
    $var = array();
    if($user->group->type == 1) // we make it visible for students only
    $var[] = array("item" ,
    array("href" => $set->url,
    "name" => "Home",
    "class" => $this->isActive("home")),
    "id" => "home");

    if($user->group->type == 1) // we make it visible for students only
    $var[] = array("item",
    array("href" => $set->url."/evaluation.php",
    "name" => "Evaluation",
    "class" => $this->isActive("evaluation")),
    "id" => "evaluation");

    if($user->group->type == 1) // we make it visible for students only
    $var[] = array("item",
    array("href" => $set->url."/reports.php",
    "name" => "Reports",
    "class" => $this->isActive("reports")),
    "id" => "reports");

    if($user->group->type == 3) // we make it visible for admin only
    $var[] = array("item",
    array("href" => $set->url."/students.php",
    "name" => "Students",
    "class" => $this->isActive("studentslist")),
    "id" => "studentslist");

    if($user->group->type == 3) // we make it visible for admin only
    $var[] = array("item",
    array("href" => $set->url."/teachers.php",
    "name" => "Teachers",
    "class" => $this->isActive("teacherslist")),
    "id" => "teacherslist");

    if($user->group->type == 3) // we make it visible for admin only
    $var[] = array("item",
    array("href" => $set->url."/admin",
    "name" => "Admin Panel",
    "class" => $this->isActive("adminpanel")),
    "id" => "adminpanel");

    if($user->group->type == 1 || $user->group->type == 3) // we make it visible for admin only
    $var[] = array("dropdown",
    array(  array("href" => $set->url."/profile.php?u=".$user->data->userid,
    "name" => "<i class=\"icon-user\"></i> View Profile",
    "class" => 0),
    array("href" => $set->url."/user.php",
    "name" => "<i class=\"icon-cog\"></i> Account Settings",
    "class" => 0),
    array("href" => $set->url."/privacy.php",
    "name" => "<i class=\"icon-lock\"></i> Privacy Settings",
    "class" => 0),
    array("href" => $set->url."/logout.php",
    "name" => "<i class=\"icon icon-arrow-left\"></i> Logout",
    "class" => 0),
  ),
  "class" => 0,
  "style" => 0,
  "name" => "<span class=\"text-warning1\">Welcome, </span>".$user->filter->username,
  "id" => "user");
  return $var;
}

function setActive($id) {
  $this->active = $id;
}

function isActive($id) {
  if($id == $this->active)
  return "active";
  return 0;
}
}
