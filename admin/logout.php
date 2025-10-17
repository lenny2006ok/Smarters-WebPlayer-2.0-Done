<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
session_destroy();
session_unset();
session_reset();
echo "<script type=\"text/javascript\">\n  localStorage.setItem(\"logoutmessage\", \"yes\");\n  window.location.href = \"index.php\";\n</script>";

?>