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
$portalslinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$FportalLinks = [""];
if ($portalslinks != "") {
    $FportalLinks = unserialize($portalslinks);
}
$paranset = ["title" => "CAST & CREW", "activemenu" => "CASTCREW", "logovalue" => $LogoIs, "breadcrumblink" => "castcrew.php", "license" => $ValidLicense];
$SectionAre = ["Movies" => "movies", "Series" => "series"];
$TotalMovieCast = $CommonController->GetCastDataByTypeCount($conn, "movies");
$TotalSeriesCast = $CommonController->GetCastDataByTypeCount($conn, "series");
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n  a:hover{\n    text-decoration: none;\n  }\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.eyeicon\n{\n  cursor :pointer;\n}\n.portalcontianers {\n    text-align: left;\n}\n\n.showaddtestlinebutton\n{\n  text-decoration: underline;\n      color: #3085d6;\n}\n.showaddtestlinebutton:hover\n{\n  text-decoration: none;\n  color: #6c8cab;\n}\n.MainSections {\n    width: 18%;\n    float: left;\n    border: 1px solid;\n    margin: 10px;\n    padding: 15px 0;\n    cursor: pointer;\n}\n.initialsettingcontainer {\n    margin-top: 10px;\n    border: 1px solid;\n    padding: 10px;\n}\n.card.customcard {\n    padding: 15px;\n    font-size: 25px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n\n}\n.card.customcard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n.card.customminicard {\n    padding: 15px;\n    font-size: 15px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n    margin-top: 5px;\n}\n.card.customminicard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n\np.labelcontainer {\n    padding: 5px 0px;\n    font-size: 15px;\n    font-weight: bold;\n}\n.custom-anchor2\n{\n  font-size: 15px;\n  text-decoration: underline;\n  color: #3e3e96;\n}\n.custom-anchor2:hover\n{\n  text-decoration: none;\n  color: #9393d8;\n}\n/* simer css here */\n.custom-port-title{\n  position: absolute;\n  top: 16%;\n  left: 34%;\n  font-weight: 500;\n}\n.banner-cus-portalinfo{\n  margin-bottom: 15px;\n  position: relative;\n}\n.totalrecord{\n    position: absolute;\n    top: 4%;\n}\n.banner-cus-portalinfo {\n    margin-bottom: 50px;\n}\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"castcrew.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n           <div class=\"tile\">\n              <div class=\"banner-cus-portalinfo\">\n                 <h3 class=\"tile-title custom-port-title\">\n                   CAST & CREW SECTION\n                 </h3>\n              </div>\n              <div class=\"tile-body\">\n                 <div class=\"portalcontianers\">\n                    <div class=\"row\">\n                       <div class=\"col-md-6\">\n                          <a href=\"castcrew-list.php?sec=movies\">\n                             <div class=\"card customcard\">\n                                movies (";
echo $TotalMovieCast;
echo ")                              \n                             </div>\n                          </a>\n                       </div>\n                       <div class=\"col-md-6\">\n                          <a href=\"castcrew-list.php?sec=series\">\n                             <div class=\"card customcard\">\n                                series (";
echo $TotalSeriesCast;
echo ")                              \n                             </div>\n                          </a>\n                       </div>\n                    </div>\n                 </div>\n              </div>\n           </div>\n        </div>\n      </div>\n      <div class=\"clearix\"></div>\n      </div>\n    </main>\n\n\n\n\n  \n";
$dispatcher->dispatch("mainfooter");
echo " \n";

?>