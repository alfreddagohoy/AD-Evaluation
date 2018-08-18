<?php
include "inc/init.php";

$user->logout();

session_unset();
session_destroy();
header("Location: $set->url");
