<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("CLIENTCONTROLLERABSPATH2", dirname(dirname(dirname(__FILE__))) . "/");
class Controller
{
    public $funconn = NULL;
    public function __construct()
    {
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "includes/functions.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "includes/functions.php";
            $this->funconn = new clientcontrolfunctions();
        }
    }
    public function currenttheme($conn = [])
    {
        $currenttheme = "default";
        $currenttheme = $this->funconn->getcurrenttheme($conn);
        return $currenttheme;
    }
    public function currentenvoirment($conn = [])
    {
        $currentenvoirment = "production";
        $currentenvoirment = $this->funconn->getcurrentenvoirment($conn);
        return $currentenvoirment;
    }
    public function currentwebtvplayerversion($conn = [])
    {
        $currentenvoirment = "1.6";
        $currentenvoirment = $this->funconn->getcurrentwebtvplayerversion($conn);
        return $currentenvoirment;
    }
    public function header($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "index";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $sitetitle = !empty($variables["sitetitle"]) ? $variables["sitetitle"] : "";
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        $currentenvoirment = $this->currentenvoirment($conn);
        $currentwebtvplayerversion = $this->currentwebtvplayerversion($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/header.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/header.php";
        } else {
            exit("unable to include header file");
        }
    }
    public function index($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "index";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/index.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/index.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function dashboard($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/dashboard.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/dashboard.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function switchuser($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "switchuser";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/switchuser.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/switchuser.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function test($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "test";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/test.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/test.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function userinfo($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "userinfo";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/userinfo.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/userinfo.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function live($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $parentcondition = !empty($variables["parentcondition"]) ? $variables["parentcondition"] : "";
        $Getparentpinformart = !empty($variables["Getparentpinformart"]) ? $variables["Getparentpinformart"] : "";
        $liveview = !empty($variables["liveview"]) ? $variables["liveview"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        $filename = "categories.php";
        if (isset($_SESSION["webTvplayer"]["protheme"]) && $_SESSION["webTvplayer"]["protheme"] == "yes") {
            $filename = "live.php";
        }
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename)) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename;
        } else {
            exit("unable to include footer file");
        }
    }
    public function livelist($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $CurrentCateName = !empty($variables["CurrentCateName"]) ? $variables["CurrentCateName"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/live.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/live.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function epglist($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $CurrentCateName = !empty($variables["CurrentCateName"]) ? $variables["CurrentCateName"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/live_epg.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/live_epg.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function catchup($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        $filename = "categories.php";
        if (isset($_SESSION["webTvplayer"]["protheme"]) && $_SESSION["webTvplayer"]["protheme"] == "yes") {
            $filename = "catchup.php";
        }
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename)) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename;
        } else {
            exit("unable to include footer file");
        }
    }
    public function catchuplist($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $CurrentCateName = !empty($variables["CurrentCateName"]) ? $variables["CurrentCateName"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/catchup.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/catchup.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function radio($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        $filename = "categories.php";
        if (isset($_SESSION["webTvplayer"]["protheme"]) && $_SESSION["webTvplayer"]["protheme"] == "yes") {
            $filename = "radio.php";
        }
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename)) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename;
        } else {
            exit("unable to include footer file");
        }
    }
    public function radiolist($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $CurrentCateName = !empty($variables["CurrentCateName"]) ? $variables["CurrentCateName"] : "";
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/radio.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/radio.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function movies($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        $filename = "categories.php";
        if (isset($_SESSION["webTvplayer"]["protheme"]) && $_SESSION["webTvplayer"]["protheme"] == "yes") {
            $filename = "movies.php";
        }
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename)) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename;
        } else {
            exit("unable to include footer file");
        }
    }
    public function movieslist($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $getBlockedStreamsIts = !empty($variables["getBlockedStreamsIts"]) ? $variables["getBlockedStreamsIts"] : [];
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $BannerData = !empty($variables["BannerData"]) ? $variables["BannerData"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (!empty($categoryid) && $categoryid == "favorite") {
            if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies_view.php")) {
                include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies_view.php";
            }
        } else {
            if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies.php")) {
                include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies.php";
            } else {
                exit("unable to include footer file");
            }
        }
    }
    public function moviesview($conn = [], $variables = [])
    {
        $BannerData = !empty($variables["BannerData"]) ? $variables["BannerData"] : "";
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $getBlockedStreamsIts = !empty($variables["getBlockedStreamsIts"]) ? $variables["getBlockedStreamsIts"] : [];
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $sortby = !empty($variables["sortby"]) ? $variables["sortby"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies_view.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/movies_view.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function series($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        $filename = "categories.php";
        if (isset($_SESSION["webTvplayer"]["protheme"]) && $_SESSION["webTvplayer"]["protheme"] == "yes") {
            $filename = "series.php";
        }
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename)) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/" . $filename;
        } else {
            exit("unable to include footer file");
        }
    }
    public function serieslist($conn = [], $variables = [])
    {
        $BannerData = !empty($variables["BannerData"]) ? $variables["BannerData"] : "";
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $getBlockedStreamsIts = !empty($variables["getBlockedStreamsIts"]) ? $variables["getBlockedStreamsIts"] : [];
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (!empty($categoryid) && $categoryid == "favorite") {
            if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series_view.php")) {
                include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series_view.php";
            }
        } else {
            if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series.php")) {
                include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series.php";
            } else {
                exit("unable to include footer file");
            }
        }
    }
    public function seriesview($conn = [], $variables = [])
    {
        $BannerData = !empty($variables["BannerData"]) ? $variables["BannerData"] : "";
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $categoryid = !empty($variables["categoryid"]) ? $variables["categoryid"] : "";
        $getBlockedStreamsIts = !empty($variables["getBlockedStreamsIts"]) ? $variables["getBlockedStreamsIts"] : [];
        $getBlockedCategoriesIts = !empty($variables["getBlockedCategoriesIts"]) ? $variables["getBlockedCategoriesIts"] : [];
        $sortby = !empty($variables["sortby"]) ? $variables["sortby"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series_view.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/series_view.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function mastersearch($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "mastersearch";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/mastersearch.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/mastersearch.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function epg($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "epg";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/epg.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/epg.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function navigation($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "dash-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $Getblockedsection = !empty($variables["Getblockedsection"]) ? $variables["Getblockedsection"] : [];
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/navigation.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/navigation.php";
        }
    }
    public function settings($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "dashboard";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $funconn = !empty($variables["funconn"]) ? $variables["funconn"] : "";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/settings.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/settings.php";
        } else {
            exit("unable to include footer file");
        }
    }
    public function footer($conn = [], $variables = [])
    {
        $activepage = !empty($variables["activepage"]) ? $variables["activepage"] : "index";
        $pagetitle = !empty($variables["pagetitle"]) ? $variables["pagetitle"] : "WebTV Player";
        $classsname = !empty($variables["classsname"]) ? $variables["classsname"] : "main-bg";
        $CookieData = !empty($variables["CookieData"]) ? $variables["CookieData"] : "";
        $logovalue = !empty($variables["logovalue"]) ? str_replace("../", "", $variables["logovalue"]) : "images/blackdemo-Logo.jpg";
        $section = !empty($variables["section"]) ? $variables["section"] : "";
        $categories = !empty($variables["categories"]) ? $variables["categories"] : "";
        $portallinks = !empty($variables["portallinks"]) ? $variables["portallinks"] : "";
        $currenttheme = $this->currenttheme($conn);
        if (file_exists(CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/footer.php")) {
            include_once CLIENTCONTROLLERABSPATH2 . "themes/" . $currenttheme . "/includes/footer.php";
        } else {
            exit("unable to include footer file");
        }
    }
}

?>