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
$titleis = "Change PW";
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
$paranset = ["title" => "Change PW", "activemenu" => "CHANGEPASSWORD", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-key\"></i> Change Password</h1>\n          <p>Change Password</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"changepassword.php\">Change Password</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-3\">\n        </div>\n        <div class=\"col-md-6\">\n          <div class=\"tile\">\n            <h3 class=\"tile-title\">Change Password Form</h3>\n            <div class=\"tile-body\">\n              <div class=\"form-group\">\n                <label class=\"control-label\">Current Password</label><i class=\"fa fa-eye-slash eyecheck\" data-current=\"show\"  data-secshow=\"currentpassword\" aria-hidden=\"true\"></i>\n                <input class=\"form-control commoninput\" type=\"password\" id=\"currentpassword\" placeholder=\"Enter Current Password\">\n              </div>\n              <div class=\"form-group\">\n                <label class=\"control-label\">New Password</label><i class=\"fa fa-eye-slash eyecheck\" data-current=\"show\" data-secshow=\"newpassword\" aria-hidden=\"true\"></i>\n                <input class=\"form-control commoninput\" type=\"password\" id=\"newpassword\" placeholder=\"Enter New Password\">\n              </div>\n              <div class=\"form-group\">\n                  <div class=\"row\">\n                      <div class=\"col-md-4\">\n                          Password Strength:\n                      </div>\n                      <div class=\"col-md-8\">\n                        <div class=\"col-md-12\">\n                          <div class=\"progress\">\n                            <div class=\"progress-bar progress-bar-danger\" role=\"progressbar\" aria-valuenow=\"0\"\n                            aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:0%\">\n                            </div>\n                          </div>\n                        </div>\n                      </div>\n                  </div>\n              </div>\n              <div class=\"form-group\">\n                <label class=\"control-label\">Confirm New Password</label><i class=\"fa fa-eye-slash eyecheck\" data-current=\"show\" data-secshow=\"confirmnewpassword\" aria-hidden=\"true\"></i>\n                <input class=\"form-control commoninput\" type=\"password\" id=\"confirmnewpassword\" placeholder=\"Enter Confirm New Password\">\n              </div>\n            </div>\n            <div class=\"tile-footer\">\n             <a class=\"btn btn-primary\" id=\"changepassword\" href=\"#\">Save Changes</a>\n            </div>\n          </div>\n        </div>\n        <div class=\"col-md-3\">\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n      \$(\".eyecheck\").click(function(p){\n          idsellector = \$(this).data(\"secshow\");\n          if(\$(this).data(\"current\") == \"show\")\n          {\n            \$(this).removeClass(\"fa-eye-slash\");\n            \$(this).addClass(\"fa-eye\");\n            \$(this).data(\"current\",\"hide\")\n            \$(\"#\"+idsellector).attr('type','text');\n          }\n          else\n          {\n            \$(this).removeClass(\"fa-eye\");\n            \$(this).addClass(\"fa-eye-slash\");\n            \$(this).data(\"current\",\"show\");\n            \$(\"#\"+idsellector).attr('type','password');\n          }          \n      });\n\n\n      \$(\".commoninput\").click(function(p){\n          \$(this).removeClass(\"is-invalid\");\n      });\n      \$(\"#changepassword\").click(function(p){\n        p.preventDefault();\n        sucesscounter = 3;\n         \$( \".commoninput\" ).each(function( i ) {\n            if(\$(this).val() == \"\")\n            {\n                sucesscounter = Number(sucesscounter)-Number(1);   \n                \$(this).addClass(\"is-invalid\");\n            }\n         });\n         if(sucesscounter == \"3\")\n         {\n            currentpassword = \$(\"#currentpassword\").val();\n            newpassword = \$(\"#newpassword\").val();\n            confirmnewpassword = \$(\"#confirmnewpassword\").val();\n            if(newpassword == confirmnewpassword)\n            {\n              \$(\".is-invalid\").removeClass(\"is-invalid\");\n              jQuery.ajax({\n                type:\"POST\",\n                url:\"includes/ajax-control.php\",\n                dataType:\"text\",\n                data:{\n                action:'checkcurrentpassword',\n                currentpassword:currentpassword,\n                newpassword:newpassword,\n                confirmnewpassword:confirmnewpassword\n                },  \n                  success:function(response2){\n                     var responseObj = jQuery.parseJSON(response2);\n                     console.log(responseObj);\n                     if(responseObj.result == \"success\")\n                      {\n                         Swal.fire({\n                            position: 'center',\n                            title: responseObj.message,\n                            type: 'success',\n                            showCancelButton: false,\n                            confirmButtonColor: '#3085d6',\n                            confirmButtonText: 'Okay'\n                          }).then((result) => {\n                            if (result.value) {\n                                 window.location.href =  'dashboard.php';\n                            }\n                          })\n                      }\n                      else\n                      {\n                          if(responseObj.message == \"\")\n                          {\n                              responseObj.message = \"Invalid Details\";\n                          }\n                          \n                          Swal.fire({\n                                position: 'center',\n                                type: 'error',\n                                title: responseObj.message,\n                                showConfirmButton: false,\n                                timer: 1500\n                              })\n                      }\n                  }\n                }); \n            }\n            else\n            {\n                \$(\"#newpassword\").addClass(\"is-invalid\");\n                \$(\"#confirmnewpassword\").addClass(\"is-invalid\");\n            }\n         }\n      });\n        \n\n        \$( \"#newpassword\" ).keyup(function() {\n            GetStreenGTH = checkStrength(\$(this).val());\n            textshow = \"\";\n            percentage = \"\";\n            barclassis = \"\";\n            if(GetStreenGTH == \"25\")\n            {\n                textshow = \"Too short\"\n                percentage = GetStreenGTH;\n                barclassis = \"progress-bar-danger\";\n            }\n            else if(GetStreenGTH == \"40\")\n            {\n                textshow = \"Weak\"\n                percentage = GetStreenGTH;\n                barclassis = \"progress-bar-warning\";\n            }\n            else if(GetStreenGTH == \"70\")\n            {\n                textshow = \"GOOD\"\n                percentage = GetStreenGTH;\n                barclassis = \"progress-bar-info\";\n            }\n            else if(GetStreenGTH == \"100\")\n            {\n                textshow = \"Strong\"\n                percentage = GetStreenGTH;\n                barclassis = \"progress-bar-success\";\n            }\n\n\n            \$(\".progress-bar\").removeClass(\"progress-bar-success progress-bar-info progress-bar-warning progress-bar-danger\");\n            \$(\".progress-bar\").addClass(barclassis);\n            \$(\".progress-bar\").text(textshow);\n            \$(\".progress-bar\").attr('aria-valuenow', percentage);\n            \$(\".progress-bar\").css('width', percentage+\"%\");\n        });\n\n    });\n\n    function checkStrength(password) {\n        var strength = 0\n        if (password.length < 6) {\n        \$('#result').removeClass()\n        \$('#result').addClass('short')\n        return '25'\n        }\n        if (password.length > 7) strength += 1\n        // If password contains both lower and uppercase characters, increase strength value.\n        if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1\n        // If it has numbers and characters, increase strength value.\n        if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1\n        // If it has one special character, increase strength value.\n        if (password.match(/([!,%,&,@,#,\$,^,*,?,_,~])/)) strength += 1\n        // If it has two special characters, increase strength value.\n        if (password.match(/(.*[!,%,&,@,#,\$,^,*,?,_,~].*[!,%,&,@,#,\$,^,*,?,_,~])/)) strength += 1\n        // Calculated strength value, we can return messages\n        // If value is less than 2\n        if (strength < 2) {\n        \$('#result').removeClass()\n        \$('#result').addClass('weak')\n        return '40'\n        } else if (strength == 2) {\n        \$('#result').removeClass()\n        \$('#result').addClass('good')\n        return '70'\n        } else {\n        \$('#result').removeClass()\n        \$('#result').addClass('strong')\n        return '100'\n        }\n      }\n  </script>";

?>