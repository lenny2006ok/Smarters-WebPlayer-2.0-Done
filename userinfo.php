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
if (!isset($_SESSION["webTvplayer"]) && empty($_SESSION["webTvplayer"])) {
    unset($_SESSION["webTvplayer"]);
    session_destroy();
}
$dispatcher = new ClientContoller();
$CommonController = new CommonController();
$funconn = new clientcontrolfunctions();
$Admincontrolfunctions = new controlfunctions();
$funconn->createrecommendedtablesclients($conn);
$checkCurrentUserStatus = $CommonController->checkCurrentUserStatus($conn);
if ($checkCurrentUserStatus != "Active") {
    unset($_SESSION["webTvplayer"]);
    session_destroy();
    echo "    <script type=\"text/javascript\">\n      window.location.href = \"index.php\";\n    </script>\n    ";
    exit;
}
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$LicenseIS = isset($ConfigDetails["license"]) && $ConfigDetails["license"] != "" ? $ConfigDetails["license"] : "";
$LicenseLocalKey = isset($ConfigDetails["localKey"]) && $ConfigDetails["localKey"] != "" ? $ConfigDetails["localKey"] : "";
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "";
$sitetitle = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$portallinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$portallink = $_SESSION["webTvplayer"]["portallink"];
$Getblockedsection = $CommonController->GetBlockedDataByPortalLInk($conn, $portallink);
$variablesArray = ["activepage" => "dashboard", "pagetitle" => "Dashboard", "classsname" => "dash-bg", "logovalue" => $LogoIs, "sitetitle" => $sitetitle, "Getblockedsection" => $Getblockedsection, "portallinks" => $portallinks, "license" => $ValidLicense];
$dispatcher->dispatch("header", $conn, $variablesArray);
$dispatcher->dispatch("userinfo", $conn, $variablesArray);
$dispatcher->dispatch("footer", $conn, $variablesArray);

?>