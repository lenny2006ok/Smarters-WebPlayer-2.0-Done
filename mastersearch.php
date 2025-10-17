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
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$dispatcher = new ClientContoller();
$CommonController = new CommonController();
$funconn = new clientcontrolfunctions();
$funconn->createrecommendedtablesclients($conn);
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$LicenseIS = isset($ConfigDetails["license"]) && $ConfigDetails["license"] != "" ? $ConfigDetails["license"] : "";
$LicenseLocalKey = isset($ConfigDetails["localKey"]) && $ConfigDetails["localKey"] != "" ? $ConfigDetails["localKey"] : "";
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "";
$sitetitle = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$checkCurrentUserStatus = $CommonController->checkCurrentUserStatus($conn);
if ($checkCurrentUserStatus != "Active") {
    unset($_SESSION["webTvplayer"]);
    session_destroy();
    echo "    <script type=\"text/javascript\">\n      window.location.href = \"index.php\";\n    </script>\n    ";
    exit;
}
$portallink = $_SESSION["webTvplayer"]["portallink"];
$Getblockedsection = $CommonController->GetBlockedDataByPortalLInk($conn, $portallink);
$getActivePortalID = $CommonController->getActivePortal($conn, $portallink);
$getBlockedCategoriesIts = $CommonController->getBlockedCategoriesIts($conn, $getActivePortalID, "live");
$GetFavforCategories = $funconn->webtvpanel_getFavforCategories("live", $conn);
$newCategoryIDArray = [];
$FinalCategoriesArray = [];
$GetCateGories = $CommonController->getCategoriesBySection("live");
$Getliveviewsettings = $funconn->webtvpanel_getliveviewsettings($conn);
if (!empty($GetCateGories) && $GetCateGories["result"] == "success") {
    if (!empty($GetFavforCategories)) {
        $newCategoryIDArray["favorite"] = (int) ["category_id" => "favorite", "category_name" => "Favorite", "parent_id" => "0"];
    }
    $FinalCategoriesArray = $GetCateGories;
    foreach ($FinalCategoriesArray["data"] as $catkey) {
        if (!in_array($catkey->category_id, $getBlockedCategoriesIts)) {
            $newCategoryIDArray[$catkey->category_id] = (int) ["category_id" => $catkey->category_id, "category_name" => $catkey->category_name, "parent_id" => $catkey->parent_id];
        }
        $parentcondition[$catkey->category_id] = $funconn->webtvpanel_parentcondition($catkey->category_name, $conn);
        $FinalCategoriesArray["data"] = $newCategoryIDArray;
    }
}
$Getparentpinformart = $funconn->webtvpanel_getparentpinformart($conn);
$categoryid = base64_decode($categoryid);
$variablesArray = ["activepage" => "categories", "pagetitle" => "Live Categories", "classsname" => "dash-bg", "section" => "live", "logovalue" => $LogoIs, "sitetitle" => $sitetitle, "parentcondition" => $parentcondition, "Getparentpinformart" => $Getparentpinformart, "categoryid" => $categoryid, "Getblockedsection" => $Getblockedsection, "liveview" => $Getliveviewsettings, "categories" => $FinalCategoriesArray, "license" => $ValidLicense];
$dispatcher->dispatch("header", $conn, $variablesArray);
$dispatcher->dispatch("navigation", $conn, $variablesArray);
$dispatcher->dispatch("mastersearch", $conn, $variablesArray);
$dispatcher->dispatch("footer", $conn, $variablesArray);

?>