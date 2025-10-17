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
$FirstCategoriesIDS = [];
$FinalCategoriesArray = [];
$getCategories_by_page = $CommonController->getCategoriesBySection("radio");
if (!empty($getCategories_by_page) && $getCategories_by_page["result"] == "success") {
    if (!empty($getCategories_by_page["data"])) {
        foreach ($getCategories_by_page["data"] as $StreamsData) {
            if ($StreamsData->stream_type == "radio_streams" && $StreamsData->category_id != "") {
                $FirstCategoriesIDS[$StreamsData->category_id] = "catregoryid";
            }
        }
    }
    sleep(1);
}
if (!empty($FirstCategoriesIDS)) {
    $GetOnlyCateGories = $CommonController->getCategoriesBySection("live");
    if ($GetOnlyCateGories["result"] == "success" && !empty($GetOnlyCateGories["data"])) {
        $FinalCategoriesArray["result"] = "success";
        $counter = 0;
        foreach ($GetOnlyCateGories["data"] as $CatKey) {
            if (array_key_exists($CatKey->category_id, $FirstCategoriesIDS)) {
                $FinalCategoriesArray["data"][$counter] = (int) ["category_id" => $CatKey->category_id, "category_name" => $CatKey->category_name, "parent_id" => "0"];
                $counter++;
            }
        }
    }
}
if (isset($_GET["c"]) && $_GET["c"] != "") {
    $categoryid = $_GET["c"];
    $categoryid = base64_decode($categoryid);
    $portallink = $_SESSION["webTvplayer"]["portallink"];
    $Getblockedsection = $CommonController->GetBlockedDataByPortalLInk($conn, $portallink);
    $getActivePortalID = $CommonController->getActivePortal($conn, $portallink);
    $getBlockedCategoriesIts = $CommonController->getBlockedCategoriesIts($conn, $getActivePortalID, "radio");
    $GetCateGoriesName = $CommonController->getCategoriesBySection("live");
    if (isset($GetCateGoriesName["data"]) && !empty($GetCateGoriesName["data"])) {
        foreach ($GetCateGoriesName["data"] as $Cateval) {
            if ($categoryid == $Cateval->category_id) {
                $CurrentCateName = $Cateval->category_name;
            }
        }
    }
    $variablesArray = ["activepage" => "radio", "pagetitle" => "Radio", "classsname" => "main-bg", "section" => "radio", "funconn" => $funconn, "logovalue" => $LogoIs, "sitetitle" => $sitetitle, "categoryid" => $categoryid, "CurrentCateName" => $CurrentCateName, "getBlockedCategoriesIts" => $getBlockedCategoriesIts, "Getblockedsection" => $Getblockedsection, "funconn" => $funconn, "categories" => $FinalCategoriesArray, "license" => $ValidLicense];
    $dispatcher->dispatch("header", $conn, $variablesArray);
    $dispatcher->dispatch("navigation", $conn, $variablesArray);
    $dispatcher->dispatch("radiolist", $conn, $variablesArray);
    $dispatcher->dispatch("footer", $conn, $variablesArray);
} else {
    echo "<script>window.location.href = 'radio.php';</script>";
    exit;
}

?>