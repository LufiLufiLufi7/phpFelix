<!-- LOGOUT-->
<?php
session_start();
session_destroy();
header("Location: /WEB_SAJTOVI_PHP/felix_php/index.php");
exit();
?>