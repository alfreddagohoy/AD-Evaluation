<?php

if(!isset($_SESSION['token']))
$_SESSION['token'] = sha1(rand()); // random token

?>

<footer class="container-fluid text-center">
  <p>© 2018 - Teachers Evaluation Script Project</p>
</footer>

<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
