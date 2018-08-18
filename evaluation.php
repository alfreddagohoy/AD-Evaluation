<?php
include "inc/init.php";

$page->title = "Evaluation - ". $set->site_name;

include 'header.php';
?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span3">
      <div class="well sidebar-nav sidebar-nav-fixed">
        <ul class="nav nav-list">
          <li class="nav-header">MAIN OPTIONS</li>
          <li><a href='index.php'><i class='icon-home'></i></a></li>
          <li><a href='reports.php'>Reports</a></li>
          <li><a href='#'>Latest News</a></li>
          <li><a href='#'>Frequently Asked Questions</a></li>
        </ul>
      </div>
    </div>

    <div class="span8">

      <?php
      echo "
      <div class=\"container\">
      <div class=\"hero-unit\">
      <legend><h1>Evaluation</h1></legend>
      <p>

      </p>";
      if(!$user->islg()) {
        echo "<p>
        <a class=\"btn btn-success btn-large\" href=\"$set->url/login.php\">Login</a>
        <a class=\"btn btn-large\" href=\"$set->url/register.php\">Forgot Password</a>
        </p>";
      }
      echo
      "</div>
      </div>";
      ?>

    </div>
  </div>
</div><!--/.fluid-container-->

<?php
include 'footer.php';
?>
