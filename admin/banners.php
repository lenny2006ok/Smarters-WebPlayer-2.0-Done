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
$paranset = ["title" => "ADD BANNERS", "activemenu" => "ADDBANNERS", "logovalue" => $LogoIs, "breadcrumblink" => "banners.php", "license" => $ValidLicense];
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
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\nbody {\n  background: #000;\n    font-family: sans-serif;\n}\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.portalcontianers {\n    text-align: center;\n}\n\n.showaddtestlinebutton\n{\n  text-decoration: underline;\n      color: #3085d6;\n}\n.showaddtestlinebutton:hover\n{\n  text-decoration: none;\n  color: #6c8cab;\n}\n/* simern css */\n.tile-title{\n    margin: 0;\n    font-size: 24px;\n    font-weight: 400;\n}\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"banners.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n          <div class=\"tile\">\n            <h3 class=\"tile-title\">PORTAL LINKS FOR ";
echo $paranset["title"];
echo "</h3>\n            <div class=\"tile-body\">\n              <div class=\"portalcontianers\">\n                ";
if (!empty($FportalLinks)) {
    foreach ($FportalLinks as $PortalLInkIS) {
        echo "                      <a href=\"#\" class=\"btn btn-primary openblocksection\" data-portallinkis=\"";
        echo $PortalLInkIS;
        echo "\">";
        echo $PortalLInkIS;
        echo "</a>\n                    ";
    }
}
echo "              </div>\n            </div>\n          </div>\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n\n\n\n\n  <!-- Modal -->\n  <div id=\"AddTestLineContainer\" class=\"modal fade\" role=\"dialog\"  data-backdrop=\"static\" data-keyboard=\"false\">\n    <div class=\"modal-dialog\">\n\n      <!-- Modal content-->\n      <div class=\"modal-content\">\n        <div class=\"modal-header\">\n          <h4 class=\"modal-title\">Add Test For Block Content</h4>\n          <button type=\"button\" class=\"close disablewhileajax\" data-dismiss=\"modal\">&times;</button>\n        </div>\n        <div class=\"modal-body\">\n          <div class=\"form-group\">\n            <label for=\"testlineusername\">Username:</label>\n            <input type=\"text\" class=\"form-control commoninput\" id=\"testlineusername\">\n          </div>\n          <div class=\"form-group\">\n            <label for=\"testlinepassword\">Password:</label>\n            <input type=\"password\" class=\"form-control commoninput\" id=\"testlinepassword\">\n          </div>\n        </div>\n        <div class=\"modal-footer\">\n          <button type=\"button\" class=\"btn btn-primary disablewhileajax\" id=\"testlinebtn\" data-portallinkdata=\"\">Test Details</button>\n          <button type=\"button\" class=\"btn btn-default disablewhileajax\" data-dismiss=\"modal\">Close</button>\n        </div>\n      </div>\n\n    </div>\n  </div>\n";
$dispatcher->dispatch("mainfooter");
echo "  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n      \$(\"#testlinebtn\").click(function(e){\n          \$(\".is-invalid\").removeClass(\"is-invalid\");\n          var portalis = \$(this).data(\"portallinkdata\");\n          var username = \$(\"#testlineusername\").val();\n          var password = \$(\"#testlinepassword\").val();\n          successcounter = 0;\n          if(username != \"\")\n          {\n              successcounter = Number(successcounter)+Number(1);\n          }\n          else\n          {\n              \$(\"#testlineusername\").addClass(\"is-invalid\");\n          }\n          if(password != \"\")\n          {\n              successcounter = Number(successcounter)+Number(1);\n          }\n          else\n          {\n              \$(\"#testlinepassword\").addClass(\"is-invalid\");\n          }\n          if(successcounter >= 2)\n          {\n              \$(\".disablewhileajax\").prop(\"disabled\",true);\n              \$(\"#testlinebtn\").text(\"Checking....\");\n              jQuery.ajax({                   \n              type:\"POST\",              \n              url:\"includes/ajax-control.php\", \n              dataType:\"text\", \n              data :{\n                  action:'checktestlineforblockconetent',\n                  portalis:portalis,\n                  username:username,\n                  password:password\n              },\n              success:function(response2){\n                \$(\".disablewhileajax\").prop(\"disabled\",false);\n                \$(\"#testlinebtn\").text(\"Test Details\");\n                \$(\"#AddTestLineContainer\").modal(\"hide\");\n                var obj = JSON.parse(response2);\n                if(obj.result == \"success\")\n                {\n                    window.location.href = 'banners-list.php?p='+obj.insertid;\n                }\n                else\n                {\n                    Swal.fire({\n                      title: obj.message,\n                      type: 'error',\n                      showCancelButton: false,\n                    });\n                }\n              } \n            });\n          }\n\n      });\n\n\n      \$(\".openblocksection\").click(function(e){\n        e.preventDefault();\n        var portvalue = \$(this).data(\"portallinkis\");\n        var thisselector = \$(this);\n        thisselector.prop(\"disabled\",true);\n        jQuery.ajax({                   \n            type:\"POST\",              \n            url:\"includes/ajax-control.php\", \n            dataType:\"text\", \n            data :{\n                action:'checkportallinkwithtestline',\n                portvalue:portvalue,\n                checkfor:'addbanner'\n            },\n            success:function(response2){\n              thisselector.prop(\"disabled\",false);\n              var obj = JSON.parse(response2);\n              if(obj.result == \"success\")\n              {\n                  Swal.fire({\n                    title: obj.message,\n                    type: 'success',\n                    html:\n                    'Click <a href=\"#\" class=\"showaddtestlinebutton\">Here</a> to change details for add bannres',                 \n                    showCancelButton: true,\n                    confirmButtonColor: '#3085d6',\n                    cancelButtonColor: '#d33',\n                    confirmButtonText: 'List Content'\n                  }).then((result) => {\n                      if (result.value) {\n                       window.location.href = 'banners-list.php?p='+obj.insertid;\n                     }\n                  })\n                  \$(\".showaddtestlinebutton\").bind(\"click\", function(p){\n                      p.preventDefault();\n                      swal.close();\n                      \$(\"#testlinebtn\").data(\"portallinkdata\",portvalue);\n                      \$(\"#AddTestLineContainer\").modal(\"show\");\n                   });\n              }\n              else\n              {\n\n                Swal.fire({\n                  title: obj.message,\n                  type: 'warning',\n                  showCancelButton: true,\n                  confirmButtonColor: '#3085d6',\n                  cancelButtonColor: '#d33',\n                  confirmButtonText: 'Add Test Line'\n                }).then((result) => {\n                  if (result.value) {\n                      swal.close()\n                      \$(\"#testlinebtn\").data(\"portallinkdata\",portvalue);\n                      \$(\"#AddTestLineContainer\").modal(\"show\");\n                  }\n                })\n              }\n            } \n          });\n      });\n\n       \$(\".commoninput\").click(function(p){\n          \$(this).removeClass(\"is-invalid\");\n      });\n    });\n   \n  </script>\n";

?>