<?php

if(!isset($_SESSION['token']))
$_SESSION['token'] = sha1(rand()); // random token

?>

<div class="panel-footer copyrights">
<footer class="container-fluid text-center">
  <p>© 2018 - Admin Panel</p>
</footer>
</div>

<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/main.js"></script>

</body>
</html>
