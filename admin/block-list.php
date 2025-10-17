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
    echo "<script>window.location.href = 'blockcontent.php';</script>";
    exit;
}
$paranset = ["title" => "BLOCK CONTENT", "activemenu" => "BLOCKCONTENT", "logovalue" => $LogoIs, "breadcrumblink" => "blockcontent.php", "license" => $ValidLicense];
if (isset($_POST["blockedids"])) {
    $ExplodedIDS = explode(",", $_POST["blockedids"]);
    $DeleteBlockedIdsWithIdsArray = $CommonController->DeleteBlockedIdsWithIdsArray($ExplodedIDS, $conn);
    if ($DeleteBlockedIdsWithIdsArray == "1") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"deletesuccess\", \"yes\");\n        window.location.href = \"unblockip.php\";\n      </script>  \n      ";
        exit;
    }
}
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n\n/* toggle css start here*/\n/*.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}*/\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.eyeicon\n{\n  cursor :pointer;\n}\n.portalcontianers {\n    text-align: center;\n}\n\n.showaddtestlinebutton\n{\n  text-decoration: underline;\n      color: #3085d6;\n}\n.showaddtestlinebutton:hover\n{\n  text-decoration: none;\n  color: #6c8cab;\n}\n.MainSections {\n    width: 18%;\n    float: left;\n    border: 1px solid #ccc;\n    border-radius: 8px;\n    margin: 10px;\n    padding: 15px 0;\n    cursor: pointer;\n}\n.initialsettingcontainer {\n    margin-top: 10px;\n    border: 1px solid #cccc;\n    padding: 10px;\n    border-radius: 8px;\n}\n\n\ninput[type=checkbox]{\n  height: 0;\n  width: 0;\n  visibility: hidden;\n}\n\nlabel {\n  cursor: pointer;\n  text-indent: -9999px;\n  width: 100px;\n  height: 50px;\n  background: grey;\n  display: block;\n  border-radius: 100px;\n  position: relative;\n  left: 45px;\n  top: 12px;\n}\n\nlabel:after {\n  content: '';\n  position: absolute;\n  top: 0px;\n  left: 0px;\n  width: 45px;\n  height: 45px;\n  background: #fff;\n  border-radius: 90px;\n  transition: 0.3s;\n}\n\ninput:checked + label {\n  background: red;\n}\n\ninput:checked + label:after {\n  left: calc(100% - 0px);\n  transform: translateX(-100%);\n}\n\nlabel:active:after {\n  width: 130px;\n}\n.section-title{\n  text-align: center;\n  padding: 15px;\n  font-size: 24px;  \n}\n.section-card:hover{\n  cursor: pointer;\n}\n.title-info{\n  position: absolute;\n  left: 28%;\n  font-weight: 500;\n}\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"blockcontent.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n          <div class=\"tile\">\n            <h4 class=\"tile-title\">\n              <a href=\"blockcontent.php\" class=\"btn btn-primary\">Back</a>\n              <span class=\"title-info\">\n                BLOCK CONTENT OF \n              <b style=\"text-decoration: underline;\">\n                ";
$PortalLINKGET = isset($PortalData["portallink"]) && !empty($PortalData["portallink"]) ? $PortalData["portallink"] : "";
echo $PortalLINKGET;
$FullBlocked = $CommonController->GetBlockedDataByPortalLInk($conn, $PortalLINKGET);
echo "              </b>\n              </span>\n\n            </h4>\n            <div class=\"tile-body\">\n              <div class=\"portalcontianers\">\n                <center><h2>Block Full Section</h2></center>\n                <div class=\"row\">\n                    ";
$SectionAre = ["Live" => "live", "Movies" => "movies", "Series" => "series", "Catch UP" => "catchup", "Radio" => "radio"];
echo "                    <div class=\"col-md-12\">\n                      ";
foreach ($SectionAre as $Skey => $Sval) {
    echo "                          <div class=\"user-status-td\">\n                            <div class=\"MainSections onoffswitch\">\n                                ";
    echo $Skey;
    echo "                                <input type=\"checkbox\" name=\"onoffswitch\" class=\"onoffswitch-checkbox blockfullsecbtn\" id=\"myonoffswitch-";
    echo $Skey;
    echo "\" data-currentis=\"";
    echo in_array($Sval, $FullBlocked) ? "1" : "0";
    echo "\" data-rowid=\"";
    echo $Sval;
    echo "\" data-nameselector=\"";
    echo $Skey;
    echo "\" ";
    echo in_array($Sval, $FullBlocked) ? "checked" : "";
    echo ">\n                                <label class=\"onoffswitch-label\" for=\"myonoffswitch-";
    echo $Skey;
    echo "\">\n                                </label>\n                            </div>\n                          </div>\n                        ";
}
echo "                    </div>\n                </div>\n              </div>\n              <div class=\"initialsettingcontainer\">\n                <center><h2>Explore Section</h2></center>\n                <div class=\"sectioncontainer\">\n                    <div class=\"row appendeddatahere\">\n                      <div class=\"col-md-1\"></div>\n                      ";
foreach ($SectionAre as $Skey => $Sval) {
    echo "                        <div class=\"col-md-2 exploersection-";
    echo $Sval;
    echo " ";
    echo in_array($Sval, $FullBlocked) ? "d-none" : "";
    echo "\">\n\t                        <a href=\"explore-block.php?p=";
    echo $_GET["p"];
    echo "&sec=";
    echo $Sval;
    echo "\">\t\n\t                          <div class=\"card section-card\">\n\t                            <div class=\"section-title\">";
    echo $Skey;
    echo "</div>                              \n\t                          </div>\n\t                        </a>\n                        </div>\n                        ";
}
echo "                    </div>\n                </div>\n              </div>\n            </div>\n          </div>\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n\n\n\n\n  \n";
$dispatcher->dispatch("mainfooter");
echo "<script type=\"text/javascript\">\n  \$(document).ready(function(){\n    \$(\".blockfullsecbtn\").click(function(e){\n      e.preventDefault();\n      thisVarIs = \$(this);\n      secttionis = \$(this).data(\"rowid\");\n      nameselector = \$(this).data(\"nameselector\");\n      currentis = \$(this).data(\"currentis\");\n      TextData = \"active\";\n      AjaxRetunTextData = \"Actived\";\n      if(currentis == 0)\n      {\n        TextData = \"block\";\n        AjaxRetunTextData = \"Blocked\";\n      }\n      Swal.fire({\n          title: 'Are you sure?',\n          html: \"You want to \"+TextData+\" full <b style='text-decoration:underline;font-weight: bold;'>\"+nameselector+\"</b> section!!\",\n          type: 'warning',\n          showCancelButton: true,\n          confirmButtonColor: '#3085d6',\n          cancelButtonColor: '#d33',\n          confirmButtonText: 'Yes, go for it!'\n        }).then((result) => {\n          if (result.value) {\n            swal.close();\n            Swal.fire({\n              text: \"Processing.....\",\n              allowOutsideClick: false,\n              showCancelButton: false,\n              showConfirmButton: false\n            });\n            jQuery.ajax({\n              type:\"POST\",\n              url:\"includes/ajax-control.php\",\n              dataType:\"text\",\n              data:{\n              action:'blockfullsection',\n              rowid:secttionis,\n              currentis:currentis,\n              portallink:'";
echo isset($PortalData["portallink"]) && !empty($PortalData["portallink"]) ? $PortalData["portallink"] : "";
echo "'\n              },  \n              success:function(response){\n                swal.close();\n                if(response != \"\")\n                {\n                  if(currentis == 0)\n                  {\n                      thisVarIs.data(\"currentis\",\"1\");\n                      \$(\".exploersection-\"+secttionis).addClass(\"d-none\");\n                      thisVarIs.prop(\"checked\",true);\n                  }\n                  else\n                  {\n                  \t  thisVarIs.data(\"currentis\",\"0\"); \t\n                      \$(\".exploersection-\"+secttionis).removeClass(\"d-none\");\n                      thisVarIs.prop(\"checked\",false);\n                  }\n                }\n              }\n            });            \n          }\n        })\n    });\n  });\n \n</script>\n";

?>