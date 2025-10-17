<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

echo " <!-- Sidebar menu-->\n  <div class=\"app-sidebar__overlay\" data-toggle=\"sidebar\"></div>\n  <aside class=\"app-sidebar\">\n    <div class=\"app-sidebar__user\">\n      <div>\n        <p class=\"app-sidebar__user-name\">\n        Admin Panel\n        <a class=\"ml-2 btn btn-danger btn-sm\" href=\"logout.php\"><i class=\"fa fa-sign-out fa-lg\"></i> Logout</a>\n        </p>\n        <a href=\"../index.php\" target=\"_blank\" class=\"btn btn-success btn-sm btn-block clientarealink\">Go To WebPlayer</a>\n      </div>\n    </div>\n    ";
$activemenu = isset($activemenu) ? $activemenu : "DASHBOARD";
$license = isset($license) ? $license : "";
echo "    <ul class=\"app-menu\">\n      <li>\n        <a class=\"app-menu__item ";
echo $activemenu == "DASHBOARD" ? "active" : "";
echo "\" href=\"dashboard.php\">\n          <i class=\"app-menu__icon fa fa-dashboard\"></i>\n          <span class=\"app-menu__label\">Dashboard</span>\n        </a>\n      </li>\n      <li>\n        <a class=\"app-menu__item ";
echo $activemenu == "CHANGEPASSWORD" ? "active" : "";
echo "\" href=\"changepassword.php\">\n          <i class=\"app-menu__icon fa fa-key\"></i>\n          <span class=\"app-menu__label\">Change Password</span>\n        </a>\n      </li>\n      <li>\n        <a class=\"app-menu__item ";
echo $activemenu == "MANAGELICENSE" ? "active" : "";
echo "\" href=\"managelicense.php\">\n          <i class=\"app-menu__icon fa  fa-id-card\"></i>\n          <span class=\"app-menu__label\">Manage License</span>\n        </a>\n      </li>\n      ";
if ($license == "Active") {
    echo "          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "CONFIGURATION" ? "active" : "";
    echo "\" href=\"configuration.php\">\n              <i class=\"app-menu__icon fa  fa-gear\"></i>\n              <span class=\"app-menu__label\">Settings</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "USERLOGS" ? "active" : "";
    echo "\" href=\"userlogs.php\">\n              <i class=\"app-menu__icon fa  fa-users\"></i>\n              <span class=\"app-menu__label\">User Logs</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "MEDIA FILES" ? "active" : "";
    echo "\" href=\"media.php\">\n              <i class=\"app-menu__icon fa fa-file-image-o\"></i>\n              <span class=\"app-menu__label\">Media Files</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "THEME LIST" ? "active" : "";
    echo "\" href=\"theme_list.php\">\n              <i class=\"app-menu__icon fa  fa-picture-o\"></i>\n              <span class=\"app-menu__label\">Themes</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "BLOCKCONTENT" ? "active" : "";
    echo "\" href=\"blockcontent.php\">\n              <i class=\"app-menu__icon fa  fa-ban\"></i>\n              <span class=\"app-menu__label\">Block Content</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "ADDBANNERS" ? "active" : "";
    echo "\" href=\"banners.php\">\n              <i class=\"app-menu__icon fa fa-film\"></i>\n              <span class=\"app-menu__label\">Add Banners</span>\n            </a>\n          </li>\n          <li>\n            <a class=\"app-menu__item ";
    echo $activemenu == "UNBLOCKIP" ? "active" : "";
    echo "\" href=\"unblockip.php\">\n              <i class=\"app-menu__icon fa  fa-unlock\"></i>\n              <span class=\"app-menu__label\">Unblock IP's</span>\n            </a>\n          </li>\n          ";
}
echo "    </ul>\n  </aside>\n\n\n";

?>