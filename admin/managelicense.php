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
$titleis = "Manage License";
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
$paranset = ["title" => "Manage License", "activemenu" => "MANAGELICENSE", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"managelicense.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"col-md-10\">\n          <div class=\"tile\">\n            <h3 class=\"tile-title\">";
echo $paranset["title"];
echo " Form</h3>\n            <div class=\"tile-body\">\n              <div class=\"form-group\">\n                <div class=\"row\">\n                  <div class=\"col-md-2\">\n                    <label class=\"control-label\">Your License</label>\n                  </div>\n                  <div class=\"col-md-10\">\n                    <input class=\"form-control commoninput\" type=\"text\" id=\"licenseselector\" placeholder=\"Your License\" value=\"";
echo $LicenseIS;
echo "\">\n                  </div>\n                </div>\n              </div>\n              \t";
if ($paranset["license"] == "Active") {
    echo "\t            \t<div class=\"form-group\">\n\t\t                <div class=\"row\">\n\t\t                  <div class=\"col-md-2\">\n\t\t                    <label class=\"control-label\">Status</label>\n\t\t                  </div>\n\t\t                  <div class=\"col-md-10\">\n\t\t                    <input class=\"form-control \" type=\"text\" id=\"\" placeholder=\"Registered Email\" value=\"";
    echo $checkLicense["status"];
    echo "\" readonly>\n\t\t                  </div>\n\t\t                </div>\n\t\t             </div>\n\t            \t\t            \t<div class=\"form-group\">\n\t\t                <div class=\"row\">\n\t\t                  <div class=\"col-md-2\">\n\t\t                    <label class=\"control-label\">Registered Email</label>\n\t\t                  </div>\n\t\t                  <div class=\"col-md-10\">\n\t\t                    <input class=\"form-control \" type=\"text\" id=\"\" placeholder=\"Registered Email\" value=\"";
    echo $checkLicense["email"];
    echo "\" readonly>\n\t\t                  </div>\n\t\t                </div>\n\t\t             </div>\n\t            \t\t            \t<div class=\"form-group\">\n\t\t                <div class=\"row\">\n\t\t                  <div class=\"col-md-2\">\n\t\t                    <label class=\"control-label\">Registered on</label>\n\t\t                  </div>\n\t\t                  <div class=\"col-md-10\">\n\t\t                    <input class=\"form-control \" type=\"text\" id=\"\" placeholder=\"Registered Email\" value=\"";
    echo date("l, d F Y", strtotime($checkLicense["regdate"]));
    echo "\" readonly>\n\t\t                  </div>\n\t\t                </div>\n\t\t             </div>\n\t            \t";
    $ExpiredON = "UNLIMITED";
    if ($checkLicense["nextduedate"] != "0000-00-00") {
        $ExpiredON = date("l, d F Y", strtotime($checkLicense["nextduedate"]));
    }
    echo "\t            \t<div class=\"form-group\">\n\t\t                <div class=\"row\">\n\t\t                  <div class=\"col-md-2\">\n\t\t                    <label class=\"control-label\">Expired on</label>\n\t\t                  </div>\n\t\t                  <div class=\"col-md-10\">\n\t\t                    <input class=\"form-control \" type=\"text\" id=\"\" placeholder=\"Registered Email\" value=\"";
    echo $ExpiredON;
    echo "\" readonly>\n\t\t                  </div>\n\t\t                </div>\n\t\t             </div>\n\t            \t";
}
echo "            </div>\n            <div class=\"tile-footer\">\n                <button class=\"btn btn-primary\" id=\"checklicense\" data-actionis=\"check\">Check License</button>\n            </div>\n          </div>\n        </div>\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n       \$(\"#licenseselector\").click(function(){\n            \$(this).removeClass(\"is-invalid\");\n       });\n\n\n       \$(\"#checklicense\").click(function(w){\n          w.preventDefault();\n          \$(\"#licenseselector\").removeClass(\"is-invalid\");\n\n          licenseval = \$(\"#licenseselector\").val();\n          actionis = \$(this).data(\"actionis\");\n          if(actionis == \"check\")\n          {\n             if(licenseval != \"\")\n            {\n               \$(\"#licenseselector\").prop(\"readonly\",true);\n               \$(\"#checklicense\").prop(\"disabled\",true);\n               \$(\"#checklicense\").text(\"Checking..\");\n              jQuery.ajax({\n                type:\"POST\",\n                url:\"includes/ajax-control.php\",\n                dataType:\"text\",\n                data:{\n                action:'checkvalidlicense',\n                licenseval:licenseval\n                },  \n                  success:function(response2){\n                     var responseObj = jQuery.parseJSON(response2);\n                     if(responseObj.result == \"success\")\n                     {                      \n                         Swal.fire({\n                            position: 'center',\n                            title: responseObj.message,\n                            type: 'success',\n                            showCancelButton: false,\n                            confirmButtonColor: '#3085d6',\n                            confirmButtonText: 'Okay'\n                          }).then((result) => {\n                            if (result.value) {\n                                 window.location.href =  'managelicense.php';\n                            }\n                          });                      \n                     }\n                     else\n                     {\n                        if(responseObj.message == \"\")\n                        {\n                            responseObj.message = \"Invalid License\";\n                        }\n                        \n                        Swal.fire({\n                              position: 'center',\n                              type: 'error',\n                              title: responseObj.message,\n                              showConfirmButton: false,\n                              timer: 1500\n                            });\n                     }   \n                      \$(\"#licenseselector\").prop(\"readonly\",false); \n                      \$(\"#checklicense\").prop(\"disabled\",false);\n                      \$(\"#checklicense\").text(\"Check License\");\n                  }\n                }); \n            }\n            else\n            {\n              \$(\"#licenseselector\").addClass(\"is-invalid\");\n            }\n          }\n          else\n          {\n            alert(\"changelicense works here\");\n          }\n       });\n      //alert(\"This is just for the testing..\");\n    });\n\n  </script>";

?>