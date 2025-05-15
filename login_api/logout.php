<?php
session_start();
session_unset();
session_destroy();
header("Location: login_front_end.html");
exit;
?>