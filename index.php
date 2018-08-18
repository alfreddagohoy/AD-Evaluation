<?php
include 'inc/init.php';
$page->title = "Home - ". $set->site_name;
include 'header.php';
?>

<div class="container-fluid">
  <div class="row-fluid">

    <div class="span8">

      <?php
      echo "
      <div class=\"container\">
      <div class=\"hero-unit\">
      <div class=\"page-header\">
      <legend>
      <h1>Welcome, <small style='text-transform:capitalize;'><u><i>".$user->filter->username."!</i></u></small></h1>
      <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
      </legend>

      <div class=\"span12\">
      <div class=\"span4 panel panel-default\">
      <div class=\"panel-heading\">
      <h3 class=\"panel-title\">Admin Login</h3>
      </div>
      <div class=\"panel-body\">
      Username: admin<br />
      Password: 1234
      </div>
      </div>

      <div class=\"span4 panel panel-default\">
      <div class=\"panel-heading\">
      <h3 class=\"panel-title\">Student Login</h3>
      </div>
      <div class=\"panel-body\">
      Username: student<br />
      Password: 1234
      </div>
      </div>
      </div>

      </div>";

      if($user->group->type == 3) {
        echo "<div class=\"btn-group\">
        <p><br /><br /><a class=\"btn btn-danger btn-large\" href=\"$set->url/register.php\">Create Account</a></p>
        </div>";
      }
      echo
      "</div>
      </div>";
      ?>
    </div>
  </div>
</div><

<?php
include 'footer.php';
?>
