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
$paranset = ["title" => "UNBLOCK IP's", "activemenu" => "UNBLOCKIP", "logovalue" => $LogoIs, "breadcrumblink" => "unblockip.php", "license" => $ValidLicense];
if (isset($_POST["blockedids"])) {
    $ExplodedIDS = explode(",", $_POST["blockedids"]);
    $DeleteBlockedIdsWithIdsArray = $CommonController->DeleteBlockedIdsWithIdsArray($ExplodedIDS, $conn);
    if ($DeleteBlockedIdsWithIdsArray == "1") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"deletesuccess\", \"yes\");\n        window.location.href = \"unblockip.php\";\n      </script>  \n      ";
        exit;
    }
}
$blockedlist = $CommonController->getblockedipsaddresslist($conn);
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"unblockip.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"col-md-10\">\n          <div class=\"tile\">\n            <div class=\"row\">\n              <div class=\"col-md-10\">\n                <h3 class=\"tile-title\">BLOCKED IP ADDRESSES LIST</h3>\n              </div>\n              <div class=\"col-md-2\">\n                  <form method=\"POST\" action=\"unblockip.php\" id=\"deleteblockeddata\">\n                    <input type=\"hidden\" name=\"blockedids\" id=\"blockedidscontianer\" value=\"\">\n                    <input type=\"submit\" name=\"deleteblocked\" class=\"btn btn-primary deletebtnsel\" value=\"DELETE\" disabled>\n                  </form>\n              </div>\n            </div>\n            ";
if (!empty($blockedlist)) {
    echo "              <table class=\"table\">\n                <thead>\n                  <tr>\n                    <th><input type=\"checkbox\" class=\"checkall\" value=\"\"> </th>\n                    <th>IP ADDRESS</th>\n                    <th>BANNED ON</th>\n                  </tr>\n                </thead>\n                <tbody>\n                  ";
    foreach ($blockedlist as $key) {
        echo "                    <tr>\n                      <td>\n                        <input type=\"checkbox\" class=\"checkboxcollector\" value=\"";
        echo $key["id"];
        echo "\"> \n                      </td>\n                      <td>";
        echo $key["ipaddress"];
        echo "</td>\n                      <td>";
        echo date("l, d F Y", strtotime($key["created_on"]));
        echo "</td>\n\n                    </tr>\n                    ";
    }
    echo "                </tbody>\n              </table>\n              ";
} else {
    echo "                <center><h4>No Result Found!!</h4></center>\n                ";
}
echo "          </div>\n        </div>\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n\n      deletesuccess = localStorage.getItem(\"deletesuccess\");\n      if(deletesuccess == \"yes\")\n      {\n        Swal.fire({\n          position: 'center',\n          type: 'success',\n          title: 'Records Deleted Successfully!',\n          showConfirmButton: false,\n          timer: 1500\n        })\n        localStorage.removeItem('deletesuccess');\n      }\n\n       \$(\".checkboxcollector\").click(function(w){\n          addcheckboxes();\n          buttonvisibility();\n        });\n       \$(\".checkall\").click(function(w){\n          if(this.checked) {\n            \$(\".checkboxcollector\").prop(\"checked\",true);\n          }\n          else\n          {\n            \$(\".checkboxcollector\").prop(\"checked\",false);\n          }\n          addcheckboxes();\n          buttonvisibility();         \n       });\n      //alert(\"This is just for the testing..\");\n    });\n    function buttonvisibility()\n    {\n        \$(\".deletebtnsel\").prop(\"disabled\",true);\n        if(\$('.checkboxcollector:checked').length > 0)\n        {\n          \$(\".deletebtnsel\").prop(\"disabled\",false);\n        }\n    }\n\n    function addcheckboxes()\n    {\n      var ids = [];\n      \$('.checkboxcollector:checked').each(function( index ) {\n         ids.push(\$( this ).val());\n      });\n      \$(\"#blockedidscontianer\").val(ids);\n\n    }\n  </script>";

?>