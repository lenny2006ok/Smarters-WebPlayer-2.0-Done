<?php


if (!isset($_REQUEST["adduser"])) {
    echo "        <div class=\"blnkonload\"></div>\n        <style type=\"text/css\">\n\n        .blnkonload {\n            position: fixed;\n            background-image: url(\"themes/";
    echo $currenttheme;
    echo "/images/live_background.jpg\");\n            width: 100%;\n            height: 100%;\n            top: 0;\n            bottom: 0;\n            right: 0;\n            left: 0;\n            z-index: 999;\n        }\n        </style>\n        <script type=\"text/javascript\">\n        \$(document).ready(function(){\n            redirctforce = \"0\";\n            localData = localStorage.getItem(\"listUser\");\n            if(localData != null && localData != \"\")\n            {\n                decoded = JSON.parse(localData);\n                jQuery.each(decoded, function(index, item) {\n                   jQuery.each(item, function(index2, item2) {\n                    redirctforce = \"1\";\n                   });\n                });\n            }\n\n            if(redirctforce == \"1\")\n            {\n                window.location.href = \"switchuser.php\";\n            }\n            else\n            {\n                \$(\".blnkonload\").remove(); \n            }\n        });\n        </script>\n        ";
}
$cookieusername = isset($_COOKIE["username"]) ? $_COOKIE["username"] : "";
$FinalUsername = isset($_GET["username"]) ? $_GET["username"] : $cookieusername;
$cookiepassword = isset($_COOKIE["userpassword"]) ? base64_decode($_COOKIE["userpassword"]) : "";
$FinalPassword = isset($_GET["password"]) ? $_GET["password"] : $cookiepassword;
$PortalLinks = [];
if (isset($portallinks) && !empty($portallinks)) {
    $PortalLinks = unserialize($portallinks);
}
echo "<body style=\"background-image: url(themes/";
echo $currenttheme;
echo "/images/color-line.png);background-size: 100% 100%;background-repeat: repeat;\">\n\n    <div class=\"bodyMain\">\n        \n        <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n            <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12 loginbody\">\n                <div class=\"row\">\n                    <div class=\"col-sm-6 col-xs-6 col-md-6 col-lg-6\">\n                        <div class=\"loginLogo\">\n                            <a class=\"nav-link text-light\" title=\"User details\" style=\"padding-left: 10px;\">\n                                <img src=\"";
echo !empty($logovalue) ? $logovalue : "themes/" . $currenttheme . "/images/iptv-smarter-logo.png";
echo "\" alt=\"IPTV Smarters-WebTV\" style=\" width: 30%; \">\n                            </a>\n                            <div class=\"loginBtnLeft\">\n                               <!--  <button type=\"submit\" class=\"btnConVPN\"><i class=\"fa fa-shopping-cart\" aria-hidden=\"true\"></i> GET A FREE TRIAL</button> -->\n                                <a href=\"switchuser.php\"\n                                <button type=\"submit\" class=\"btnListUser\"><i class=\"fa fa-list\" aria-hidden=\"true\"></i> LIST USER</button>\n    </a>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col-sm-6 col-xs-6 col-md-6 col-lg-6\">\n                        <div class=\"loginForm\">\n                            <form action=\"\">\n                                <!-- <a class=\"nav-link text-light\" href=\"#\" title=\"User details\" style=\" padding-left: 10px; \">\n                                    <img src=\"images/logo.png\" alt=\"IPTV Smarters-WebTV\" style=\" width: 40%; \">\n                                </a> -->\n                                <h1 style=\" margin-bottom: 18px !important; \">Enter Your Login Details</h1>\n                                <div class=\"input-group\">\n                                    <input type=\"text\" class=\"inputBox\" id=\"input-anyName\" placeholder=\"Any Name\" value=\"\" required>\n                                </div>\n                                <div class=\"input-group\">\n                                    <input type=\"text\" class=\"inputBox\" id=\"input-login\" placeholder=\"Username\" value=\"\" required>\n                                </div>\n                                <div class=\"input-group\">\n                                    <input type=\"password\" class=\"inputBox\" id=\"input-pass\" placeholder=\"Password\" value=\"\" required>\n                                    <div class=\"input-group-append\">\n                                        <label class=\"eyesClicker showpass\" data-current=\"hide\">\n                                            <i class=\"fa fa-eye-slash hideeye\"  aria-hidden=\"true\"></i>\n                                            <i class=\"fa fa-eye showeye\" aria-hidden=\"true\" style=\"display: none;\"></i>\n                                        </label>\n                                    </div>\n                                </div>\n                                ";
if (!empty($PortalLinks) && 1 < count($PortalLinks)) {
    echo "                                     <div class=\"input-group\">\n                                         <select class=\"inputBox\" id=\"input-portal\">\n                                            ";
    foreach ($PortalLinks as $key => $value) {
        echo "                                                <option value=\"";
        echo $key;
        echo "\">";
        echo $key;
        echo "</option>\n                                                ";
    }
    echo "                                         </select>\n                                     </div>\n                                    ";
}
echo "                                \n                                <div class=\"checkbox checkbox_new\" style=\"text-align: left; padding-left: 60px !important; display: none;\">\n                                    <label style=\" font-size: 20px; padding: 0px !important; background-color: transparent; border: none; margin: 0px !important; \">\n                                        <input type=\"checkbox\" id=\"rememberMe\" style=\" width: 33px; height: 19px; border-radius: 50px !important; padding: 0px !important; background: transparent; border: none; \"> Remember me\n                                    </label>\n                                </div>\n                                <button type=\"button\" id=\"add_user\">ADD USER <i class=\"fa fa-spin fa-spinner hide checkingspin\" style=\"color:black;\"></i></button>\n                            </form>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</body>\n</html>";

?>