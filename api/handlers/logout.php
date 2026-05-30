<?php
require_once(__DIR__."/../../app/helpers/session_config.php");

session_unset();
session_destroy();

header("Location: /WebCheck360-backup/WebCheck360/public/login.php");
exit;
?>
