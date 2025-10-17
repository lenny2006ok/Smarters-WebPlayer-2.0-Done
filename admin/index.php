<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("LOGININDEXPAGEDIRECTORYPATH", dirname(dirname(__FILE__)) . "/");
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "connection.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "connection.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/AdminContoller.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/AdminContoller.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/Controller.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Admin/Controller.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "admin/includes/functions.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "admin/includes/functions.php";
}
if (file_exists(LOGININDEXPAGEDIRECTORYPATH . "lib/Common/CommonController.php")) {
    include_once LOGININDEXPAGEDIRECTORYPATH . "lib/Common/CommonController.php";
}
$DatabaseObj = new DBConnect();
$conn = $DatabaseObj->makeconnection();
if (array_key_exists("dberror", $conn)) {
    echo "<script>window.location.href = '../dbconfiguration.php';</script>";
    exit;
}
$CommonController = new CommonController();
$checkblockedip = $CommonController->checkblockedip($conn);
if ($checkblockedip == "0") {
    echo "<script>window.location.href = 'blocked.php';</script>";
    exit;
}
$dispatcher = new AdminContoller();
$checkadminlogin = $dispatcher->dispatch("checkadminlogin");
if ($checkadminlogin == "1") {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
if (file_exists("../dbconfiguration.php")) {
    unlink("../dbconfiguration.php");
}
$dispatcher->dispatch("loginheader");
$dispatcher->dispatch("createallrecommendedtables", $conn);
$controlfunctions = new controlfunctions();
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$gasecret = isset($ConfigDetails["gasecret"]) && !empty($ConfigDetails["gasecret"]) ? $controlfunctions->webtvtheme_decrypt($ConfigDetails["gasecret"]) : "";
$recaptchasitekey = isset($ConfigDetails["recaptchasitekey"]) && !empty($ConfigDetails["recaptchasitekey"]) ? $ConfigDetails["recaptchasitekey"] : "";
$recptchasecret = isset($ConfigDetails["recptchasecret"]) && !empty($ConfigDetails["recptchasecret"]) ? $ConfigDetails["recptchasecret"] : "";
$captcha = isset($ConfigDetails["captcha"]) && $ConfigDetails["captcha"] == "on" ? "on" : "";
$twofa = isset($ConfigDetails["twofa"]) && $ConfigDetails["twofa"] == "on" ? "on" : "";
$defaultusername = isset($_COOKIE["adminusername"]) && !empty($_COOKIE["adminusername"]) ? $controlfunctions->webtvtheme_decrypt($_COOKIE["adminusername"]) : "";
$defaultpassword = isset($_COOKIE["adminuserpassword"]) && !empty($_COOKIE["adminuserpassword"]) ? $controlfunctions->webtvtheme_decrypt($_COOKIE["adminuserpassword"]) : "";
echo "<style type=\"text/css\">\n  .addrequiredborder\n  {\n    border:2px solid red;\n  }\n  .d-none\n  {\n    display: none !important;\n  }\n  .adminlogo img{\n    width: 10rem;\n  }\n  ";
if ($captcha == "on") {
    echo "         .login-content .login-box\n         {\n          min-height: 450px;\n              min-width: 390px;\n         }\n         ";
}
echo "\n</style>\n<section class=\"material-half-bg\">\n  <div class=\"cover\"></div>\n</section>\n<section class=\"login-content\">\n  <div class=\"logo adminlogo\">\n    <!-- <img src=\"";
echo $ConfigDetails["logo"];
echo "\" alt=\"\"/> -->\n  </div>\n  <div class=\"login-box\">\n    <form class=\"login-form\" action=\"index.html\">\n      <h3 class=\"login-head\"><i class=\"fa fa-lg fa-fw fa-user\"></i>SIGN IN</h3>\n      <div class=\"form-group\">\n        <label class=\"control-label\">USERNAME *</label>\n        <input class=\"form-control\" type=\"text\" id=\"usernameselector\" placeholder=\"Username\" value=\"";
echo $defaultusername;
echo "\" autofocus>\n      </div>\n      <div class=\"form-group\">\n        <label class=\"control-label\">PASSWORD *</label>\n        <input class=\"form-control\" type=\"password\" id=\"passwordselector\" value=\"";
echo $defaultpassword;
echo "\" placeholder=\"Password\">\n      </div>\n      ";
if ($captcha == "on") {
    echo "         <script src='https://www.google.com/recaptcha/api.js' async defer ></script>\n         <div class=\"g-recaptcha\" data-sitekey=\"";
    echo $recaptchasitekey;
    echo "\"></div>\n\n         ";
}
echo "      <div class=\"form-group\">\n        <div class=\"utility\">\n          <div class=\"animated-checkbox\">\n            <label>\n              <input type=\"checkbox\" id=\"rememberme\"><span class=\"label-text\">Stay Signed in</span>\n            </label>\n          </div>\n        </div>\n      </div>\n      <div class=\"form-group btn-container\">\n        <button class=\"btn btn-primary btn-block loginprogress\"><i class=\"fa fa-sign-in fa-lg fa-fw\"></i>SIGN IN</button>\n      </div>\n    </form>\n  </div>\n</section>\n\n<script type=\"text/javascript\">\n  logoutmessage = localStorage.getItem(\"logoutmessage\");\n  if(logoutmessage == \"yes\")\n  {\n    Swal.fire({\n      position: 'center',\n      type: 'success',\n      title: 'Logout Successfully!',\n      showConfirmButton: false,\n      timer: 1500\n    })\n    localStorage.removeItem('logoutmessage');\n  }\n\n  \n  timeout = localStorage.getItem(\"timeout\");\n  if(timeout == \"yes\")\n  {\n    Swal.fire({\n      position: 'center',\n      type: 'success',\n      title: 'You have been logged out of the session due to inactivity. Kindly login again to continue.!!',\n      showConfirmButton: true\n    })\n    localStorage.removeItem('timeout');\n  } \n</script>\n";
$dispatcher->dispatch("loginfooter");
$dispatcher->dispatch("loginscript");

?>