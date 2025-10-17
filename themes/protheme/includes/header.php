<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */
 
ob_start();
session_start();
$currentenvoirment = isset($currentenvoirment) && !empty($currentenvoirment) ? $currentenvoirment : "production";
$currentwebtvplayerversion = isset($currentwebtvplayerversion) && !empty($currentwebtvplayerversion) ? $currentwebtvplayerversion : "1.6";
$rander = rand(0, 9999);
echo "<html lang=\"en\">\n<head>\n    <meta charset=\"utf-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">\n    <meta name=\"viewport\" content=\"width=device-width, user-scalable=0, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0\" />\n    <!-- <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">\n    <meta name=\"mobile-web-app-capable\" content=\"yes\"> -->\n    <title>";
echo isset($XCsitetitleval) && !empty($XCsitetitleval) ? $XCsitetitleval : "WebTV Player";
echo "</title>\n    <link rel=\"shortcut icon\" href=\"";
echo !empty($logovalue) ? $logovalue : "Favicon.ico";
echo "\"/>\n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/style.css?v=";
echo $rander;
echo "\">\n    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css\">\n    <!-- CSS only -->\n    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC\" crossorigin=\"anonymous\">\n    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM\" crossorigin=\"anonymous\"></script>\n    <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js\" integrity=\"sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p\" crossorigin=\"anonymous\"></script>\n    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js\" integrity=\"sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF\" crossorigin=\"anonymous\"></script>\n    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\" type=\"text/javascript\"></script>\n    <script src=\"//cdn.jsdelivr.net/npm/sweetalert2@11\"></script>\n    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.20/sweetalert2.min.js\" integrity=\"sha512-KIRtgwO59gNBBB6xsSD53HJ2zXW0PV9aRw4cIR33lTreCLhsjA3RgUwPAWOAYjZ+70olt9+jEdSayO3kNyamVg==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>\n    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.20/sweetalert2.all.min.js\" integrity=\"sha512-LQTHxCMBTyQqw1exya4NgYQ7yf4k88KusIUXqfd8+R9fQtlBwdJ15BivuxjfduNsk2tdLGmNKaN2lk5fTQtK3Q==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>\n    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.20/sweetalert2.css\" integrity=\"sha512-8543GQ3j5GD6UFe+71cFcnC1UfXISyhtxIqppx1rT21H/G/WP6YdgHHMCcaMoOCKTFasS2M0HzmXFTXmYSz4oA==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />\n\n    <script src=\"https://momentjs.com/downloads/moment-with-locales.js\"></script>\n\n    <script src=\"themes/";
echo $currenttheme;
echo "/jquery/jquery-3.5.1.min.js\"></script>\n    <script src=\"themes/";
echo $currenttheme;
echo "/jquery/main.js?v=";
echo $rander;
echo "\"></script>\n    <script src=\"themes/";
echo $currenttheme;
echo "/jquery/front.js?v=";
echo $rander;
echo "\"></script>\n    <!-- <link rel=\"stylesheet\" href=\"css/responsive.css?se=4\">     -->\n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/newresponsive.css?v=";
echo $rander;
echo "\">    \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/listuseresponsive.css?v=";
echo $rander;
echo "\">    \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/loginresponsive.css?v=";
echo $rander;
echo "\">    \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/dashboardresponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/liveresponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/movieresponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/movieinforesponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/searchresponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/userinforesponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/settingresponsive.css?v=";
echo $rander;
echo "\"> \n    <link rel=\"stylesheet\" href=\"themes/";
echo $currenttheme;
echo "/css/popupresponsive.css?v=";
echo $rander;
echo "\"> \n    <input type=\"hidden\" id=\"currentthemename\" value=\"";
echo $currenttheme;
echo "\">\n    <input type=\"hidden\" id=\"usernames\" value=\"";
echo $_SESSION["webTvplayer"]["anyname"];
echo "\">\n    <link href=\"themes/";
echo $currenttheme;
echo "/css/bootstrap-pincode-input.css?v=";
echo $rander;
echo "\" rel=\"stylesheet\">\n    <script type=\"text/javascript\" src=\"themes/";
echo $currenttheme;
echo "/jquery/bootstrap-pincode-input.js?v=";
echo $rander;
echo "\"></script>\n    <style>\n    .fullscreencss{\n        position: fixed;\n        right: 4px;\n        bottom: 12px;\n        border-radius: 100%;\n        z-index: 11111111111;\n        width: 34px;\n        height: auto;\n        padding: 4px;\n        background: #ffffff;\n        border: none;\n        outline: none;\n        opacity: 0.05;\n    }\n    .fullscreencss:hover {\n        opacity: 4;\n    }\n    .fullscreencss img{\n        width: 100%;\n        border-radius: 100%;\n    }\n    </style>\n</head>\n<body class=\"attechback\">\n\n\n    <div id=\"turn\">\n        <div class=\"rotateDlogo\" style=\" text-align: center; \">\n            <a class=\"nav-link text-light\" title=\"IPTV Smarters-WebTV\">\n                <img src=\"";
echo !empty($logovalue) ? $logovalue : "themes/" . $currenttheme . "/images/logo_home1.png";
echo "\" alt=\"IPTV Smarters-WebTV\">\n            </a>\n       </div>\n       <div class=\"rotateDicon\" style=\" text-align: center; \">\n            <a class=\"nav-link text-light\" title=\"IPTV Smarters-WebTV\">\n                <img src=\"themes/";
echo $currenttheme;
echo "/images/d-rotate.png\" alt=\"IPTV Smarters-WebTV\">\n            </a>\n        </div>\n        <input type=\"button\"  value=\"Rotate Device\"/>\n    </div>\n    \n      \n    <button type=\"button\" class=\"mobiClickfull fullscreencss\">\n        <img src=\"themes/";
echo $currenttheme;
echo "/images/fullscreenop.gif\" alt=\"\">\n    </button>\n\n    <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12 hide parentallockhtml\" style=\"position: absolute; z-index: 1111; background: rgb(0 0 0 / 85%); height: 100%; padding-top: 7%;\">\n            <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n                <div class=\"row\">\n                    <div class=\"col-sm-1\"></div>\n                    <div class=\"col-sm-10 col-xs-10 col-md-10 col-lg-10\">\n                        <div class=\"parenralconMain show parentalenterpass\">\n                            <div class=\"genSettingHeader\">\n                                <span> <img src=\"themes/";
echo $currenttheme;
echo "/images/parentallock.png\" style=\" padding-bottom: 7px !important; \"> Confirm Your - Parental Control</span>\n                            </div>\n                            <div class=\"parental_input parentallms show\">\n                                <h3>Enter Your Password</h3>\n                                <input type=\"text\" id=\"parentalpinlock\">\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col-sm-1\"></div>\n                    <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n                        <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12 parentallockbtn show\">\n                            <div class=\"row\" style=\" margin-top: 25px;\">\n                                <div class=\"col-sm-1\"></div>\n                                <div class=\"col-sm-5 col-xs-5 col-md-5 col-lg-5\">\n                                    <div class=\"parenntalSetSaveBtn\">\n                                        <a class=\"closeparentelloak\">\n                                            <button> BACK </button>\n                                        </a>\n                                    </div>\n                                </div>\n                                <div class=\"col-sm-5 col-xs-5 col-md-5 col-lg-5\">\n                                    <div class=\"parenntalSetCloseBtn\">\n                                        <a class=\"cofirmlock\">\n                                            <button> NEXT </button>\n                                        </a>\n                                    </div>\n                                </div>\n                                <div class=\"col-sm-1\"></div>\n                            </div>\n                        </div>\n                    </div>\n                </div>  \n            </div>\n        </div>\n\n<!-- Modal -->\n<div class=\"modal fade\" id=\"seriesmodal\" tabindex=\"-1\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n    <div class=\"modal-dialog\">\n      <div class=\"modal-content\">\n        <div class=\"modal-header\">\n          <h5 class=\"modal-title\" id=\"exampleModalLabel\"> Logout </h5>\n          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n        </div>\n        <div class=\"modal-body\">\n              Are you sure to logout?\n        </div>\n        <div class=\"modal-footer\">\n          <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">CLOSE</button>\n          <button type=\"button\" class=\"btn log_out btn-primary\">LOGOUT</button>\n        </div>\n      </div>\n    </div>\n</div>\n";
if ($currentwebtvplayerversion < 0) {
    echo "   <div class=\"alert alert-danger\" style=\"    width: 80%;    margin: 0 auto;    margin-top: 10%;\">\n      <strong>Error Code 103!</strong> <br>Sorry for the Inconvenience. Something Went Wrong Please Contact With Provider\n    </div>\n   ";
    exit;
}
if ($currentenvoirment == "production") {
    echo "    <script type=\"text/javascript\">\n    \$(document).ready(function(){\n        document.onkeydown = function(e) {\n              if(event.keyCode == 123) {\n                 return false;\n              }\n              if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {\n                 return false;\n              }\n              if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {\n                 return false;\n              }\n              if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {\n                 return false;\n              }\n              if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {\n                 return false;\n              }\n            }\n\n    });\n        document.addEventListener('contextmenu', function(e) {\n        e.preventDefault();\n        });\n    </script>\n    ";
}

?>