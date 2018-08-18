<?php

if($page->navbar == array())
$page->navbar = $presets->GenerateNavbar();

if(!$user->islg())
unset($page->navbar[count($page->navbar)-1]);

?>

<!DOCTYPE html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title><?php echo $page->title; ?></title>
  <meta name="viewport" content="width=device-width">
  <!-- Stylesheets -->
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/bootstrap-responsive.css">
  <link rel="stylesheet" href="../css/main.css">
  <!-- Javascripts -->
  <script src="../js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  <style>
  body {
    padding-top: 60px;
    padding-bottom: 60px;
  }
  </style>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="<?php echo $set->url; ?>"><?php echo $set->site_name; ?></a>
        <div class="nav-collapse collapse">

          <?php
          if(!$user->islg()) {
            echo "
            <span class='pull-right' style='padding: 0px 32px'>
            <!-- <a href='$set->url/register.php' class='btn btn-danger btn-small'>Register</a> -->
            <a href='$set->url/login.php' class='btn btn-success btn-medium'>Login Me</a>
            </span>
            ";
          }
          ?>

          <ul class="nav pull-right">

            <?php

            foreach ($page->navbar as $key => $v) {
              if ($v[0] == 'item') {

                echo "<li".($v[1]['class'] ? " class='".$v[1]['class']."'" : "").">
                <a href='".$v[1]['href']."'>".$v[1]['name']."</a></li>";

              } else if($v[0] == 'dropdown') {

                echo "<li class='dropdown".
                // extra classes
                ($v['class'] ? " ".$v['class'] : "")."'".
                // extra style
                ($v['style'] ? " style='".$v['style']."'" : "").">

                <a href='#' class='dropdown-toggle' data-toggle='dropdown'>".$v['name']." <b class='caret'></b></a>
                <ul class='dropdown-menu'>";
                foreach ($v[1] as $k => $v)
                echo "<li".
                ($v['class'] ? " class='".$v['class']."'" : "").">
                <a href='".$v['href']."'>".$v['name']."</a></li>";
                echo "</ul></li>";
              }
            }
            echo "</ul>";
            echo "
            </div><!--/.nav-collapse -->
            </div>
            </div>
            </div>";

            if($user->data->banned) {

              // we delete the expired banned
              $_unban = $db->getAll("SELECT `userid` FROM `".PREFIX."banned` WHERE `until` < ".time());
              if($_unban)
              foreach ($_unban as $_usr) {
                $db->query("DELETE FROM `".APP_PREFIX."banned` WHERE `userid` = ?i", $_usr->userid);
                $db->query("UPDATE `".APP_PREFIX."users` SET `banned` = '0' WHERE `userid` = ?i", $_usr->userid);
              }

              $_banned = $user->getBan();
              if($_banned)
              $options->error("You were marked as INACTIVE by <a href='$set->url/profile.php?u=$_banned->by'>".$user->showName($_banned->by)."</a> for `<i>".$options->html($_banned->reason)."</i>`.
              Your status will expire in ".$options->tsince($_banned->until, "from now.")."
              ");
            }
            if($user->islg() && $set->email_validation && ($user->data->validated != 1)) {
              $options->error("Your account is not yet activated ! Please check your email !");
            }

            if(isset($_SESSION['success'])){
              $options->success($_SESSION['success']);
              unset($_SESSION['success']);
            }
            if(isset($_SESSION['error'])){
              $options->error($_SESSION['error']);
              unset($_SESSION['error']);

            }

            flush();
