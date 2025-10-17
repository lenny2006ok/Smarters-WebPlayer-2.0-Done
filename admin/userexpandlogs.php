<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("ADMINFILESDIRECTORY", dirname(dirname(__FILE__)) . "/");
if (file_exists(ADMINFILESDIRECTORY . "connection.php")) {
    include_once ADMINFILESDIRECTORY . "connection.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Admin/AdminContoller.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Admin/AdminContoller.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Admin/Controller.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Admin/Controller.php";
}
if (file_exists(ADMINFILESDIRECTORY . "admin/includes/functions.php")) {
    include_once ADMINFILESDIRECTORY . "admin/includes/functions.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Common/CommonController.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Common/CommonController.php";
}
$DatabaseObj = new DBConnect();
$controlfunctions = new controlfunctions();
$conn = $DatabaseObj->makeconnection();
$CommonController = new CommonController();
if (array_key_exists("dberror", $conn)) {
    exit("You are not connected to the database!!");
}
$RowID = "";
if (isset($_GET["u"]) && !empty($_GET["u"])) {
    $RowID = base64_decode($_GET["u"]);
}
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$LicenseIS = isset($ConfigDetails["license"]) && $ConfigDetails["license"] != "" ? $ConfigDetails["license"] : "";
$LicenseLocalKey = isset($ConfigDetails["localKey"]) && $ConfigDetails["localKey"] != "" ? $ConfigDetails["localKey"] : "";
$ValidLicense = "";
if ($LicenseIS != "") {
    $checkLicense = $CommonController->checklicense($LicenseIS, $LicenseLocalKey);
    if ($checkLicense["status"] == "Active") {
        $ValidLicense = $checkLicense["status"];
        if (isset($checkLicense["localkey"]) && !empty($checkLicense["localkey"])) {
            $updateLocalKey = $CommonController->updatelocalkey($checkLicense["localkey"], $conn);
        }
    }
}
if ($ValidLicense != "Active") {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
$titleis = "UNBLOCK IP's";
$dispatcher = new AdminContoller();
$dispatcher->dispatch("createallrecommendedtables", $conn);
$checkadminlogin = $dispatcher->dispatch("checkadminlogin");
if ($checkadminlogin != "1") {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$CommonController->addActivityOnload($conn);
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "../images/blackdemo-Logo.jpg";
$SitetileIs = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$paranset = ["title" => "ALL LOGS", "activemenu" => "USERLOGS", "logovalue" => $LogoIs, "breadcrumblink" => "userexpandlogs.php", "license" => $ValidLicense];
if (isset($_POST["blockedids"])) {
    $ExplodedIDS = explode(",", $_POST["blockedids"]);
    $DeleteBlockedIdsWithIdsArray = $CommonController->DeleteBlockedIdsWithIdsArray($ExplodedIDS, $conn);
    if ($DeleteBlockedIdsWithIdsArray == "1") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"deletesuccess\", \"yes\");\n        window.location.href = \"unblockip.php\";\n      </script>  \n      ";
        exit;
    }
}
$ForTotalCount = $CommonController->getloggedusersfulllist($conn, 0, 0, $RowID);
$totalRecords = isset($ForTotalCount["total"]) && !empty($ForTotalCount["total"]) ? $ForTotalCount["total"] : "";
$limitnum = 50;
if (isset($_REQUEST["pageno"])) {
    $nextPage = $_REQUEST["pageno"] + 1;
    $currentPage = $_REQUEST["pageno"];
    $previousPage = $_REQUEST["pageno"] - 1;
    if ($_REQUEST["pageno"] != 1) {
        $limitstart = $_REQUEST["pageno"] * $limitnum - $limitnum;
    } else {
        $limitstart = 0;
    }
} else {
    $currentPage = 1;
    $nextPage = 2;
    $limitstart = 0;
}
$totalPage = ceil($totalRecords / $limitnum);
$Limit = $limitnum;
$Offset = $limitstart;
$loggeduserslist = $CommonController->getloggedusersfulllist($conn, $Limit, $Offset, $RowID);
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\nbody {\n  background: #000;\n    font-family: sans-serif;\n}\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n</style>\n\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"userlogs.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n          <div class=\"tile\">\n            <div class=\"row\">\n              <div class=\"col-md-12\">\n                <h3 class=\"tile-title\">";
echo $paranset["title"];
echo " LIST</h3>\n              </div>\n            </div>\n            <div class=\"row\">\n              <div class=\"col-md-12 searchfiltercontainer\">\n                <div class=\"row\">\n                   <div class=\"col-md-12 btncontainersec\">\n                    <a class=\"btn btn-primary searchfilterbtn\" style=\"float:left\" href=\"userlogs.php\">\n                      << Back to user list\n                    </a>\n                      \n                   </div>\n                </div>\n              </div>\n            </div>\n            ";
if (isset($loggeduserslist["data"]) && !empty($loggeduserslist["data"])) {
    if (isset($_GET["count"]) && !empty($_GET["count"])) {
        $counterrow = $_GET["count"];
    } else {
        $counterrow = 1;
    }
    echo "              <p style=\"text-align: right;\">Total Records: <b>";
    echo $totalRecords;
    echo "</b></p>\n              <table class=\"table\">\n                <thead>\n                  <tr>\n                    <th>#</th>\n                    <th>DNS</th>\n                    <th>IP ADDRESS</th>\n                    <th>LOGIN TIME</th>\n                    \n                    \n              \n                  </tr>\n                </thead>\n                <tbody>\n                  ";
    foreach ($loggeduserslist["data"] as $key) {
        echo "                    <tr>\n                      <td>";
        echo $counterrow;
        echo "</td>\n                      <td>";
        echo $key["dns"];
        echo "</td>\n                      <td>";
        echo $key["ip_address"];
        echo "</td>\n                      <td>";
        echo date("l, d F Y", strtotime($key["login_time"]));
        echo "</td>\n                      \n                      \n\n                     \n                      \n                    </tr>\n                    ";
        $counterrow++;
    }
    echo "                </tbody>\n              </table>\n              ";
    if (0 < $totalPage) {
        echo "                <div class=\"pagination-container\">\n                   ";
        for ($i = 1; $i <= $totalPage; $i++) {
            echo "                      <a href=\"userexpandlogs.php?u=";
            echo base64_encode($RowID);
            echo "&pageno=";
            echo $i;
            echo "&count=";
            echo $counterrow;
            echo "\" class=\"";
            echo $i == $currentPage ? "active-paging" : "";
            echo "\">\n                        ";
            echo $i;
            echo "                      </a>\n                      ";
        }
        echo " \n                </div>\n                ";
    }
} else {
    echo "                <center><h4>No Result Found!!</h4></center>\n                ";
}
echo "          </div>\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo " \n";

?>