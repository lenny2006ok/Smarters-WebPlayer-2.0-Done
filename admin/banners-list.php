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
$ActualID = "";
$ValidPortalLink = "";
$PortalData = [];
if (isset($_GET["p"]) && $_GET["p"] != "") {
    $ActualID = base64_decode($_GET["p"]);
    $checkResponse = $CommonController->getWorkingTestlineByListID($conn, $ActualID);
    if ($checkResponse["result"] == "success") {
        $ValidPortalLink = "true";
        $PortalData["portallink"] = $checkResponse["portallink"];
    }
}
if ($ValidPortalLink == "") {
    echo "<script>window.location.href = 'banners.php';</script>";
    exit;
}
$paranset = ["title" => "BANNERS LIST", "activemenu" => "ADDBANNERS", "logovalue" => $LogoIs, "breadcrumblink" => "banners.php", "license" => $ValidLicense];
if (isset($_POST["blockedids"])) {
    $ExplodedIDS = explode(",", $_POST["blockedids"]);
    $DeleteBlockedIdsWithIdsArray = $CommonController->DeleteBlockedIdsWithIdsArray($ExplodedIDS, $conn);
    if ($DeleteBlockedIdsWithIdsArray == "1") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"deletesuccess\", \"yes\");\n        window.location.href = \"unblockip.php\";\n      </script>  \n      ";
        exit;
    }
}
$SectionAre = ["Movies" => "movies", "Series" => "series"];
$SectionShow = "mainlist";
$backLink = "banners.php";
if (in_array($_GET["sec"], $SectionAre)) {
    $SectionShow = "InnerList";
    $backLink = "banners-list.php?p=" . $_GET["p"];
    if (isset($_GET["cat"]) && !empty($_GET["cat"])) {
        $SectionShow = "streamsList";
        $backLink = "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"];
        if (isset($_GET["a"]) && !empty($_GET["a"])) {
            $SectionShow = "streamsAddList";
            $backLink = "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"] . "&cat=" . $_GET["cat"];
        }
    }
}
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n  a:hover{\n    text-decoration: none;\n  }\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.eyeicon\n{\n  cursor :pointer;\n}\n.portalcontianers {\n    text-align: left;\n}\n\n.showaddtestlinebutton\n{\n  text-decoration: underline;\n      color: #3085d6;\n}\n.showaddtestlinebutton:hover\n{\n  text-decoration: none;\n  color: #6c8cab;\n}\n.MainSections {\n    width: 18%;\n    float: left;\n    border: 1px solid;\n    margin: 10px;\n    padding: 15px 0;\n    cursor: pointer;\n}\n.initialsettingcontainer {\n    margin-top: 10px;\n    border: 1px solid;\n    padding: 10px;\n}\n.card.customcard {\n    padding: 15px;\n    font-size: 25px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n\n}\n.card.customcard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n.card.customminicard {\n    padding: 15px;\n    font-size: 15px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n    margin-top: 5px;\n}\n.card.customminicard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n\np.labelcontainer {\n    padding: 5px 0px;\n    font-size: 15px;\n    font-weight: bold;\n}\n.custom-anchor2\n{\n  font-size: 15px;\n  text-decoration: underline;\n  color: #3e3e96;\n}\n.custom-anchor2:hover\n{\n  text-decoration: none;\n  color: #9393d8;\n}\n/* simer css here */\n.custom-port-title{\n  position: absolute;\n  top: 16%;\n  left: 34%;\n  font-weight: 500;\n}\n.banner-cus-portalinfo{\n  margin-bottom: 15px;\n  position: relative;\n}\n.totalrecord{\n    position: absolute;\n    top: 4%;\n}\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"banners.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n          <div class=\"tile\">\n            <div class=\"banner-cus-portalinfo\">\n            \t<a href=\"";
echo $backLink;
echo "\" class=\"btn btn-primary\" style=\"color:#fff;\">BACK</a>\n              <h3 class=\"tile-title custom-port-title\">\n                BANNERS OF \n                <b style=\"text-decoration: underline;\">\n                  ";
echo isset($PortalData["portallink"]) && !empty($PortalData["portallink"]) ? $PortalData["portallink"] : "";
echo "                </b>\n              </h3>\n            </div>\n            <div class=\"tile-body\">\n              <div class=\"portalcontianers\">\n                <div class=\"row\">\n                    ";
if ($SectionShow == "mainlist") {
    foreach ($SectionAre as $Skey => $Sval) {
        echo "          \t\t\t\t\t\t  <div class=\"col-md-6\">\n          \t\t\t\t\t\t  \t<a href=\"banners-list.php?p=";
        echo $_GET["p"];
        echo "&sec=";
        echo $Sval;
        echo "\">\n          \t\t\t\t\t\t      \t<div class=\"card customcard\">\n          \t\t\t\t\t\t      \t\t";
        echo $Sval;
        echo "          \t\t\t\t\t\t      \t</div>\n          \t\t\t\t\t\t    </a>\n          \t\t\t\t\t\t  </div>\n          \t\t\t\t\t\t";
    }
} else {
    if ($SectionShow == "InnerList") {
        $selectedSec = $_GET["sec"];
        echo "                    \t<div class=\"col-md-12\">\n                    \t\t<center>\n                    \t\t\t<h4>\n                    \t\t\t\tCATEGORIES LIST ( ";
        echo strtoupper($selectedSec);
        echo " )\n                    \t\t\t</h4>\n                    \t\t</center>\n                    \t</div>\n                    \t";
        $CateGoriesListSection = $CommonController->getCategoriesBySectionAndListID($conn, $ActualID, $selectedSec);
        $GetBannersCount = $CommonController->GetBannersListByCateGoryData($conn, $ActualID, $selectedSec);
        $cateisall = base64_encode("all");
        echo "                      <div class=\"col-md-6\">\n                            <a href=\"banners-list.php?p=";
        echo $_GET["p"];
        echo "&sec=";
        echo $selectedSec;
        echo "&cat=";
        echo $cateisall;
        echo "\" class=\"categorySelect\" data-nametosave=\"ALL\">\n                                <div class=\"card customminicard\">\n                                  ALL (";
        echo isset($GetBannersCount["all"]) && !empty($GetBannersCount["all"]) ? $GetBannersCount["all"] : "0";
        echo ")\n                                </div>\n                            </a>\n                          </div>\n                      ";
        if ($CateGoriesListSection["result"] == "success" && !empty($CateGoriesListSection["data"])) {
            foreach ($CateGoriesListSection["data"] as $DataKey) {
                $cateis = base64_encode($DataKey->category_id);
                echo "          \t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n          \t\t\t\t\t\t\t\t  \t<a href=\"banners-list.php?p=";
                echo $_GET["p"];
                echo "&sec=";
                echo $selectedSec;
                echo "&cat=";
                echo $cateis;
                echo "\" class=\"categorySelect\" data-nametosave=\"";
                echo $DataKey->category_name;
                echo "\">\n          \t\t\t\t\t\t\t\t      \t<div class=\"card customminicard\">\n          \t\t\t\t\t\t\t\t      \t\t";
                echo $DataKey->category_name;
                echo " (";
                echo isset($GetBannersCount[$DataKey->category_id]) && !empty($GetBannersCount[$DataKey->category_id]) ? $GetBannersCount[$DataKey->category_id] : "0";
                echo ")\n          \t\t\t\t\t\t\t\t      \t</div>\n          \t\t\t\t\t\t\t\t    </a>\n          \t\t\t\t\t\t\t\t  </div>\n          \t\t\t\t\t\t\t\t";
            }
        } else {
            echo "          \t\t\t\t\t\t\t<div class=\"col-md-12\">\n          \t\t\t\t\t\t\t\t<center>\n          \t\t\t\t\t\t\t\t\t<h4>\n          \t\t\t\t\t\t\t\t\t\tNo Category Found!!!\n          \t\t\t\t\t\t\t\t\t</h4>\n          \t\t\t\t\t\t\t\t</center>\n          \t\t\t\t\t\t\t</div>\n          \t\t\t\t\t\t\t";
        }
    } else {
        if ($SectionShow == "streamsList") {
            $selectedSec = $_GET["sec"];
            $portallistid = base64_decode($_GET["p"]);
            $catIDIS = base64_decode($_GET["cat"]);
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $portallistid]];
            $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $ListPortalLink = isset($ExecuteQuery[0]["portallink"]) && !empty($ExecuteQuery[0]["portallink"]) ? $ExecuteQuery[0]["portallink"] : "";
                $bar = "/";
                if (substr($ListPortalLink, -1) == "/") {
                    $bar = "";
                }
                $ListPortalLink = $ListPortalLink . $bar;
                $QueryData = ["request" => "Get", "table" => "webtvtheme_banners", "data" => ["portalurl" => $ListPortalLink, "type" => $selectedSec, "category" => $catIDIS], "extra" => ["ORDER BY id DESC"]];
                $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (!empty($ExecuteQuery)) {
                    $totalBanners = count($ExecuteQuery);
                    echo "                              <div class=\"col-md-12\">\n                                <center>\n                                  <h4>\n                                    BANNERS LIST (";
                    echo strtoupper($selectedSec);
                    echo ": <span class=\"CateShowSpan\" style=\"    text-decoration: underline;\"></span>)\n                                  </h4>\n                                </center>\n                              </div>\n                              <div class=\"col-md-12\">\n                                <span class=\"totalrecord\">Total Banners :<b>";
                    echo $totalBanners;
                    echo "</b></span>\n                                <br>\n                                 ";
                    if ($totalBanners < 10) {
                        echo "                                      <p style=\"text-align: right;\">\n                                       \n                                            <a href=\"";
                        echo "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"] . "&cat=" . $_GET["cat"] . "&a=add";
                        echo "\" class=\"btn btn-primary\">Add New</a>\n                                           \n                                        \n                                      </p>\n                                      ";
                    } else {
                        echo "                                      <p style=\"text-align: right;\">                                       \n                                            &nbsp;\n                                      </p>\n                                      ";
                    }
                    echo "                                  <table class=\"table\">\n                                    <thead>\n                                      <tr>\n                                        <th scope=\"col\">#</th>\n                                        <th scope=\"col\">Banner</th>\n                                        <th scope=\"col\">Name</th>\n                                        <th scope=\"col\">Type</th>\n                                        <th scope=\"col\">Action</th>\n                                      </tr>\n                                    </thead>\n                                    <tbody>\n                                      ";
                    $BannersCounter = 1;
                    foreach ($ExecuteQuery as $skey) {
                        $StreamINfo = unserialize($skey["streamdata"]);
                        echo "                                        <tr>\n                                          <th scope=\"row\">";
                        echo $BannersCounter;
                        echo "</th>\n                                          <td><img src=\"";
                        echo $skey["banner"];
                        echo "\" style=\"width:100px;height: 50px;\"></td>\n                                          <td>";
                        echo $StreamINfo["name"];
                        echo "</td>\n                                          <td>";
                        echo $skey["type"];
                        echo "</td>\n                                          <td><button class=\"btn btn-danger deletelistnumber\" data-bannerlistid=\"";
                        echo $skey["id"];
                        echo "\">DELETE</button></td>\n                                        </tr>\n                                        ";
                        $BannersCounter++;
                    }
                    echo "                                    </tbody>\n                                  </table>\n                              </div>\n                            ";
                } else {
                    echo "                            <div class=\"col-md-12\">\n                                <center>\n                                  <h2>\n                                    BANNERS LIST (";
                    echo strtoupper($selectedSec);
                    echo ": <span class=\"CateShowSpan\" style=\"    text-decoration: underline;\"></span>)\n                                  </h2>\n                                </center>\n                              </div>\n                              <div class=\"col-md-12\">\n                                <p style=\"text-align: right;\">\n                                  <a href=\"";
                    echo "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"] . "&cat=" . $_GET["cat"] . "&a=add";
                    echo "\" class=\"btn btn-primary\">Add New</a>\n                                </p>\n                                <center>\n                                  <h4>\n                                    No Banner Found!!!\n                                  </h4>\n                                </center>\n                              </div>\n                            ";
                }
            }
        } else {
            if ($SectionShow == "streamsAddList") {
                $selectedSec = $_GET["sec"];
                $StreamSecCustom = "Movie";
                if ($selectedSec == "series") {
                    $StreamSecCustom = "Series";
                }
                $selectedSec = $_GET["sec"];
                $portallistid = base64_decode($_GET["p"]);
                $catIDIS = base64_decode($_GET["cat"]);
                $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $portallistid]];
                $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (!empty($ExecuteQuery)) {
                    $ListPortalLink = isset($ExecuteQuery[0]["portallink"]) && !empty($ExecuteQuery[0]["portallink"]) ? $ExecuteQuery[0]["portallink"] : "";
                    $bar = "/";
                    if (substr($ListPortalLink, -1) == "/") {
                        $bar = "";
                    }
                    $ListPortalLink = $ListPortalLink . $bar;
                    $QueryData = ["request" => "Get", "table" => "webtvtheme_banners", "data" => ["portalurl" => $ListPortalLink, "type" => $selectedSec, "category" => $catIDIS], "extra" => ["ORDER BY id DESC"]];
                    $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                    $totalBannersCount = count($ExecuteQuery);
                    if (9 < $totalBannersCount) {
                        echo "                            <script>window.location.href = '";
                        echo "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"] . "&cat=" . $_GET["cat"];
                        echo "';</script>\n                          ";
                        exit;
                    }
                    echo "  \n                        <div class=\"col-md-12\">\n                          <center>\n                            <h4>\n                              ADD BANNER (";
                    echo strtoupper($selectedSec);
                    echo " : <span class=\"CateShowSpan\" style=\"    text-decoration: underline;\"></span>)\n                            </h4>\n                          </center>\n                        </div>\n                        <div class=\"col-md-2 selectionsection\">\n                        </div>\n                        <div class=\"col-md-8 selectionsection\">\n                          <div class=\"form-container\">\n                            <div class=\"form-group row\">\n                              <div class=\"col-md-3\">\n                                <p class=\"labelcontainer\">Select ";
                    echo $StreamSecCustom;
                    echo ":</p>\n                              </div>\n                              <div class=\"col-md-9\">\n                                <div class=\"selectcontainer\">\n                                  <select class=\"form-control\" id=\"streamslistselector\" disabled>\n                                    <option value=\"\">Loading Data...</option>\n                                  </select>\n                                </div>\n                              </div>\n                            </div>\n                          </div>\n                        </div>\n                        <div class=\"col-md-2 selectionsection\">\n                        </div>\n                        <!-- -->\n                        <div class=\"col-md-2 manualaddnamesection d-none\">\n                        </div>\n                        <div class=\"col-md-8 manualaddnamesection d-none\">\n                          <div class=\"form-container\">\n                            <div class=\"form-group row\">\n                              <div class=\"col-md-3\">\n                                <p class=\"labelcontainer\">Search Banner By ";
                    echo $StreamSecCustom;
                    echo " Name <a href=\"#\" title=\"If no banner found from the selected must be some caused by special characters and numeric value you can search banner by manually enter name or you can add upload banner image in media section and select from media\" data-toggle=\"tooltip\"><i class=\"fa fa-info-circle\"></i></a>:</p>\n                              </div>\n                              <div class=\"col-md-9\">\n                               <div class=\"row\">\n                                  <div class=\"col-md-10\" >\n                                      <input type=\"text\" class=\"form-control\" placeholder=\"";
                    echo $StreamSecCustom;
                    echo " Name\" id=\"nameselector\">\n                                  </div>\n                                  <div class=\"col-md-2\" >\n                                      <button class=\"btn btn-primary\" id=\"searchByNametbn\">Search</button>\n                                  </div>\n                              </div>\n                              </div>\n                            </div>\n                          </div>\n                        </div>\n                        <div class=\"col-md-2 manualaddnamesection d-none\">\n                        </div>\n                        <!-- -->\n                        <div class=\"col-md-2 showbannersection d-none\">\n                        </div>\n                        <div class=\"col-md-8 showbannersection d-none\">\n                          <div class=\"form-container\">\n                            <div class=\"form-group row\">\n                              <div class=\"col-md-3\">\n                              </div>\n                              <div class=\"col-md-9\">\n                                <div class=\"row\">\n                                  <div class=\"col-md-12\" style=\"text-align: right; margin-bottom: 10px;\">\n                                    <a href=\"#\" class=\"custom-anchor2 showmediafiles\" data-filesfor=\"banners\">Select From Media</a>\n                                  </div>  \n                                  <div class=\"col-md-12\">\n                                    <img src=\"images/nobannerfound.png\" id=\"BannerImgContainer\" style=\"width: 100%;border-radius: 8px;\">\n                                  </div>\n                                </div>\n                              </div>\n                            </div>\n                          </div>\n                        </div>\n                        <div class=\"col-md-2 showbannersection d-none\">\n                        </div>\n                        <div class=\"col-md-2 showbannersection d-none\">\n                        </div>\n                        <div class=\"col-md-8 showbannersection d-none\">\n                          <div class=\"form-container\">\n                            <div class=\"form-group row\">\n                              <div class=\"col-md-3\">\n                              </div>\n                              <div class=\"col-md-9\">\n                                <div class=\"row\">\n                                  <div class=\"col-md-12\" style=\"text-align: center; margin-bottom: 10px;\">\n                                    <input type=\"hidden\" id=\"streamRating\" value=\"0\">\n                                    <a href=\"#\" class=\"btn btn-primary\" id=\"savebannerimages\">Save Changes</a>\n                                  </div> \n                                </div>\n                              </div>\n                            </div>\n                          </div>\n                        </div>\n                        <div class=\"col-md-2 showbannersection d-none\">\n                        </div>\n                        ";
                }
            }
        }
    }
}
echo "                </div>\n              </div>\n            </div>\n          </div>\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n\n\n\n\n  \n";
$dispatcher->dispatch("mainfooter");
if ($SectionShow == "streamsAddList") {
    echo "\t\t<script type=\"text/javascript\">\n\t    \$(document).ready(function(){\n        categorynameis = localStorage.getItem(\"categorynameis\");\n        \$(\".CateShowSpan\").text(categorynameis);\n\n\t    \tGetStreamByCateGoryAndSec();\n        \$(\"#streamslistselector\").change(function(){\n            var thisvaris = \$(this);\n            var SteamRating = \$(\"#streamslistselector option:selected\").data(\"ratingis\");\n            \$(\".manualaddnamesection\").addClass(\"d-none\");\n            \$(\".showbannersection\").addClass(\"d-none\");\n            \$(\"#nameselector\").val(\"\");\n            \$(\"#BannerImgContainer\").attr(\"src\",\"images/nobannerfound.png\");\n            \$(\"#streamRating\").val(SteamRating);\n            var StreamID = \$(this).val();\n            var portalID = '";
    echo base64_decode($_GET["p"]);
    echo "';\n            var SectionIS = '";
    echo $_GET["sec"];
    echo "';\n            if(StreamID != \"\")\n            {\n              thisvaris.prop(\"disabled\",true);\n              jQuery.ajax({                   \n                type:\"POST\",              \n                url:\"includes/ajax-control.php\", \n                dataType:\"text\", \n                data :{\n                    action:'GetStreamDataByStremID',\n                    portalID:portalID,\n                    SectionIS:SectionIS,\n                    StreamID:StreamID\n                },\n                success:function(response2){\n                    thisvaris.prop(\"disabled\",false);\n                    \$(\".manualaddnamesection\").removeClass(\"d-none\");\n                    \$(\".showbannersection\").removeClass(\"d-none\");\n                    if(response2 != \"\")\n                    {\n                        \$(\"#BannerImgContainer\").attr(\"src\",response2);\n                    }\n                } \n              });\n            }\n        });\n        \$(\"#searchByNametbn\").click(function(){\n            var SerThisVar = \$(this);\n            var streamName = \$(\"#nameselector\").val();\n            var SectionIS = '";
    echo $_GET["sec"];
    echo "';\n            if(streamName != \"\")\n            {\n              \$(\".showbannersection\").addClass(\"d-none\");\n              SerThisVar.prop(\"disabled\",true);\n              \$(\"#streamslistselector\").prop(\"disabled\",true);\n              \$(\"#BannerImgContainer\").attr(\"src\",\"images/nobannerfound.png\");\n              jQuery.ajax({                   \n                type:\"POST\",              \n                url:\"includes/ajax-control.php\", \n                dataType:\"text\", \n                data :{\n                    action:'GetStreambyBannerName',\n                    SectionIS:SectionIS,\n                    streamName:streamName\n                },\n                success:function(response2){\n                    SerThisVar.prop(\"disabled\",false);\n                    \$(\"#streamslistselector\").prop(\"disabled\",false);\n                    \$(\".showbannersection\").removeClass(\"d-none\");\n                    if(response2 != \"\")\n                    {\n                        \$(\"#BannerImgContainer\").attr(\"src\",response2);\n                    }\n                } \n              });\n            }\n            else\n            {\n                \$(\"#nameselector\").addClass(\"is-invalid\");\n            }\n        });\n        \$(\"#nameselector\").click(function(p){\n            \$(this).removeClass(\"is-invalid\");\n        });\n\n        \$(\"#savebannerimages\").click(function(e){\n          e.preventDefault();\n          var portalID = '";
    echo base64_decode($_GET["p"]);
    echo "';\n          var SectionIS = '";
    echo $_GET["sec"];
    echo "';\n          var Categoryid = '";
    echo base64_decode($_GET["cat"]);
    echo "';\n          var streamID = \$('#streamslistselector').val();\n          var streamRating = \$('#streamRating').val();\n          var bannerImage = \$('#BannerImgContainer').attr(\"src\");\n          \$(\"#streamslistselector\").prop(\"disabled\",true); \n          \$(\"#savebannerimages\").prop(\"disabled\",true); \n          \$(\"#searchByNametbn\").prop(\"disabled\",true); \n          \$(\"#nameselector\").prop(\"readonly\",true); \n          \$(\"#savebannerimages\").text(\"Processing...\"); \n          jQuery.ajax({                   \n              type:\"POST\",              \n              url:\"includes/ajax-control.php\", \n              dataType:\"text\", \n              data :{\n                  action:'SaveSliderBannerDetails',\n                  portalID:portalID,\n                  SectionIS:SectionIS,\n                  Categoryid:Categoryid,\n                  streamID:streamID,\n                  streamRating:streamRating,\n                  bannerImage:bannerImage\n              },\n              success:function(response2){  \n                \$(\"#savebannerimages\").prop(\"disabled\",false);              \n                \$(\"#streamslistselector\").prop(\"disabled\",false);              \n                \$(\"#searchByNametbn\").prop(\"disabled\",false);\n                \$(\"#nameselector\").prop(\"readonly\",false);   \n                \$(\"#savebannerimages\").text(\"Save Changes\");             \n                if(response2 == \"1\")\n                {\n                    window.location.href = 'banners-list.php?p=";
    echo $_GET["p"];
    echo "&sec=";
    echo $_GET["sec"];
    echo "&cat=";
    echo $_GET["cat"];
    echo "';\n                }\n              } \n            });\n\n        });\n\t    });  \n\t    function GetStreamByCateGoryAndSec()\n\t    {\n\t    \tvar portalID = '";
    echo base64_decode($_GET["p"]);
    echo "';\n\t    \tvar SectionIS = '";
    echo $_GET["sec"];
    echo "';\n\t    \tvar Categoryid = '";
    echo base64_decode($_GET["cat"]);
    echo "';\n\t    \tjQuery.ajax({                   \n\t            type:\"POST\",              \n\t            url:\"includes/ajax-control.php\", \n\t            dataType:\"text\", \n\t            data :{\n\t                action:'getstreamlisttestdetails',\n\t                portalID:portalID,\n\t                SectionIS:SectionIS,\n\t                Categoryid:Categoryid\n\t            },\n\t            success:function(response2){\n\t            \tif(response2 != \"\")\n                {\n                  \$(\"#streamslistselector\").prop(\"disabled\",false);\n                  \$(\"#streamslistselector\").html(\"\");\n                  \$(\"#streamslistselector\").html(response2);\n                }\n\t            } \n\t          });\n\t    } \n\t  \t</script>\n\t";
}
if ($SectionShow == "streamsList") {
    echo "<script type=\"text/javascript\">\n  \$(document).ready(function(){\n    categorynameis = localStorage.getItem(\"categorynameis\");\n    \$(\".CateShowSpan\").text(categorynameis);\n\n    \$(\".deletelistnumber\").click(function(w){\n      w.preventDefault();\n      selectorID = \$(this).data(\"bannerlistid\");\n       Swal.fire({\n          title: 'Are you sure?',\n          text: \"You want delete this banner?\",\n          type: 'warning',\n          showCancelButton: true,\n          confirmButtonColor: '#3085d6',\n          cancelButtonColor: '#d33',\n          confirmButtonText: 'Yes, go for it!'\n        }).then((result) => {\n          if (result.value) {\n              swal.close();\n              Swal.fire({\n                text: \"Processing.....\",\n                allowOutsideClick: false,\n                showCancelButton: false,\n                showConfirmButton: false\n              })\n              jQuery.ajax({\n                type:\"POST\",\n                url:\"includes/ajax-control.php\",\n                dataType:\"text\",\n                data:{\n                action:'deletebannerbyid',\n                selectorID:selectorID\n                },  \n                success:function(response){\n                  swal.close();\n                  Swal.fire({\n                    text: \"Banner Successfully Deleted!!!\",\n                    type: 'success',\n                    allowOutsideClick: false,\n                    showCancelButton: false,\n                    showConfirmButton: false\n                  })\n                  setTimeout(function(){ \n                    window.location.href = '";
    echo "banners-list.php?p=" . $_GET["p"] . "&sec=" . $_GET["sec"] . "&cat=" . $_GET["cat"];
    echo "'; \n                   }, 1500);\n                   \n                }\n              });\n          }\n        })\n    });\n  });\n</script>\n";
}
if ($SectionShow == "InnerList") {
    echo "<script type=\"text/javascript\">\n  \$(document).ready(function(){\n      \$(\".categorySelect\").click(function(e){\n        e.preventDefault();\n        CateGoryName = \$(this).data(\"nametosave\");\n        LinkGenerate = \$(this).attr(\"href\");\n        localStorage.setItem(\"categorynameis\", CateGoryName);\n        window.location.href = LinkGenerate;\n      });\n  });\n</script>\n";
}
echo " \n";

?>