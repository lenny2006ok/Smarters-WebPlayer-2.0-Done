<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("LOGININDEXPAGEDIRECTORYPATH", dirname(dirname(__FILE__)) . "/");
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "connection.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "connection.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/AdminContoller.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/AdminContoller.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/Controller.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/Controller.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "admin/includes/functions.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "admin/includes/functions.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Common/CommonController.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Common/CommonController.php";
}
$DatabaseObj = new DBConnect();
$conn = $DatabaseObj->makeconnection();
if (array_key_exists("dberror", $conn)) {
    echo "<script>window.location.href = '../dbconfiguration.php';</script>";
    exit;
}
$CommonController = new CommonController();
$checkblockedip = $CommonController->checkblockedip($conn);
if ($checkblockedip == "1") {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
echo "\n<!DOCTYPE html>\n<html lang=\"en\">\n\n<head>\n\t<meta charset=\"utf-8\">\n\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n\t<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->\n\n\t<title>Technical Issue</title>\n\n\t<!-- Google font -->\n\t<link href=\"https://fonts.googleapis.com/css?family=Montserrat:400,700,900\" rel=\"stylesheet\">\n\n\t<!-- Custom stlylesheet -->\n\t<link type=\"text/css\" rel=\"stylesheet\" href=\"../oops/css/style.css\" />\n\n\t<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->\n\t<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n\t<!--[if lt IE 9]>\n\t\t  <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n\t\t  <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n\t\t<![endif]-->\n\n</head>\n\n<body>\n\n\t<div id=\"notfound\">\n\t\t<div class=\"notfound\">\n\t\t\t<div class=\"notfound-404\">\n\t\t\t\t<h1>Oops!</h1>\n\t\t\t</div>\n\t\t\t<p>Your accont is blocked due to maximum numbers of wrong attempts..</p>\n\t\t</div>\n\t</div>\n\n</body><!-- This templates was made by Colorlib (https://colorlib.com) -->\n\n</html>\n";

?>