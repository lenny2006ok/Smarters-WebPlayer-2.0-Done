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
if (file_exists(ADMINFILESDIRECTORY . "lib/twoFaLib.php")) {
    include_once ADMINFILESDIRECTORY . "lib/twoFaLib.php";
}
$DatabaseObj = new DBConnect();
$controlfunctions = new controlfunctions();
$conn = $DatabaseObj->makeconnection();
$CommonController = new CommonController();
$googleauthenticator = new GoogleAuthenticator();
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
$titleis = "Configuration Settings";
$dispatcher = new AdminContoller();
$dispatcher->dispatch("createallrecommendedtables", $conn);
$checkadminlogin = $dispatcher->dispatch("checkadminlogin");
if ($checkadminlogin != "1") {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$CommonController->addActivityOnload($conn);
if (isset($_POST["adminemail"]) && !empty($_POST["adminemail"])) {
    $currentPassword = $controlfunctions->webtvtheme_Saveadminemailaddress($_POST, $conn);
    if (isset($currentPassword["result"]) && $currentPassword["result"] == "success") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"configuration\", \"success\");\n        window.location.href = \"configuration.php?emailsettings\";\n      </script>\n      ";
    } else {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"configuration\", \"error\");\n        window.location.href = \"configuration.php?emailsettings\";\n      </script>\n      ";
    }
    exit;
}
if (isset($_POST["portallinks"])) {
    $currentPassword = $controlfunctions->webtvtheme_Saveconfigdetails($_POST, $conn);
    if (isset($currentPassword["result"]) && $currentPassword["result"] == "success") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"configuration\", \"success\");\n        window.location.href = \"configuration.php\";\n      </script>\n      ";
    } else {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"configuration\", \"error\");\n        window.location.href = \"configuration.php\";\n      </script>\n      ";
    }
    exit;
}
if (isset($_POST["savesecurity"])) {
    unset($_POST["savesecurity"]);
    if (!empty($_POST)) {
        $_POST["captcha"] = isset($_POST["captcha"]) && $_POST["captcha"] == "on" ? "on" : "";
        if (isset($_POST["twofa"]) && $_POST["twofa"] == "on") {
            $checkfromdb = isset($ConfigDetails["gasecret"]) && !empty($ConfigDetails["gasecret"]) ? "y" : "n";
            if ($checkfromdb == "n") {
                $_POST["gasecret"] = $controlfunctions->webtvtheme_encrypt($googleauthenticator->createSecret());
            }
        }
        $_POST["twofa"] = isset($_POST["twofa"]) && $_POST["twofa"] == "on" ? "on" : "";
        $currentPassword = $controlfunctions->webtvtheme_SaveSecuritySettings($_POST, $conn);
        if (isset($currentPassword["result"]) && $currentPassword["result"] == "success") {
            echo "            <script type=\"text/javascript\">\n              localStorage.setItem(\"securitysavelocal\", \"success\");\n              window.location.href = \"configuration.php?security\";\n            </script>\n            ";
        }
    } else {
        echo "        <script type=\"text/javascript\">\n          localStorage.setItem(\"securitysavelocal\", \"error\");\n          window.location.href = \"configuration.php?security\";\n        </script>\n        ";
    }
}
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "../images/blackdemo-Logo.jpg";
$LogoIstwo = isset($ConfigDetails["logo2"]) && $ConfigDetails["logo2"] != "" ? $ConfigDetails["logo2"] : "../images/blackdemo-Logo.jpg";
$SitetileIs = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$portalslinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$storedemail = isset($ConfigDetails["adminemail"]) && $ConfigDetails["adminemail"] != "" ? $ConfigDetails["adminemail"] : "";
$FportalLinks = [""];
if ($portalslinks != "") {
    $FportalLinks = unserialize($portalslinks);
}
$paranset = ["title" => "Configuration", "activemenu" => "CONFIGURATION", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
$showcurrent = "configuration";
if (isset($_GET["emailsettings"])) {
    $showcurrent = "emailsettings";
} else {
    if (isset($_GET["smtpsettings"])) {
        $showcurrent = "smtpsettings";
    } else {
        if (isset($_GET["security"])) {
            $showcurrent = "security";
        }
    }
}
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n  .nav-tabs .nav-item {\n    margin-bottom: -1px;\n    background: #e5e5e5;\n    border-left: 0.5px solid #ebc6c6;\n}\n\n.contentaligncenter img {\n      background: #222d32;\n}\n\n\n</style>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-cogs\"></i> Settings</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"configuration.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"col-md-10\">\n          <ul class=\"nav nav-tabs\" style=\"background: white;padding: 6px;border-bottom: none;\">\n            <li class=\"nav-item\">\n              <a class=\"nav-link ";
echo $showcurrent == "configuration" ? "active" : "";
echo "\"href=\"configuration.php\"> <i class=\"fa fa fa-cogs\" aria-hidden=\"true\"></i> Configuration</a>\n            </li>\n            <li class=\"nav-item\">\n              <a class=\"nav-link ";
echo $showcurrent == "emailsettings" ? "active" : "";
echo "\" href=\"configuration.php?emailsettings\"> <i class=\"fa fa-envelope\" aria-hidden=\"true\"></i> Email Settings</a>\n            </li>\n            <li class=\"nav-item\">\n              <a class=\"nav-link ";
echo $showcurrent == "smtpsettings" ? "active" : "";
echo "\" href=\"configuration.php?smtpsettings\"> <i class=\"fa fa-server\" aria-hidden=\"true\"></i> SMTP Settings</a>\n            </li>\n            <li class=\"nav-item\">\n              <a class=\"nav-link ";
echo $showcurrent == "security" ? "active" : "";
echo "\" href=\"configuration.php?security\"> <i class=\"fa fa-lock\" aria-hidden=\"true\"></i> Security</a>\n            </li>\n          </ul>\n          ";
if ($showcurrent == "configuration") {
    echo "  \n             <form method=\"POST\" action=\"configuration.php\" id=\"configurationform\"> \n\n                <div class=\"tile\">\n                  \n                  <h3 class=\"tile-title\">";
    echo $paranset["title"];
    echo "</h3>                  \n                  <div class=\"tile-body\">\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">Site title</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput\" type=\"text\" name=\"sitetitle\" id=\"sitetitleselector\" placeholder=\"Enter Site Title\" value=\"";
    echo $SitetileIs;
    echo "\">\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">your Logo</label>\n                        </div>\n                        <div class=\"col-md-6 contentaligncenter\" >\n                            <img src=\"";
    echo $LogoIs;
    echo "\" id=\"logoimgforadmin\" style=\"width: 100px;\">\n                        </div>\n                        <div class=\"col-md-4 contentalignright\" style=\"text-align: right;\">\n                            <button class=\"btn btn-primary showmediafiles\" data-filesfor=\"logo\">Select from media</button>\n                        </div>\n                        <input type=\"hidden\" name=\"logo\" id=\"logoinput\" value=\"";
    echo $LogoIs;
    echo "\">\n                      </div>\n                    </div>\n                    <!-- <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">Your 2nd Logo</label>\n                        </div>\n                        <div class=\"col-md-6 contentaligncenter\" >\n                            <img src=\"";
    echo $LogoIstwo;
    echo "\" id=\"logoimgforadmin2\" style=\"width: 100px;\">\n                        </div>\n                        <div class=\"col-md-4 contentalignright\" style=\"text-align: right;\">\n                            <button class=\"btn btn-primary showmediafiles\" data-filesfor=\"logo2\">Select from media</button>\n                        </div>\n                        <input type=\"hidden\" name=\"logo2\" id=\"logoinput2\" value=\"";
    echo $LogoIstwo;
    echo "\">\n                        <div class=\"col-md-12\" style=\"margin-top:5px;\">\n                          <div class=\"alert alert-warning\">\n                            <strong>Important Note!</strong> Your 2nd logo is only for the themes that support protheme features\n                          </div>\n                        </div>\n                      </div>\n                    </div> -->\n                    ";
    $index = 0;
    foreach ($FportalLinks as $identifire => $val) {
        echo "                      <div class=\"form-group\">\n                        <div class=\"row\">\n                          <div class=\"col-md-2\">\n                            <label class=\"control-label\">Portal URL / DNS</label>\n                          </div>\n                          <div class=\"col-md-4\">\n                            <input class=\"form-control commoninput\" name=\"portalidentifire[]\" type=\"text\" placeholder=\"Enter Portal Identifier\" value=\"";
        echo $identifire;
        echo "\">\n                          </div>\n                          <div class=\"col-md-4\">\n                            <input class=\"form-control commoninput\" name=\"portallinks[]\" type=\"text\" id=\"portallink-";
        echo $index;
        echo "\" placeholder=\"Enter Portal Link\" value=\"";
        echo $val;
        echo "\" ";
        echo !empty($val) ? "readonly" : "";
        echo ">\n                          </div>\n                          <div class=\"col-md-2 contentalignright\" style=\"text-align: right;\">\n                              <button class=\"btn btn-primary testportallink\" data-portof=\"portallink-";
        echo $index;
        echo "\" data-makeaction=\"";
        echo !empty($val) ? "edit" : "check";
        echo "\">";
        echo !empty($val) ? "Change DNS <i id='successgprocess' class='fa fa-check d-none'></i>" : "Test Link";
        echo "</button>\n                          </div>\n                        </div>\n                      </div>  \n                      ";
        $index++;
    }
    echo "                  </div>\n                  <div class=\"tile-footer\">\n                   <button class=\"btn btn-primary\" id=\"saveportaldetails\" type=\"submit\" >Save Changes</button>\n                  </div>\n                </div>\n                </form>\n            ";
} else {
    if ($showcurrent == "emailsettings") {
        echo "  \n             <form method=\"POST\" action=\"configuration.php?emailsettings\" id=\"formtosaveadmindetails\"> \n                <div class=\"tile\">                  \n                  <h3 class=\"tile-title\">Email Settings</h3>\n                  <div class=\"tile-body\">\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">Admin Email</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput\" type=\"email\" name=\"adminemail\" id=\"adminemailid\" placeholder=\"Admin Email\" value=\"";
        echo $storedemail;
        echo "\" required>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                  </div>\n                  <div class=\"tile-footer\">\n                   <button class=\"btn btn-primary\" id=\"saveemailsettings\" type=\"submit\" >Save Changes</button>\n                  </div>\n                </div>\n                </form>\n            ";
    } else {
        if ($showcurrent == "smtpsettings") {
            $smtpdetails = [];
            $smtpdetails["smtphost"] = isset($ConfigDetails["smtphost"]) && !empty($ConfigDetails["smtphost"]) ? $ConfigDetails["smtphost"] : "";
            $smtpdetails["smtpusername"] = isset($ConfigDetails["smtpusername"]) && !empty($ConfigDetails["smtpusername"]) ? $ConfigDetails["smtpusername"] : "";
            $smtpdetails["smtppassword"] = isset($ConfigDetails["smtppassword"]) && !empty($ConfigDetails["smtppassword"]) ? $controlfunctions->webtvtheme_decrypt($ConfigDetails["smtppassword"]) : "";
            $smtpdetails["smtyssltype"] = isset($ConfigDetails["smtyssltype"]) && !empty($ConfigDetails["smtyssltype"]) ? $ConfigDetails["smtyssltype"] : "";
            $smtpdetails["smtpport"] = isset($ConfigDetails["smtyssltype"]) && !empty($ConfigDetails["smtyssltype"]) ? $ConfigDetails["smtpport"] : "";
            $checksmtponnection = $CommonController->checkSMTPDetails($smtpdetails);
            echo "  \n                <div class=\"tile\">                  \n                  <h3 class=\"tile-title\">SMTP Settings</h3>\n                  ";
            if ($checksmtponnection == "Connected") {
                echo "                        <div class=\"alert alert-success\">\n                          <strong><i class=\"fa fa-check-circle\"></i> Success!</strong> SMTP connected successfully \n                        </div>\n                        ";
            } else {
                echo "                    <div class=\"alert alert-danger\">\n                      <strong><i class=\"fa fa-times-circle\"></i> Error!</strong> SMTP is not connected \n                    </div>\n                    ";
            }
            echo "                  <div class=\"tile-body\">\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">SMTP Host</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput smtpcommon\" type=\"text\" name=\"smtphost\" id=\"smtphost\" placeholder=\"SMTP Host\" value=\"";
            echo isset($ConfigDetails["smtphost"]) && !empty($ConfigDetails["smtphost"]) ? $ConfigDetails["smtphost"] : "";
            echo "\" required>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">SMTP Port</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput smtpcommon\" type=\"text\" name=\"smtpport\" id=\"smtpport\" placeholder=\"SMTP Port\" value=\"";
            echo isset($ConfigDetails["smtpport"]) && !empty($ConfigDetails["smtpport"]) ? $ConfigDetails["smtpport"] : "";
            echo "\" required>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">SMTP Username</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput smtpcommon\" type=\"text\" name=\"smtpusername\" id=\"smtpusername\" placeholder=\"SMTP Username\" value=\"";
            echo isset($ConfigDetails["smtpusername"]) && !empty($ConfigDetails["smtpusername"]) ? $ConfigDetails["smtpusername"] : "";
            echo "\" required>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">SMTP Password</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                          <input class=\"form-control commoninput smtpcommon\" type=\"password\" name=\"smtppassword\" id=\"smtppassword\" placeholder=\"SMTP Password\" value=\"";
            echo isset($ConfigDetails["smtppassword"]) && !empty($ConfigDetails["smtppassword"]) ? $controlfunctions->webtvtheme_decrypt($ConfigDetails["smtppassword"]) : "";
            echo "\" required>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <div class=\"row\">\n                        <div class=\"col-md-2\">\n                          <label class=\"control-label\">SMTP SSL Type</label>\n                        </div>\n                        <div class=\"col-md-6\">\n                            <select name=\"smtyssltype\" id=\"smtpssltype\" class=\"smtpcommon form-control\">\n                                <option value=\"tls\" ";
            echo isset($ConfigDetails["smtyssltype"]) && $ConfigDetails["smtyssltype"] == "tls" ? "selected" : "";
            echo ">TLS</option>\n                                <option value=\"ssl\" ";
            echo isset($ConfigDetails["smtyssltype"]) && $ConfigDetails["smtyssltype"] == "ssl" ? "selected" : "";
            echo ">SSL</option>\n                            </select>\n                        </div>\n                        <div class=\"col-md-4\">\n                        </div>\n                      </div>\n                    </div>\n                  </div>\n                  <div class=\"tile-footer\">\n                   <button class=\"btn btn-primary\" id=\"savesmtpandtest\" type=\"submit\" >Test & Save Changes</button>\n                  </div>\n                </div>\n            ";
        } else {
            if ($showcurrent == "security") {
                echo "             <div class=\"tile\">                  \n                  <h3 class=\"tile-title\">Security Settings</h3>\n                  <form method=\"POST\" action=\"configuration.php?security\"> \n                    <table class=\"table table-bordered\">\n                       <tbody>\n                          <tr>\n                             <td>Captcha</td>\n                             <td>\n                                <label><input type=\"checkbox\" name=\"captcha\" ";
                echo isset($ConfigDetails["captcha"]) && $ConfigDetails["captcha"] == "on" ? "checked" : "";
                echo " value=\"on\"> Enable/Disable Captcha</label>\n                             </td>\n                          </tr>\n                          <tr>\n                             <td>Google reCaptcha Sitekey</td>\n                             <td>\n                                <input type=\"text\" class=\"form-control\" name=\"recaptchasitekey\" value=\"";
                echo isset($ConfigDetails["recaptchasitekey"]) && !empty($ConfigDetails["recaptchasitekey"]) ? $ConfigDetails["recaptchasitekey"] : "";
                echo "\">\n                             </td>\n                          </tr>\n                          <tr>\n                             <td>Google reCaptcha Secret</td>\n                             <td>\n                                <input type=\"text\" class=\"form-control\" name=\"recptchasecret\" value=\"";
                echo isset($ConfigDetails["recptchasecret"]) && !empty($ConfigDetails["recptchasecret"]) ? $ConfigDetails["recptchasecret"] : "";
                echo "\">\n                             </td>\n                          </tr>\n                          <tr>\n                             <td>2 Factor Authentication</td>\n                             <td>\n                                <label>\n                                <input type=\"checkbox\" class=\"\" name=\"twofa\" ";
                echo isset($ConfigDetails["twofa"]) && $ConfigDetails["twofa"] == "on" ? "checked" : "";
                echo " value=\"on\"> Enable (Recommended)\n                                </label><br>\n                                ";
                if (isset($ConfigDetails["twofa"]) && $ConfigDetails["twofa"] == "on" && isset($ConfigDetails["gasecret"]) && !empty($ConfigDetails["gasecret"])) {
                    $storedGkey = $controlfunctions->webtvtheme_decrypt($ConfigDetails["gasecret"]);
                    $qrCodeUrl = $googleauthenticator->getQRCodeGoogleUrl($SitetileIs, $storedGkey);
                    echo "                                      <img src=\"";
                    echo $qrCodeUrl;
                    echo "\">\n                                      ";
                }
                echo "                             </td>\n\n                              <tr>\n                                 <td>Environment</td>\n                                 <td>\n                                    <select name=\"environment\" class=\"form-control\">\n                                        <option value=\"production\" ";
                echo isset($ConfigDetails["environment"]) && $ConfigDetails["environment"] == "production" ? "selected" : "";
                echo ">Production</option>\n                                        <option value=\"development\" ";
                echo isset($ConfigDetails["environment"]) && $ConfigDetails["environment"] == "development" ? "selected" : "";
                echo ">Development</option>\n                                    </select>\n                                 </td>\n                              </tr>\n                          </tr>\n                          <tr>\n                             <td colspan=\"2\">\n                                <input type=\"submit\" class=\"btn btn-primary\" value=\"Save Changes\" name=\"savesecurity\">\n                             </td>\n                          </tr>\n                       </tbody>\n                    </table>\n                  </form>\n                </div>\n             ";
            }
        }
    }
}
echo "        </div>\n        <div class=\"col-md-1\">\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n\n\n\n      \$(\"#savesmtpandtest\").click(function(e){\n        e.preventDefault();\n        \$(\"#savesmtpandtest\").text(\"Connecting...\");\n        \$(\"#savesmtpandtest\").prop(\"disabled\",true);\n        smtpcommonconditionallength  =  \$( \".smtpcommon\" ).length;\n        datatosend = {};\n        smtpvarsuccesscounter  = 0;\n        \$( \".smtpcommon\" ).each(function( index ) {\n              if(\$(this).val() != \"\")\n              {\n                smtpvarsuccesscounter = Number(smtpvarsuccesscounter)+Number(1);\n                datatosend[\$(this).attr(\"name\")] = \$(this).val();\n              }\n              else\n              {\n                \$(this).addClass(\"is-invalid\");\n              }\n          });\n\n        if(smtpvarsuccesscounter == smtpcommonconditionallength)\n          {\n              jQuery.ajax({                   \n                type:\"POST\",              \n                url:\"includes/ajax-control.php\", \n                dataType:\"text\", \n                data :{\n                    action:'checkandsavesmtp',\n                    datatosend:datatosend\n                },\n                success:function(response2){\n                  \$(\"#savesmtpandtest\").text(\"Test & Save Changes\");\n                  \$(\"#savesmtpandtest\").prop(\"disabled\",false);\n                  if(response2 == \"connected\")\n                  {\n                       Swal.fire({\n                        position: 'center',\n                        type: 'success',\n                        title: 'SMTP connected and details saved!!',\n                        showConfirmButton: false,\n                        timer: 1500\n                      })\n                       setTimeout(function(){ \n                        window.location.href = \"configuration.php?smtpsettings\";\n                       }, 1500);\n                       \n                  }\n                  else\n                  {\n                      Swal.fire({\n                        position: 'center',\n                        type: 'error',\n                        title: response2,\n                        showConfirmButton: false,\n                        timer: 1500\n                      })\n                  }\n                } \n              });\n          }\n      });\n\n\n\n\n      securitysavelocal = localStorage.getItem(\"securitysavelocal\");\n\n      if(securitysavelocal == \"error\")\n      {\n        Swal.fire({\n          position: 'center',\n          type: 'error',\n          title: 'Details Not Provided',\n          showConfirmButton: false,\n          timer: 1500\n        })\n        localStorage.removeItem('securitysavelocal');\n      }\n\n      if(securitysavelocal == \"success\")\n      {\n        Swal.fire({\n          position: 'center',\n          type: 'success',\n          title: 'Settings successfully saved!',\n          showConfirmButton: false,\n          timer: 1500\n        })\n        localStorage.removeItem('securitysavelocal');\n      }\n\n\n\n      logoutmessage = localStorage.getItem(\"configuration\");\n        if(logoutmessage == \"success\")\n        {\n          Swal.fire({\n            position: 'center',\n            type: 'success',\n            title: 'Changes successfully saved!',\n            showConfirmButton: false,\n            timer: 1500\n          })\n          localStorage.removeItem('configuration');\n        }\n        \n        if(logoutmessage == \"error\")\n        {\n          Swal.fire({\n            position: 'center',\n            type: 'error',\n            title: 'Unable to save please contact provider!',\n            showConfirmButton: false,\n            timer: 1500\n          })\n          localStorage.removeItem('configuration');\n        }\n\n      \$(\".testportallink\").click(function(e){\n          e.preventDefault();\n          thisvar = \$(this);\n           \$(\"#\"+portof).removeClass(\"is-invalid\");\n          var portof = \$(this).data(\"portof\");\n          var makeaction = \$(this).data(\"makeaction\");\n          if(makeaction == \"check\")\n          {\n            if(portof != \"\")\n            {\n                \n                portvalue = \$(\"#\"+portof).val();\n                if(portvalue != \"\")\n                {\n                  thisvar.text(\"Testing..\");\n                  thisvar.prop(\"disabled\",true);\n                  \$(\"#\"+portof).prop(\"readonly\",true);\n                    jQuery.ajax({                   \n                      type:\"POST\",              \n                      url:\"includes/ajax-control.php\", \n                      dataType:\"text\", \n                      data :{\n                          action:'checkportallink',\n                          portvalue:portvalue\n                      },\n                      success:function(response2){\n                        thisvar.text(\"Test Link\");\n                        thisvar.prop(\"disabled\",false);\n                        \$(\"#saveportaldetails\").prop(\"disabled\",false);\n                        var str1 = response2;\n                        var str2 = \"Access Denied\";\n                        var str3 = \"Xtream Codes Reborn\";\n                        if(str1.indexOf(str2) != -1 || str1.indexOf(str3) != -1 ){\n                          thisvar.data(\"makeaction\",\"edit\");\n                           thisvar.html(\"Valid DNS <i id='successgprocess' class='fa fa-check'></i>\");\n                          //\$('#offlinedatabtn').attr('disabled', false);\n                        }\n                        else\n                        {\n                          alert(\"It seems your portal is not valid. but if you are sure then can ignore and save configuration\");\n                           \$(\"#\"+portof).prop(\"readonly\",false);\n                        }\n                      } \n                    });\n                }\n                else\n                {\n                    \$(\"#\"+portof).addClass(\"is-invalid\");\n                     \$(\"#\"+portof).prop(\"readonly\",false);\n                }\n            }\n          }\n          else\n          {\n             thisvar.html(\"Test Link\");\n             thisvar.data(\"makeaction\",\"check\");\n             \$(\"#\"+portof).prop(\"readonly\",false);\n             /*\$(\"#saveportaldetails\").prop(\"disabled\",true);*/\n          }\n        });  \n\n\n        \$(\"#saveportaldetails\").click(function(e){\n            e.preventDefault();\n            \$( \".commoninput\" ).removeClass(\"is-invalid\");\n            conditionallength  =  \$( \".commoninput\" ).length;\n            varsuccesscounter  = 0;\n            \$( \".commoninput\" ).each(function( index ) {\n                if(\$(this).val() != \"\")\n                {\n                  varsuccesscounter = Number(varsuccesscounter)+Number(1);\n                }\n                else\n                {\n                  \$(this).addClass(\"is-invalid\");\n                }\n            });\n            if(varsuccesscounter == conditionallength)\n            {\n               \$(\"#configurationform\").submit();\n            }\n        });\n    });\n\n  </script>";

?>