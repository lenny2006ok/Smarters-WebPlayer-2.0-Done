<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("MAINROOTPATH", dirname(__FILE__) . "/");
if (file_exists(MAINROOTPATH . "connection.php")) {
    include_once MAINROOTPATH . "connection.php";
}
if (file_exists(MAINROOTPATH . "lib/Clients/ClientContoller.php")) {
    include_once MAINROOTPATH . "lib/Clients/ClientContoller.php";
}
if (file_exists(MAINROOTPATH . "lib/Clients/Controller.php")) {
    include_once MAINROOTPATH . "lib/Clients/Controller.php";
}
if (file_exists(MAINROOTPATH . "includes/functions.php")) {
    include_once MAINROOTPATH . "includes/functions.php";
}
if (file_exists(MAINROOTPATH . "admin/includes/functions.php")) {
    include_once MAINROOTPATH . "admin/includes/functions.php";
}
if (file_exists(MAINROOTPATH . "lib/Common/CommonController.php")) {
    include_once MAINROOTPATH . "lib/Common/CommonController.php";
}
$DatabaseObj = new DBConnect();
$conn = $DatabaseObj->makeconnection();
if (array_key_exists("dberror", $conn)) {
    echo "<script>window.location.href = 'oops/index.php';</script>";
    exit;
}
if (isset($_SESSION["webTvplayer"]) && !empty($_SESSION["webTvplayer"])) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
$dispatcher = new ClientContoller();
$CommonController = new CommonController();
$funconn = new clientcontrolfunctions();
$funconn->createrecommendedtablesclients($conn);
$controlfunctions = new controlfunctions();
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$LicenseIS = isset($ConfigDetails["license"]) && $ConfigDetails["license"] != "" ? $ConfigDetails["license"] : "";
$LicenseLocalKey = isset($ConfigDetails["localKey"]) && $ConfigDetails["localKey"] != "" ? $ConfigDetails["localKey"] : "";
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "";
$sitetitle = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$portallinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$CookieData = [];
$ValueUsername = isset($_COOKIE["username"]) && $_COOKIE["username"] != "" ? $_COOKIE["username"] : "";
$ValuePassword = isset($_COOKIE["userpassword"]) && $_COOKIE["userpassword"] != "" ? $_COOKIE["userpassword"] : "";
if ($ValuePassword != "") {
    $ValuePassword = $controlfunctions->webtvtheme_decrypt($ValuePassword);
}
if ($ValueUsername != "" && $ValuePassword != "") {
    $CookieData = ["username" => $ValueUsername, "password" => $ValuePassword];
}
$variablesArray = ["activepage" => "index", "pagetitle" => "Login", "classsname" => "index-bg", "CookieData" => $CookieData, "logovalue" => $LogoIs, "sitetitle" => $sitetitle, "portallinks" => $portallinks, "license" => $ValidLicense];
$dispatcher->dispatch("header", $conn, $variablesArray);
$dispatcher->dispatch("index", $conn, $variablesArray);
$dispatcher->dispatch("footer", $conn, $variablesArray);

?>