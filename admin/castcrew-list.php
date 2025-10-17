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
$paranset = ["title" => "CAST & CREW", "activemenu" => "CASTCREW", "logovalue" => $LogoIs, "breadcrumblink" => "castcrew-list.php", "license" => $ValidLicense];
$RequestedSection = $_GET["sec"];
$SectionAre = ["Movies" => "movies", "Series" => "series"];
if (!in_array($RequestedSection, $SectionAre)) {
    echo "<script>window.location.href = 'castcrew.php';</script>";
    exit;
}
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n  a:hover{\n    text-decoration: none;\n  }\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.eyeicon\n{\n  cursor :pointer;\n}\n.portalcontianers {\n    text-align: left;\n}\n\n.showaddtestlinebutton\n{\n  text-decoration: underline;\n      color: #3085d6;\n}\n.showaddtestlinebutton:hover\n{\n  text-decoration: none;\n  color: #6c8cab;\n}\n.MainSections {\n    width: 18%;\n    float: left;\n    border: 1px solid;\n    margin: 10px;\n    padding: 15px 0;\n    cursor: pointer;\n}\n.initialsettingcontainer {\n    margin-top: 10px;\n    border: 1px solid;\n    padding: 10px;\n}\n.card.customcard {\n    padding: 15px;\n    font-size: 25px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n\n}\n.card.customcard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n.card.customminicard {\n    padding: 15px;\n    font-size: 15px;\n    text-transform: uppercase;\n    background: #f8f8f8;\n    color: #000;\n    border: 1px solid;\n    margin-top: 5px;\n}\n.card.customminicard:hover {\n    background-color: #c1bfbf;\n    -webkit-transform: translate3d(0, -1px, 0);\n    transform: translate3d(0, -1px, 0);\n    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);\n}\n\np.labelcontainer {\n    padding: 5px 0px;\n    font-size: 15px;\n    font-weight: bold;\n}\n.custom-anchor2\n{\n  font-size: 15px;\n  text-decoration: underline;\n  color: #3e3e96;\n}\n.custom-anchor2:hover\n{\n  text-decoration: none;\n  color: #9393d8;\n}\n/* simer css here */\n.custom-port-title{\n  position: absolute;\n  top: 16%;\n  left: 34%;\n  font-weight: 500;\n}\n.banner-cus-portalinfo{\n  margin-bottom: 15px;\n  position: relative;\n}\n.totalrecord{\n    position: absolute;\n    top: 4%;\n}\n.castcontainer img {\n    width: 100%;\n    max-height: 230px;\n}\n\n.castcontainer {\n    border: 1px solid;\n    margin: 15px;\n    text-align: center;\n    padding: 10px;\n}\n\n.jumpagesec {\n    text-align: right;\n    padding-right: 40px;\n}\np.pagidesc {\n    font-size: 16px;\n    font-weight: 500;\n}\na.pag-links {\n    background-color: #337ab7;\n    padding: 4px;\n    color: #fff;\n    border: 1px solid #22537d;\n}\n\na.pag-links:hover {\n    background-color: #9f98a5;\n}\n\n.activepag-links\n{\n  background-color: #9f98a5 !important;\n}\n.col-md-12.searchsection {\n    margin-top: 15px;\n    margin-bottom: 15px;\n}\n.addborder\n{\n  border: 1px solid red;\n}\na.clearfilter {\n    color: #5b5bc7;\n    text-decoration: underline;\n}\na.clearfilter:hover {\n    color: #2d2d61;\n    text-decoration: none;\n}\n</style>\n";
$CastNameFilterToSubmit = "";
if (isset($_POST["castnamefilter"])) {
    $CastNameFilterToSubmit = $_POST["castnamefilter"];
}
if ($CastNameFilterToSubmit == "") {
    $TotalCastCount = $CommonController->GetCastDataByTypeCount($conn, $RequestedSection);
} else {
    $TotalCastCount = $CommonController->GetCastDataByTypeCountSearch($conn, $RequestedSection, $CastNameFilterToSubmit);
}
$limit = 40;
$orderByis = "az";
$OrderByQueryData = "ORDER BY cast_name ASC";
if (isset($_GET["orderby"]) && $_GET["orderby"] == "popularity") {
    $orderByis = "popularity";
    $OrderByQueryData = "ORDER BY popularity DESC";
}
if (isset($_REQUEST["pageno"])) {
    $nextPage = $_REQUEST["pageno"] + 1;
    $currentPage = $_REQUEST["pageno"];
    $previousPage = $_REQUEST["pageno"] - 1;
    if ($_REQUEST["pageno"] != 1) {
        $limitstart = $_REQUEST["pageno"] * $limit - $limit;
    } else {
        $limitstart = 0;
    }
} else {
    $currentPage = 1;
    $nextPage = 2;
    $limitstart = 0;
}
$TotalPagesAre = ceil($TotalCastCount / $limit);
if ($CastNameFilterToSubmit == "") {
    $GetCastData = $CommonController->GetCastDataByType($conn, $RequestedSection, $limitstart, $limit, $OrderByQueryData);
} else {
    $GetCastData = $CommonController->GetCastDataByTypeSearch($conn, $RequestedSection, $CastNameFilterToSubmit);
}
echo "\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"castcrew-list.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n           <div class=\"tile\">\n              <div class=\"banner-cus-portalinfo\">\n                  <a href=\"castcrew.php\" class=\"btn btn-primary\" style=\"color:#fff;\">BACK</a>\n                 <h3 class=\"tile-title custom-port-title\">\n                   CAST & CREW SECTION (";
echo strtoupper($RequestedSection);
echo ")\n                 </h3>\n              </div>\n              <div class=\"tile-body\">\n                  ";
if (!empty($GetCastData) || $CastNameFilterToSubmit != "") {
    echo "                  <div class=\"row\">\n                    <div class=\"col-md-12 searchsection\">\n                        <form method=\"POST\" action=\"\" id=\"filtercastform\">\n                            <div class=\"row\">\n                              <div class=\"col-md-10\">\n                                  <input type=\"text\" name=\"castnamefilter\" class=\"form-control\" id=\"castnameselector\"  value=\"";
    echo $CastNameFilterToSubmit;
    echo "\" placeholder=\"Filter By Name\">\n                              </div>\n                              <div class=\"col-md-2\">\n                                  <input type=\"submit\" name=\"filter\" class=\"btn btn-primary\" id=\"filterbtn\" value=\"Filter\">\n                              </div>\n                            </div>\n                        </form>  \n                    </div>\n                  </div>\n                  ";
}
echo "                 <div class=\"portalcontianers\">\n                    ";
if (!empty($GetCastData) && $CastNameFilterToSubmit == "") {
    echo "\t                      <div class=\"row\">\n\t                        <div class=\"col-md-6 \">\n\t                          <p class=\"pagidesc\">";
    echo $TotalCastCount;
    echo " Records Found, Showing ";
    echo $currentPage;
    echo " to ";
    echo $TotalPagesAre;
    echo "</p>\n\t                        </div>\n\t                        <div class=\"col-md-6 jumpagesec\" >\n\t                           <p class=\"pagidesc\">\n\t                              SORT BY \n\t                              <select id=\"orderbyselector\">\n\t                                  <option value=\"az\" ";
    echo $orderByis == "az" ? "selected" : "";
    echo ">A - Z</option>\n\t                                  <option value=\"popularity\" ";
    echo $orderByis == "popularity" ? "selected" : "";
    echo ">POPULARITY</option>\n\t                              </select>\n\t                              , JUMP TO PAGE\n\t                              <select id=\"pagdropdown\">\n\t                                ";
    for ($i = 1; $i <= $TotalPagesAre; $i++) {
        echo "\t                                    <option value=\"";
        echo $i;
        echo "\" ";
        echo $currentPage == $i ? "selected" : "";
        echo ">";
        echo $i;
        echo "</option>\n\t                                  ";
    }
    echo " \n\t                              </select>\n\t                            </p>\n\t                        </div>\n\t                      </div>\n\t                      ";
}
if ($CastNameFilterToSubmit != "") {
    echo "\t                      <div class=\"row\">\n\t                        <div class=\"col-md-12 \">\n\t                          <p class=\"pagidesc\">";
    echo $TotalCastCount;
    echo " Records Found <a href=\"castcrew-list.php?sec=";
    echo $RequestedSection;
    echo "&pageno=";
    echo $currentPage;
    echo "&orderby=";
    echo $orderByis;
    echo "\" class=\"clearfilter\">Clear Filter</a></p>\n\t                        </div>\n\t                      </div>\n\t                      ";
}
echo "                    <div class=\"row\">\n                        ";
if (!empty($GetCastData)) {
    foreach ($GetCastData as $Data) {
        echo "                                 <div class=\"col-md-2 castcontainer\">\n                                    <img src=\"";
        echo $Data["image_path"];
        echo "\">\n                                    <p>\n                                      <b>\n                                        ";
        echo $Data["cast_name"];
        echo "  \n                                      </b>\n                                    </p>\n                                 </div> \n                                ";
    }
} else {
    echo "                               <div class=\"col-md-12\">\n                                  <center><h4>No Record Found!!</h4></center>\n                              </div>\n                            ";
}
echo "                    </div>\n                    ";
if (!empty($GetCastData) && $CastNameFilterToSubmit == "") {
    echo "\t                       <div class=\"row\">\n\t                          <div class=\"col-md-12\" style=\"text-align: right;\">\n\t                          ";
    for ($i = 1; $i <= $TotalPagesAre; $i++) {
        echo "\t                              <a href=\"castcrew-list.php?sec=";
        echo $RequestedSection;
        echo "&pageno=";
        echo $i;
        echo "&orderby=";
        echo $orderByis;
        echo "\" class=\"pag-links ";
        echo $currentPage == $i ? "activepag-links" : "";
        echo "\">";
        echo $i;
        echo "</a>\n\t                            ";
    }
    echo " \n\t                          </div> \n\t                       </div> \n\t                    ";
}
echo "                 </div>\n              </div>\n           </div>\n        </div>\n      </div>\n      <div class=\"clearix\"></div>\n      </div>\n    </main>\n\n\n\n\n  \n";
$dispatcher->dispatch("mainfooter");
echo " \n<script type=\"text/javascript\">\n  \$(document).ready(function(){\n\n    \$(\"#filterbtn\").click(function(e){\n      e.preventDefault();\n      valueofinput = \$(\"#castnameselector\").val();\n      if(valueofinput != \"\")\n      {\n          \$(\"#filtercastform\").submit();\n      }\n      else\n      {\n        \$(\"#castnameselector\").addClass(\"addborder\");\n      }\n    });\n\n\n    \$(\"#orderbyselector\").change(function(){\n       selectedVal = \$(this).val();\n       window.location.href = 'castcrew-list.php?sec=";
echo $RequestedSection;
echo "&pageno=";
echo $currentPage;
echo "&orderby='+selectedVal;\n    });\n    \$(\"#pagdropdown\").change(function(){\n       selectedVal = \$(this).val();\n       window.location.href = 'castcrew-list.php?sec=";
echo $RequestedSection;
echo "&pageno='+selectedVal+'&orderby=";
echo $orderByis;
echo "';\n    });\n\n  });\n</script>\n";

?>