<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

echo "    <div class=\"mainBody\">\n        <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n            <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12 headerMain\">\n                <div class=\"row\">\n                    <div class=\"col-sm-1 col-xs-1 col-md-1 col-lg-1\">\n                        <div class=\"liveInBack\">\n                            <a class=\"nav-link text-light\" href=\"dashboard.php\" title=\"Back\">\n                                <img src=\"themes/";
echo $currenttheme;
echo "/images/back_arrow.png\" alt=\"Back\">\n                            </a>\n                        </div>\n                    </div>\n                    <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\" style=\" text-align: initial; \">\n                        <div class=\"liveInLogo\">\n                            <a class=\"nav-link text-light\" href=\"dashboard.php\" title=\"IPTV Smarters-WebTV\">\n                                <img src=\"";
echo !empty($logovalue) ? $logovalue : "themes/" . $currenttheme . "/images/logo_home1.png";
echo "\" alt=\"IPTV Smarters-WebTV\">\n                            </a>\n                       </div>\n                    </div>\n                    <div class=\"col-sm-5 col-xs-5 col-md-5 col-lg-5\">\n                        <div class=\"liveInName\">\n                            <span>Subscription Info</span>\n                       </div>\n                    </div>\n                    <div class=\"col-sm-4 col-xs-4 col-md-4 col-lg-4\">\n                        <div class=\"liveInTime\" style=\" text-align: end; \">\n                            <span class=\"time\"></span> <span class=\"date\"> ";
echo date("F d, Y");
echo "</span>&nbsp;&nbsp;&nbsp;&nbsp;\n                       </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n        <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n            <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n                <div class=\"row\">\n                    <div class=\"col-sm-1 col-xs-1 col-md-1 col-lg-1\"></div>\n                    <div class=\"col-sm-10 col-xs-10 col-md-10 col-lg-10\">\n                        <div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">\n                            <div class=\"row generalSettingMain\">\n                                <div class=\"genSettingHeader\">\n                                    <span>Subscription Info</span>\n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Username: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\">\n                                    <span> ";
echo isset($_SESSION["webTvplayer"]["username"]) && !empty($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
echo " </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Account Status: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\">\n                                    ";
if (isset($_SESSION["webTvplayer"]["status"]) && !empty($_SESSION["webTvplayer"]["status"])) {
    if ($_SESSION["webTvplayer"]["status"] == "Active") {
        echo " <span style=\" background-color: green; padding: 2px 8px !important; \"> ";
        echo $_SESSION["webTvplayer"]["status"];
        echo " </span> ";
    } else {
        echo " <span style=\" background-color: red; padding: 2px 8px !important; \"> ";
        echo $_SESSION["webTvplayer"]["status"];
        echo " </span> ";
    }
}
echo "                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Expire Date: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\">\n                                    <span>   ";
if (isset($_SESSION["webTvplayer"]["exp_date"])) {
    if ($_SESSION["webTvplayer"]["exp_date"] == "null" || $_SESSION["webTvplayer"]["exp_date"] == "") {
        echo "Unlimited";
    } else {
        echo date("F d, Y", $_SESSION["webTvplayer"]["exp_date"]);
    }
} else {
    echo "Unlimited";
}
echo "  \n                                    </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Is Trial: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\">\n                                    <span>  ";
if (isset($_SESSION["webTvplayer"]["is_trial"])) {
    if ($_SESSION["webTvplayer"]["is_trial"] == "0") {
        echo "No";
    } else {
        echo "Yes";
    }
}
echo "  \n                                    </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Active Connections: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\"> \n                                    <span>  ";
echo isset($_SESSION["webTvplayer"]["active_cons"]) ? $_SESSION["webTvplayer"]["active_cons"] : "";
echo "  </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Created At: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\"> \n                                    <span> ";
echo isset($_SESSION["webTvplayer"]["created_at"]) && !empty($_SESSION["webTvplayer"]["created_at"]) ? date("F d, Y", $_SESSION["webTvplayer"]["created_at"]) : "";
echo "  </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 leftDivInfo\">\n                                    <span> Max connections: </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                                <div class=\"col-sm-3 col-xs-3 col-md-3 col-lg-3 rightDivInfo\"> \n                                    <span>  ";
echo isset($_SESSION["webTvplayer"]["max_connections"]) && !empty($_SESSION["webTvplayer"]["max_connections"]) ? $_SESSION["webTvplayer"]["max_connections"] : "";
echo "  </span> \n                                </div>\n                                <div class=\"col-sm-2 col-xs-2 col-md-2 col-lg-2\">  </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"col-sm-1 col-xs-1 col-md-1 col-lg-1\"></div>\n                </div>  \n            </div>\n        </div>\n    </div>\n</body>\n</html>";

?>