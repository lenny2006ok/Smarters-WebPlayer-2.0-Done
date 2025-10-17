<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

echo "<!DOCTYPE html>\n<html lang=\"en\">\n  <head>\n    <!-- Main CSS-->\n    <link rel=\"stylesheet\" type=\"text/css\" href=\"../admin/css/main.css?v=2\">\n    <link rel=\"stylesheet\" type=\"text/css\" href=\"../admin/css/style.css?v=2\">\n    <style type=\"text/css\">\n    </style> \n    <!-- Font-icon css-->\n    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css\">\n    <title>\n      ";
$titleis = isset($titleis) ? $titleis : "DASHBOARD";
echo $titleis . " | ADMIN PANEL";
echo "    </title>\n    <link rel=\"shortcut icon\" type=\"image/png\" href=\"images/gavel31.png\"/>\n    <link rel=\"shortcut icon\" type=\"image/png\" href=\"images/gavel31.png\"/>\n  </head>\n  <body class=\"app sidebar-mini rtl\">\n    <!-- Navbar-->\n    ";
$LogoIS = "../images/blackdemo-Logo.jpg";
if (isset($logovalue) && $logovalue != "") {
    $LogoIS = $logovalue;
}
echo "    <header class=\"app-header\"><a class=\"app-header__logo\" href=\"dashboard.php\"><img src=\"";
echo $LogoIS;
echo "\" class=\"adminlogo\"></a>\n      <!-- Sidebar toggle button--><a class=\"app-sidebar__toggle\" href=\"#\" data-toggle=\"sidebar\" aria-label=\"Hide Sidebar\"></a>\n      <!-- Navbar Right Menu-->\n      <ul class=\"app-nav\">        \n        <!-- User Menu-->\n        <li class=\"dropdown\"><a class=\"app-nav__item\" href=\"#\" data-toggle=\"dropdown\" aria-label=\"Open Profile Menu\"><i class=\"fa fa-user fa-lg\"></i></a>\n          <ul class=\"dropdown-menu settings-menu dropdown-menu-right\">\n            <li><a class=\"dropdown-item\" href=\"logout.php\"><i class=\"fa fa-sign-out fa-lg\"></i> Logout</a></li>\n          </ul>\n        </li>\n      </ul>\n    </header>\n        <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@8.8.5/dist/sweetalert2.all.min.js\" integrity=\"sha256-m7hW8Yyirje5pHkEHOZDzM2r8gscxT0nxPDY7rtJwGE=\" crossorigin=\"anonymous\"></script>";

?>