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
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
if (!isset($ConfigDetails["tbmdid"])) {
    $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => "tbmdid", "value" => "f584f73e8848d9ace559deee1e5a849f"]];
    $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
}
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
$getUserdetails = $controlfunctions->webtvtheme_getUserAlldetails($conn);
$potrallinksArray = unserialize($ConfigDetails["portallinks"]);
$activeuser = 0;
$blockedusers = 0;
foreach ($getUserdetails as $user) {
    if ($user["status"] == "Active") {
        $activeuser++;
    }
    if ($user["status"] == "Blocked") {
        $blockedusers++;
    }
}
$userCount = count($getUserdetails);
$countportal = 0;
if (!empty($potrallinksArray)) {
    $countportal = count($potrallinksArray);
}
$titleis = "Dashboard";
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
$paranset = ["title" => "DASHBOARD", "activemenu" => "DASHBOARD", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "<style type=\"text/css\">\n    .dashlink:hover{\n      text-decoration: none;\n    }\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1>\n            <i class=\"fa fa-dashboard\"></i> \n            Dashboard \n            ";
if ($ValidLicense != "Active") {
    echo "                <span class=\"licenseerror\">Your license is Expired or Invalid: Get More Info <a href=\"https://www.whmcssmarters.com/clients/index.php?rp=/knowledgebase/188/License-invalid-or-expired---Web-TV-Player---IPTV-Smarters-for-Web.html\" target=\"_blank\">here>></a></span>\n                ";
}
echo "          </h1>\n          \n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><i class=\"fa fa-home fa-lg\"></i></li>\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\">Dashboard</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-6 col-lg-3\">\n          <a href=\"configuration.php\" class=\"dashlink\">\n            <div class=\"widget-small primary coloured-icon\"><i class=\"icon fa fa-server fa-3x\"></i>\n              <div class=\"info\">\n                <h4>Portal DNS</h4>\n                <p><b>";
echo $countportal;
echo "</b></p>\n              </div>\n            </div>\n          </a>\n        </div>\n        <div class=\"col-md-6 col-lg-3\">\n          <a href=\"userlogs.php\" class=\"dashlink\">\n            <div class=\"widget-small info coloured-icon\"><i class=\"icon fa fa-users fa-3x\"></i>\n              <div class=\"info\">\n                <h4>Users</h4>\n                <p><b>";
echo $userCount;
echo "</b></p>\n              </div>\n            </div>\n          </a>\n        </div>\n        <div class=\"col-md-6 col-lg-3\">\n          <a href=\"userlogs.php\" class=\"dashlink userfilter\" data-actionis=\"Active\">\n            <div class=\"widget-small warning coloured-icon\"><i class=\"icon fas fa-user-tie fa-3x\"></i>\n              <div class=\"info\">\n                <h4>Active</h4>\n                <p><b>";
echo $activeuser;
echo "</b></p>\n              </div>\n            </div>\n          </a>\n        </div>\n        <div class=\"col-md-6 col-lg-3\">\n          <a href=\"userlogs.php\" class=\"dashlink userfilter\" data-actionis=\"Blocked\">\n            <div class=\"widget-small danger coloured-icon\"><i class=\"icon fas fa-user-slash fa-3x\"></i>\n              <div class=\"info\">\n                <h4>Blocked</h4>\n                <p><b>";
echo $blockedusers;
echo "</b></p>\n              </div>\n            </div>\n          </a>\n        </div>\n      </div>\n    </main>\n    <form method=\"POST\" action=\"userlogs.php\" id=\"hiddenformusers\">\n      <input type=\"hidden\" name=\"se_portal\" value=\"\">\n      <input type=\"hidden\" name=\"se_username\" value=\"\">\n      <input type=\"hidden\" name=\"se_status\" id=\"statusset\" value=\"\">\n      <input type=\"hidden\" name=\"searchFilter\" value=\"Search\">\n    </form>\n";
$dispatcher->dispatch("mainfooter");
echo "<script type=\"text/javascript\">\n  \$(document).ready(function(){\n    \$(\".userfilter\").click(function(e){\n      e.preventDefault();\n      selectedstatus = \$(this).data(\"actionis\");\n      \$(\"#statusset\").val(selectedstatus);\n      \$(\"#hiddenformusers\").submit();\n    });  \n  });\n</script>";

?>