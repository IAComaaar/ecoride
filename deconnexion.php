<?php
session_start();
session_unset();
session_destroy();
header('Location: /ecoride/index.php');
exit;
