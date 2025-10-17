<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("AJAXCONROLERDIRPATH", dirname(dirname(__FILE__)) . "/");
if (file_exists(AJAXCONROLERDIRPATH . "connection.php")) {
    include_once AJAXCONROLERDIRPATH . "connection.php";
}
if (file_exists(AJAXCONROLERDIRPATH . "lib/Common/CommonController.php")) {
    include_once AJAXCONROLERDIRPATH . "lib/Common/CommonController.php";
}
if (file_exists(AJAXCONROLERDIRPATH . "admin/includes/functions.php")) {
    include_once AJAXCONROLERDIRPATH . "admin/includes/functions.php";
}
if (file_exists(AJAXCONROLERDIRPATH . "includes/functions.php")) {
    include_once AJAXCONROLERDIRPATH . "includes/functions.php";
}
$funconn = new clientcontrolfunctions();
if (isset($_POST["action"]) && $_POST["action"] == "webtvlogin") {
    $Runtime = $_POST["runtime"];
    $username = $_POST["uname"];
    $password = $_POST["upass"];
    $rememberMe = $_POST["rememberMe"];
    $returnData = ["result" => "error", "message" => "Invalid Details"];
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->makeconnection();
    if (!empty($conn)) {
        $CommonController = new CommonController();
        $controlfunctions = new controlfunctions();
        $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
        if (isset($ConfigDetails["portallinks"]) && !empty($ConfigDetails["portallinks"])) {
            $PortalLinks = unserialize($ConfigDetails["portallinks"]);
            $indexaray = [];
            $in = 0;
            foreach ($PortalLinks as $identifire => $val) {
                $indexaray[$in] = $identifire;
                $in++;
            }
            $indexii = $indexaray[$Runtime];
            $Fportallink = isset($PortalLinks[$indexii]) && $PortalLinks[$indexii] != "" ? $PortalLinks[$indexii] : "";
            if (!empty($Fportallink)) {
                $bar = "/";
                if (substr($Fportallink, -1) == "/") {
                    $bar = "";
                }
                $Fportallink = $Fportallink . $bar;
                $ApiLink = $Fportallink . "player_api.php?username=" . $username . "&password=" . $password;
                $APIRequest = $CommonController->CallApiRequest($ApiLink);
                $Result = $APIRequest;
                if ($Result["result"] == "success") {
                    if (isset($Result["data"]->user_info->auth)) {
                        if ($Result["data"]->user_info->auth != 0) {
                            if ($Result["data"]->user_info->status == "Active") {
                                $clientIP = $CommonController->client_ipaddress();
                                $datetime = date("Y-m-d h:i:s");
                                $UserStatus = "Active";
                                $userdetailsid = "";
                                $EncPassword = $controlfunctions->webtvtheme_encrypt($password);
                                $QueryData = ["request" => "Get", "table" => "webtvtheme_userdetails", "data" => ["username" => $username, "password" => $EncPassword, "portallink" => $Fportallink]];
                                $UserData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                if (empty($UserData)) {
                                    $QueryData = ["request" => "Insert", "table" => "webtvtheme_userdetails", "data" => ["username" => $username, "password" => $EncPassword, "portallink" => $Fportallink, "status" => $UserStatus]];
                                    $InsertData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                    if ($InsertData["result"] == "success") {
                                        $userdetailsid = isset($InsertData["insert_id"]) ? $InsertData["insert_id"] : "";
                                    }
                                } else {
                                    $userdetailsid = isset($UserData[0]["id"]) ? $UserData[0]["id"] : "";
                                    $userstatus = isset($UserData[0]["status"]) ? $UserData[0]["status"] : "";
                                    if ($userstatus != "Active") {
                                        $returnData = ["result" => "error", "message" => "Your account is blocked for WebTv Player - Please contact the owner!"];
                                        echo json_encode($returnData);
                                        exit;
                                    }
                                }
                                if ($userdetailsid != "") {
                                    $QueryData = ["request" => "Insert", "table" => "webtvtheme_log", "data" => ["user_id" => $userdetailsid, "login_time" => $datetime, "dns" => $Fportallink, "ip_address" => $clientIP]];
                                    $InsertData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                }
                                if ($rememberMe == "on") {
                                    setcookie("username", $username, time() + 1209600, "/", $_SERVER["SERVER_NAME"], false);
                                    setcookie("userpassword", $EncPassword, time() + 1209600, "/", $_SERVER["SERVER_NAME"], false);
                                }
                                $SessionArray = ["username" => $Result["data"]->user_info->username, "password" => $Result["data"]->user_info->password, "portallink" => $Fportallink, "auth" => $Result["data"]->user_info->auth, "status" => $Result["data"]->user_info->status, "exp_date" => $Result["data"]->user_info->exp_date, "active_cons" => $Result["data"]->user_info->active_cons, "is_trial" => $Result["data"]->user_info->is_trial, "max_connections" => $Result["data"]->user_info->max_connections, "created_at" => $Result["data"]->user_info->created_at, "allowed_output_formats" => $Result["data"]->user_info->allowed_output_formats, "url" => $Result["data"]->server_info->url, "port" => $Result["data"]->server_info->port, "rtmp_port" => $Result["data"]->server_info->rtmp_port, "timezone" => $Result["data"]->server_info->timezone];
                                $_SESSION["webTvplayer"] = $SessionArray;
                                if (substr($XCStreamHostUrl, -1) == "/") {
                                    $bar = "";
                                    $XCStreamHostUrl = substr($XCStreamHostUrl, 0, -1);
                                }
                                $_SESSION["selectedhost"] = $XCStreamHostUrl;
                                $returnData = ["result" => "success", "message" => $SessionArray];
                            } else {
                                $returnData = ["result" => "error", "message" => "Status is " . $Result["data"]->user_info->status];
                            }
                        } else {
                            $returnData = ["result" => "error", "message" => "Invalid Details"];
                        }
                    } else {
                        $returnData = ["result" => "error", "message" => "Invalid Details"];
                    }
                } else {
                    $returnData = ["result" => "error", "message" => "Invalid Details"];
                }
            }
        }
    }
    echo json_encode($returnData);
    exit;
} else {
    if (isset($_POST["action"]) && $_POST["action"] == "logoutProcess") {
        unset($_SESSION["webTvplayer"]);
        session_destroy();
        echo "1";
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "getmoviedatalimit") {
        $SortingType = $_POST["sorting"];
        $categoryID = $_POST["category"];
        $sliderfor = $_POST["sliderfor"];
        $streamid = $_POST["streamid"];
        $cateID = $_POST["cateID"];
        $dataoffset = $_POST["dataoffset"];
        $datalimit = $_POST["datalimit"];
        $majoraction = $_POST["majoraction"];
        $coverup = "";
        $rating = "";
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $CommonController = new CommonController();
        $clientcontrolfunctions = new clientcontrolfunctions();
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
        $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "movies", $categoryID);
        $Arrayforsorting = [];
        if ($categoryID == "favorite") {
            $webtvpanel_getFavforCategories = $clientcontrolfunctions->webtvpanel_getFavforCategories("movies", $conn);
            if (!empty($webtvpanel_getFavforCategories)) {
                foreach ($webtvpanel_getFavforCategories as $Favkey) {
                    $StreamKeyIS = unserialize($Favkey["favdata"]);
                    $Arrayforsorting[$StreamKeyIS->added] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "rating" => $StreamKeyIS->rating, "rating_5based" => $StreamKeyIS->rating_5based, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "container_extension" => $StreamKeyIS->container_extension, "selectedfavmovie" => "selectedfavmovie", "direct_source" => $StreamKeyIS->direct_source];
                }
            } else {
                echo "<div class='noresult'>No Result Found!!</div>";
                exit;
            }
        } else {
            $MoviesResult = $CommonController->getMoviesByCateGoryID($categoryID);
            if ($MoviesResult["result"] == "success" && !empty($MoviesResult["data"])) {
                foreach ($MoviesResult["data"] as $StreamData) {
                    $specialArray = ["!", "@", "#", "\$", "%", "&", "*"];
                    shuffle($specialArray);
                    $customKey = $StreamData->name;
                    if ($SortingType == "topadded") {
                        $customKey = $StreamData->added;
                        if (array_key_exists($StreamData->added, $Arrayforsorting)) {
                            $randonstringis = "";
                            foreach ($specialArray as $sprcialsData) {
                                $randonstringis .= $sprcialsData;
                            }
                            $customKey = $StreamData->added . $randonstringis;
                        }
                    }
                    if ($SortingType == "toprated") {
                        $customKey = $StreamData->rating . "-" . $StreamData->name;
                    }
                    if (!in_array($StreamData->stream_id, $getBlockedStreamsIts)) {
                        $Arrayforsorting[$customKey] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => $StreamData->stream_type, "stream_id" => $StreamData->stream_id, "stream_icon" => $StreamData->stream_icon, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "added" => $StreamData->added, "category_id" => $StreamData->category_id, "container_extension" => $StreamData->container_extension, "custom_sid" => $StreamData->custom_sid, "direct_source" => $StreamData->direct_source];
                    }
                }
                if ($SortingType == "topadded") {
                    ksort($Arrayforsorting);
                    $Arrayforsorting = array_reverse($Arrayforsorting, true);
                }
                if ($SortingType == "toprated") {
                    ksort($Arrayforsorting);
                    $Arrayforsorting = array_reverse($Arrayforsorting, true);
                }
                if ($SortingType == "asc") {
                    array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                }
                if ($SortingType == "desc") {
                    array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                    $Arrayforsorting = array_reverse($Arrayforsorting, true);
                }
            }
        }
        if (!empty($Arrayforsorting)) {
            if ($sliderfor != "" && $sliderfor == "viewinfoslide") {
                $Counter = 0;
                foreach ($Arrayforsorting as $data) {
                    if ($Counter < 11) {
                        $Icon = $data->stream_icon;
                        if ($Icon == "") {
                            $Icon = "images/no_poster.png";
                            $coverup = "noposter";
                        }
                        if ($streamid != $data->stream_id) {
                            echo "\t\t\t\t\t    \t<div class=\"sliderimage-div ";
                            echo $Counter;
                            echo "\">\n\t\t                        <img src=\"";
                            echo $Icon;
                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images\"/>\n\t\t                        <div class=\"img-title\" onclick='viewdetails(\"";
                            echo $data->stream_id;
                            echo "\",\"";
                            echo $SortingType;
                            echo "\")'>\t\t                        \t\n\t\t                            <span class=\"movies-title\">";
                            echo $data->name;
                            echo "</span>\n\t\t                            <div class=\"rating\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                            echo "</div>\n\t\t                        </div>\n\t\t                    </div>\n\t\t\t\t\t    \t";
                        }
                        echo "\t  \n\t        \t\t\t";
                    }
                    $Counter++;
                }
                if ($Counter == 1) {
                    echo "\t\n        \t\t\t<div class=\"sliderimage-div\" >\n                        <img src=\"images/no_poster.png\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images\"/>\n                        <div class=\"img-title\">\t                        \t\n                            <span class=\"movies-title\">No Related Slider!!</span>\n                        </div>\n                    </div>\n\t        \t\t";
                }
                if (8 < $Counter) {
                    echo "\t        \t\t<div class=\"prev\">\n\t                    <img src=\"images/left-arrow-white.png\" alt=\"\" class=\"prev-icon d-none\" id=\"rightArrow_default\"/>\n\t                </div>\n\t                <div class=\"next\">\n\t                    <img src=\"images/right-arrow-white.png\" alt=\"\" class=\"next-icon\" id=\"leftArrow_default\">\n\t                </div>\n\t        \t\t";
                }
                echo "                ";
            } else {
                if ($majoraction != "" && $majoraction == "default") {
                    $Counter = 0;
                    foreach ($Arrayforsorting as $data) {
                        if ($dataoffset <= $Counter && $Counter <= $datalimit) {
                            $Icon = $data->stream_icon;
                            if ($Icon == "") {
                                $Icon = "images/no_poster.png";
                            }
                            echo "\t        \t\t\t<div class=\"sliderimage-view un-";
                            echo $Counter;
                            echo " ";
                            echo $data->selectedfavmovie;
                            echo "\" data-streamID=\"";
                            echo $data->stream_id;
                            echo "\">\n\t                        <img src=\"";
                            echo $Icon;
                            echo "\" onerror=\"this.src='images/no_poster.png';\" alt=\"\" class=\"view-images\"/>\n\t                        <div class=\"img-title\" onclick='viewdetails(\"";
                            echo $data->stream_id;
                            echo "\",\"";
                            echo $SortingType;
                            echo "\")'>\n\t                            <span class=\"movies-title\">";
                            echo $data->name;
                            echo "</span>\n\t                            <div class=\"rating\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                            echo "</div>\n\t                        </div>\n\t                    </div>\n\t        \t\t\t";
                        }
                        $Counter++;
                    }
                    echo "\t\t\t  \t";
                    if ($datalimit < $Counter) {
                        echo "\t\t        \n\t\t\t        <center class=\"loading-loadBtn\">\n\t\t\t          <button type=\"button\" class=\"LoadMoreBtn btn btn-success rippler rippler-default\" data-dataoffset=\"";
                        echo $datalimit;
                        echo "\" data-categoryID=\"";
                        echo $categoryID;
                        echo "\">Load More <i class=\"LoadingMoreFa fa fa-spin fa-spinner d-none\"></i></button>\n\t\t\t        </center>\n\t\t        ";
                    }
                } else {
                    $Counter = 0;
                    foreach ($Arrayforsorting as $data) {
                        if ($Counter < 11) {
                            $Icon = $data->stream_icon;
                            if ($Icon == "") {
                                $Icon = "images/no_poster.png";
                            }
                            echo "\t        \t\t\t<div class=\"sliderimage-div ";
                            echo $Counter;
                            echo "\">\n\t                        <img src=\"";
                            echo $Icon;
                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images\"/>\n\t                        <div class=\"img-title\" onclick='viewdetails(\"";
                            echo $data->stream_id;
                            echo "\",\"";
                            echo $SortingType;
                            echo "\")'>\n\t                            <span class=\"movies-title\">";
                            echo $data->name;
                            echo "</span>\n\t                            <div class=\"rating\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                            echo "</div>\n\t                        </div>\n\t                    </div>\n\t        \t\t\t";
                        }
                        $Counter++;
                    }
                    if ($Counter == 1) {
                        echo "\t\n        \t\t\t<div class=\"sliderimage-div\" >\n                        <img src=\"images/no_poster.png\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images\"/>\n                        <div class=\"img-title\">\t                        \t\n                            <span class=\"movies-title\">No Related Slider!!</span>\n                        </div>\n                    </div>\n\t        \t\t";
                    }
                    echo "\t        \t\t<div class=\"viewall-div\">\n\t\t                <a href=\"movies-view.php?sort=";
                    echo $SortingType;
                    echo "&cate=";
                    echo $cateID;
                    echo "\" class=\"viewall-title\">View all</a>\n\t\t            </div>\n\t        \t";
                }
            }
        } else {
            echo "        \t<a href=\"#\" class=\"noresult-slideView\">\n\t        \t\t<div class=\"viewall-div\">\n\t\t                <div class=\"viewall-title\">\n\t\t                No Result Found !!\n\t\t            \t</div>\n\t\t            </div>\n\t        \t</a>\n        \t";
        }
        exit;
    } else {
        if (isset($_POST["action"]) && $_POST["action"] == "getseriesdatalimit") {
            $SortingType = $_POST["sorting"];
            $categoryID = $_POST["category"];
            $majoraction = $_POST["majoraction"];
            $cateID = $_POST["cateID"];
            $dataoffset = $_POST["dataoffset"];
            $datalimit = $_POST["datalimit"];
            $coverup = "";
            $CommonController = new CommonController();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $BackdropArray = [];
            $Arrayforsorting = [];
            $clientcontrolfunctions = new clientcontrolfunctions();
            $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
            $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
            $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "series", $categoryID);
            if ($categoryID == "favorite") {
                $webtvpanel_getFavforCategories = $clientcontrolfunctions->webtvpanel_getFavforCategories("series", $conn);
                if (!empty($webtvpanel_getFavforCategories)) {
                    foreach ($webtvpanel_getFavforCategories as $Favkey) {
                        $StreamKeyIS = unserialize($Favkey["favdata"]);
                        $Arrayforsorting[$StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "series_id" => $StreamKeyIS->series_id, "cover" => $StreamKeyIS->cover, "plot" => $StreamKeyIS->plot, "cast" => $StreamKeyIS->cast, "director" => $StreamKeyIS->director, "genre" => $StreamKeyIS->genre, "releaseDate" => $StreamKeyIS->releaseDate, "last_modified" => $StreamKeyIS->last_modified, "rating" => $StreamKeyIS->rating, "rating_5based" => $StreamKeyIS->rating_5based, "youtube_trailer" => $StreamKeyIS->youtube_trailer, "episode_run_time" => $StreamKeyIS->episode_run_time, "selectedfavmovie" => "selectedfavmovie", "category_id" => $StreamKeyIS->category_id];
                    }
                } else {
                    echo "<div class='noresult'>No Result Found!!</div>";
                    exit;
                }
            } else {
                $MoviesResult = $CommonController->getSeriesByCateGoryID($categoryID);
                if ($MoviesResult["result"] == "success" && !empty($MoviesResult["data"])) {
                    foreach ($MoviesResult["data"] as $StreamData) {
                        $specialArray = ["!", "@", "#", "\$", "%", "&", "*"];
                        shuffle($specialArray);
                        $customKey = $StreamData->name;
                        if ($SortingType == "topadded") {
                            $customKey = $StreamData->last_modified;
                            if (array_key_exists($StreamData->last_modified, $Arrayforsorting)) {
                                $randonstringis = "";
                                foreach ($specialArray as $sprcialsData) {
                                    $randonstringis .= $sprcialsData;
                                }
                                $customKey = $StreamData->last_modified . $randonstringis;
                            }
                        }
                        if ($SortingType == "toprated") {
                            $customKey = $StreamData->rating . "-" . $StreamData->name;
                        }
                        if (!in_array($StreamData->series_id, $getBlockedStreamsIts)) {
                            $Arrayforsorting[$customKey] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => "series", "series_id" => $StreamData->series_id, "cover" => $StreamData->cover, "plot" => $StreamData->plot, "cast" => $StreamData->cast, "director" => $StreamData->director, "genre" => $StreamData->genre, "releaseDate" => $StreamData->releaseDate, "last_modified" => $StreamData->last_modified, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "youtube_trailer" => $StreamData->youtube_trailer, "episode_run_time" => $StreamData->episode_run_time, "category_id" => $StreamData->category_id, "rating" => $StreamData->rating];
                        }
                    }
                    if ($SortingType == "topadded") {
                        ksort($Arrayforsorting);
                        $Arrayforsorting = array_reverse($Arrayforsorting, true);
                    }
                    if ($SortingType == "toprated") {
                        ksort($Arrayforsorting);
                        $Arrayforsorting = array_reverse($Arrayforsorting, true);
                    }
                    if ($SortingType == "asc") {
                        array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                    }
                    if ($SortingType == "desc") {
                        array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                        $Arrayforsorting = array_reverse($Arrayforsorting, true);
                    }
                } else {
                    if ($majoraction != "" && $majoraction == "default") {
                        echo "        \t<a href=\"#\" class=\"grid-noresult\">\n\t        \t\t<div class=\"viewall-div\">\n\t\t                <div class=\"viewall-title\">\n\t\t                No Result Found !!\n\t\t            \t</div>\n\t\t            </div>\n\t        \t</a>\n        \t";
                    } else {
                        echo "        \t<div class=\"noresult-slideView\">\n        \t\t<div class=\"viewall-div\">\n\t                <div class=\"viewall-title\">\n\t                No Result Found !!\n\t            \t</div>\n\t            </div>\n        \t</div>\n        \t";
                    }
                    exit;
                }
            }
            if (!empty($Arrayforsorting)) {
                if ($majoraction != "" && $majoraction == "default") {
                    $Counter = 0;
                    foreach ($Arrayforsorting as $data) {
                        if ($dataoffset <= $Counter && $Counter <= $datalimit) {
                            $Icon = $data->cover;
                            if ($Icon == "") {
                                $Icon = "images/no_poster.png";
                                $coverup = "noposter";
                            }
                            $QuersyData = "moviename=" . $data->name . "&StreamId=" . $data->series_id . "&CateGoryId=" . $data->category_id . "&posterImage=" . $Icon . "&extension=" . $data->container_extension . "&rating5=" . $data->rating_5based . "&movienum=" . $data->num;
                            echo "\n\t        \t\t\t<div class=\"sliderimage-view un-";
                            echo $Counter;
                            echo " ";
                            echo $data->selectedfavmovie;
                            echo "\" data-streamID=\"";
                            echo $data->series_id;
                            echo "\">\n\t                        <img src=\"";
                            echo $Icon;
                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"view-images\"/>\n\t                        <div class=\"img-title\" onclick='viewdetails(\"";
                            echo $data->series_id;
                            echo "\",\"";
                            echo $SortingType;
                            echo "\")'>\n\t                            <span class=\"movies-title\">";
                            echo $data->name;
                            echo "</span>\n\t                            <div class=\"rating\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                            echo "</div>\n\t                        </div>\n\t                    </div>\n\t        \t\t\t";
                        }
                        $Counter++;
                    }
                    if ($datalimit < $Counter) {
                        echo "\t\t\t        <center class=\"loading-loadBtn\">\n\t\t\t          <button type=\"button\" class=\"LoadMoreBtn btn btn-success rippler rippler-default\" data-dataoffset=\"";
                        echo $datalimit;
                        echo "\" data-categoryID=\"";
                        echo $categoryID;
                        echo "\">Load More <i class=\"LoadingMoreFa fa fa-spin fa-spinner d-none\"></i></button>\n\t\t\t        </center>\n\t\t        ";
                    }
                } else {
                    $Counter = 0;
                    foreach ($Arrayforsorting as $data) {
                        if ($Counter < 11) {
                            $Icon = $data->cover;
                            if ($Icon == "") {
                                $Icon = "images/no_poster.png";
                                $coverup = "noposter";
                            }
                            $QuersyData = "moviename=" . $data->name . "&StreamId=" . $data->series_id . "&CateGoryId=" . $data->category_id . "&posterImage=" . $Icon . "&extension=" . $data->container_extension . "&rating5=" . $data->rating_5based . "&movienum=" . $data->num;
                            echo "\t        \t\t\t<div class=\"sliderimage-div ";
                            echo $Counter;
                            echo "\">\n\t                        <img src=\"";
                            echo $Icon;
                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images\"/>\n\t                        <div class=\"img-title\" onclick='viewdetails(\"";
                            echo $data->series_id;
                            echo "\",\"";
                            echo $SortingType;
                            echo "\")'>\n\t                            <span class=\"movies-title\">";
                            echo $data->name;
                            echo "</span>\n\t                            <div class=\"rating\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                            echo "</div>\n\t                        </div>\n\t                    </div>\n\t        \t\t\t";
                        }
                        $Counter++;
                    }
                    if (7 < Counter) {
                        echo "\t        \t\t<div class=\"viewall-div\">\n\t\t                <a href=\"series-view.php?sort=";
                        echo $SortingType;
                        echo "&cate=";
                        echo $cateID;
                        echo "\" class=\"viewall-title\">View all</a>\n\t\t            </div>\n        \t\t";
                    }
                }
            } else {
                echo "        \t<div href=\"#\" class=\"noresult-slideView\">\n        \t\t<div class=\"viewall-div\">\n\t                <div class=\"viewall-title\">\n\t                No Result Found !!\n\t            \t</div>\n\t            </div>\n\t        </div>\n        \t";
            }
            exit;
        } else {
            if (isset($_POST["action"]) && $_POST["action"] == "getmoviedetailsbyid") {
                $streamid = $_POST["streamid"];
                $categoryID = $_POST["category"];
                $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                $CommonController = new CommonController();
                $clientcontrolfunctions = new clientcontrolfunctions();
                $controlfunctions = new controlfunctions();
                $movieData = $CommonController->getMoviesInfo($categoryID, $streamid);
                $StreamIDIS = isset($StreamId) && !empty($StreamId) ? $StreamId : $streamid;
                $MainStreamName = isset($moviename) && !empty($moviename) ? $moviename : "n/A";
                $MainStreamNum = isset($movienum) && !empty($movienum) ? $movienum : "n/A";
                $Mainextension = isset($extension) && !empty($extension) ? $extension : "n/A";
                $rating5 = isset($rating5) && !empty($rating5) ? $rating5 : "";
                $posterImage = "";
                $DatabaseObj = new DBConnect();
                $conn = $DatabaseObj->makeconnection();
                $checkifExists = $clientcontrolfunctions->webtvpanel_getFavExists("movies", $SessionStroedportallink, $streamid, $conn);
                $GetExternalLinkdetails = $controlfunctions->webtvtheme_getExternalLinkdetails($conn, "movies", $streamid, $SessionStroedportallink);
                if ($movieData["result"] == "success" && !empty($movieData["data"])) {
                    if (isset($movieData["data"]->movie_data)) {
                        $MainStreamName = $movieData["data"]->movie_data->name != "" ? $movieData["data"]->movie_data->name : "n/A";
                        $Mainextension = $movieData["data"]->movie_data->container_extension != "" ? $movieData["data"]->movie_data->container_extension : "n/A";
                    }
                    $MovieTrailer = "";
                    if (isset($movieData["data"]->info->youtube_trailer) && !empty($movieData["data"]->info->youtube_trailer)) {
                        $MovieTrailer = $movieData["data"]->info->youtube_trailer;
                    }
                    if (!empty($movieData["data"]->info->cover_big)) {
                        $posterImage = $movieData["data"]->info->cover_big;
                    } else {
                        $posterImage = $movieData["data"]->info->movie_image;
                    }
                    $BackGroundImageCover = $movieData["data"]->info->movie_image;
                    if (!empty($movieData["data"]->info->backdrop_path)) {
                        $backdropData = $movieData["data"]->info->backdrop_path;
                        $randkey = array_rand($backdropData);
                        $BackGroundImageCover = $backdropData[$randkey];
                    }
                    $movieDescription = $movieData["data"]->info->description != "" ? $movieData["data"]->info->description : $movieData["data"]->info->plot;
                    $MovieCast = $movieData["data"]->info->cast != "" ? $movieData["data"]->info->cast : "n/A";
                    $rate5 = $rating5;
                    if ($rating5 == "") {
                        $rate10 = explode("/", $movieData["data"]->info->rating);
                        $rate5 = intval($rate10[0]) / 2;
                    }
                    if (strpos($rate5, ".") !== false) {
                        $rate5 = floatval($rate5);
                    } else {
                        $rate5 = intval($rate5);
                    }
                    echo "\n\t\t<!-- new code here -->\n\t\t<div class=\"modal-content viewinfo-content\" style=\"\n\t\t\t\tbackground: url('";
                    echo $BackGroundImageCover;
                    echo "');\n\t\t\t\tbackground-position: center;\n\t\t\t\tbackground-repeat: no-repeat;\n\t\t\t\tbackground-size: cover;\n\t\t\t\tbackground-color: #233035;\n    \t\t\tbackground-blend-mode: overlay;\n    \t\t\twidth:100vw;\n\t\t\t\t\">\n\t\t\t<div class=\"detailsexploresection\">\t\n\t\t\t<div class=\"player_changeIssue alert alert-info\" style=\"position: fixed; top: -300px;left: 35%;\">\n\t      \t\tUnable to play this format in Jw player trying with aj player.\n\t        </div>\n\t        <div class=\"modal-header viewinfo-header\">\n\t          <button type=\"button\" class=\"close view-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n\t            <span aria-hidden=\"true\">&times;</span>\n\t          </button>\n\t        </div>\n\t        <div class=\"modal-body viewinfo-body text-light\">\t\n\t       \t\t<div class=\"row player_row d-none\">\n\t       \t\t\t<div class=\"col-md-2 watchPoster\">\n\t       \t\t\t\t<div class=\"stream-icon\">\n\t\t                  <img src=\"";
                    echo !empty($posterImage) ? $posterImage : "images/no_poster.png";
                    echo "\" onerror=\"this.src='images/no_poster.png';\" class=\"stream-image\">\n\t\t                </div>\n\t\t                <div class=\"rating-movies\">";
                    echo $clientcontrolfunctions->WebTVClient_starRating($rate5);
                    echo "</div>\n\t\t                <button class=\"btn btn-info btn-backtoinfo mt-2\">Back to Info</button>\n\t       \t\t\t</div>\n\t       \t\t\t\n\t       \t\t\t<div class=\"col-md-10\">\n\t       \t\t\t\t<div class=\"PlayerHolder\" data-ajplayer=\"\" data-flowplayer=\"\" data-jwplayer=\"\">\n\t\t\t\t            <div id=\"player-holder\" class=\"d-none\"></div>\n\t\t\t\t        </div>\n\t\t\t          \t<div id=\"YoutubePLayerHolder-";
                    echo $StreamIDIS;
                    echo "\" class=\"d-none youtubeholderCommon\">\n\t\t\t          \t</div>\n\t       \t\t\t</div>\n\t       \t\t</div>       \t\n\t          <div class=\"row streamview\">\n\t              <div class=\"col-md-2 info-poster\">\n\t                <div class=\"stream-icon\">\n\t                  <img src=\"";
                    echo !empty($posterImage) ? $posterImage : "images/no_poster.png";
                    echo "\" onerror=\"this.src='images/no_poster.png';\" class=\"stream-image\">\n\t                </div>\n\t                <div class=\"text-center stream-rating\">";
                    echo $clientcontrolfunctions->WebTVClient_starRating($rate5);
                    echo "</div>\n\t              </div>\n\t              <div class=\"col-md-8 offset-md-1\">\n\t                <div class=\"stream-details\">\n\t                  <div class=\"stream-title\">\n\t                    <h1>\n\n\t                    \t";
                    echo $MainStreamName;
                    echo "\t                    \t<i  class=\"fa fa-heart makeFav ";
                    echo !empty($checkifExists) ? "activefav" : "";
                    echo "\"\n\t\t\t                    aria-hidden=\"true\" \n\t\t\t                    data-favstreamid=\"";
                    echo $StreamIDIS;
                    echo "\" \n\t\t\t                    data-favstreamtype=\"movies\" \n\t\t\t                    data-cateid='";
                    echo $categoryID;
                    echo "' \n\t\t\t                    data-favis=\"";
                    echo !empty($checkifExists) ? "no" : "yes";
                    echo "\"  \n\t\t\t                    style=\"float: right;z-index:999999;margin-right:10px;\">             \n\t\t\t                </i>\n\t                    </h1>\n\t                  </div>\n\t                  \n\t                </div>\n\t                <div class=\"stream-persona\">\n\t                  <span class=\"direct-persona\"><i class=\"fas fa-bullhorn\"></i> Director</span>\n\t                  <span class=\"direct-persona\"><i class=\"far fa-calendar-alt\"></i> Release Date</span>\n\t                  <span class=\"direct-persona\"><i class=\"far fa-clock\"></i> Duration</span>\n\t                  <span class=\"direct-persona\"><i class=\"fas fa-users\"></i> Cast</span>\n\t                </div>\n\t                <div class=\"stream-persona-info\">\n\t                  <span class=\"direct-persona-info\"> ";
                    echo $movieData["data"]->info->director != "" ? $movieData["data"]->info->director : "n/A";
                    echo "</span>\n\t                  <span class=\"direct-persona-info\"> ";
                    echo $movieData["data"]->info->releasedate != "" ? date("l, d F Y", strtotime($movieData["data"]->info->releasedate)) : "n/A";
                    echo "</span>\n\t                  <span class=\"direct-persona-info\"> ";
                    echo $movieData["data"]->info->duration != "" ? $movieData["data"]->info->duration : "n/A";
                    echo "</span>\n\t                  <span class=\"direct-persona-info cast\">\n \t\t\t\t\t\t";
                    echo substr($MovieCast, 0, 40);
                    echo " \t\t\t\t\t\t<span class=\"badge badge-info showCast ";
                    echo $MovieCast == "n/A" ? "d-none" : "";
                    echo "\" data-toggle=\"yes\">Show Full Cast</span>\n\t\t\t\t\t  </span>\n\t                  <span class=\"direct-persona-info fullcast d-none\"> ";
                    echo $MovieCast;
                    echo "<span class=\"badge badge-danger showCast\" data-toggle=\"no\">Hide</span></span>\n\t                </div>\n\t                <div class=\"stream-description\">\n\t                    <p>";
                    echo $movieDescription != "" ? $movieDescription : "n/A";
                    echo "</p>\n\t                  </div>\n\t              </div>\n\t              <div class=\"col-md-12\">\n\t                <div class=\"view-button\">\n\t                \t";
                    if ($MovieTrailer != "") {
                        echo "\t                    <button class=\"btn watchTrailer-custom\" onclick=\"watchTrailer('";
                        echo $MovieTrailer;
                        echo "','";
                        echo $StreamIDIS;
                        echo "')\">Watch Trailer</button>\n\t                    \t";
                    }
                    echo "\t                    <button class=\"btn watchNow-custom\" data-external=\"";
                    echo !empty($GetExternalLinkdetails) ? $GetExternalLinkdetails[0]["externallink"] : "";
                    echo "\" data-streamID=\"";
                    echo $StreamIDIS;
                    echo "\" data-ext=\"";
                    echo $Mainextension;
                    echo "\" onclick=\"watchMovie('";
                    echo $StreamIDIS;
                    echo ".";
                    echo $Mainextension;
                    echo "')\">Watch Now</button>\n\t                </div>\n\t              </div>\n\t          \t</div>\n\t          \t";
                    if (!empty($MovieCast) && $MovieCast != "n/A") {
                        $explodedCast = explode(",", $MovieCast);
                        if (!empty($explodedCast)) {
                            echo "\t\t          \t<div class=\"row slider-row castslidersection\">\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h3 class=\"related-slide\">Cast & Crew</h3>\n\t\t\t\t\t\t\t<div class=\"view-slide\" id=\"viewSliderCast\">\n\t\t\t\t\t\t\t\t";
                            $counterIs = 0;
                            foreach ($explodedCast as $castName) {
                                $castName = trim($castName);
                                echo "\t\t\t\t\t\t\t\t\t<div class=\"viewslide-banner commomapicastpicture castselectorby-";
                                echo $counterIs;
                                echo "\" data-castnameis=\"";
                                echo $castName;
                                echo "\"  data-counteris=\"";
                                echo $counterIs;
                                echo "\">\n\t\t\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t";
                                $counterIs++;
                            }
                            if (6 < count($explodedCast)) {
                                echo "\t\t\t\t\t        \t\t<div class=\"prev\">\n\t\t\t\t\t                    <img src=\"images/left-arrow-white.png\" alt=\"\" class=\"prev-icon d-none\" id=\"rightArrow_Cast\"/>\n\t\t\t\t\t                </div>\n\t\t\t\t\t                <div class=\"next\">\n\t\t\t\t\t                    <img src=\"images/right-arrow-white.png\" alt=\"\" class=\"next-icon\" id=\"leftArrow_Cast\">\n\t\t\t\t\t                </div>\n\t\t\t\t\t        \t\t";
                            }
                            echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t          \t</div>\n\t          \t\t";
                        }
                    }
                    echo "\t          \t<div class=\"row slider-row\">\n\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t<h3 class=\"related-slide\">Related Movies</h3>\n\t\t\t\t\t\t<div class=\"view-slide\" id=\"viewSlider\">\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"viewslide-banner\">\n\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t          </div>\n\t        </div>\n\t        </div>\n\t        <div class=\"castsectionexplorehere d-none\" style=\"min-height: 635px;\">\n\t        \t<div class=\"modal-header viewinfo-header\">\n\t\t          <button type=\"button\" class=\"close view-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n\t\t            <span aria-hidden=\"true\">&times;</span>\n\t\t          </button>\n\t\t        </div>\n\t\t        <div class=\"modal-body text-light bodycontentforcastinfo\">\t\n\t\t       \t\t\n\t\t       \t</div>\n\t        </div>\n\t    </div>\n\t\t";
                    exit;
                } else {
                    echo "0";
                    exit;
                }
            } else {
                if (isset($_POST["action"]) && $_POST["action"] == "getseriesdetailsbyid") {
                    $streamid = $_POST["streamid"];
                    $categoryID = $_POST["category"];
                    $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                    $CommonController = new CommonController();
                    $controlfunctions = new controlfunctions();
                    $clientcontrolfunctions = new clientcontrolfunctions();
                    $SeriesData = $CommonController->getSeriesInfo($categoryID, $streamid);
                    $DatabaseObj = new DBConnect();
                    $conn = $DatabaseObj->makeconnection();
                    $GetExternalLinkdetails = $controlfunctions->webtvtheme_getExternalLinkdetails($conn, "series", $streamid, $SessionStroedportallink);
                    $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
                    $StreamIDIS = isset($StreamId) && !empty($StreamId) ? $StreamId : "n/A";
                    $MainStreamName = isset($moviename) && !empty($moviename) ? $moviename : "n/A";
                    $MainStreamCategoryID = isset($CateGoryId) && !empty($CateGoryId) ? $CateGoryId : "n/A";
                    $MainStreamCover = isset($posterImage) && !empty($posterImage) ? $posterImage : "images/no_poster.png";
                    $MainStreamPlot = isset($plot) && !empty($plot) ? $plot : "n/A";
                    $MainStreamCast = isset($cast) && !empty($cast) ? $cast : "n/A";
                    $MainStreamGenre = isset($genre) && !empty($genre) ? $genre : "n/A";
                    $MainStreamDirector = isset($director) && !empty($director) ? $director : "n/A";
                    $MainStreamRating = isset($rating) && !empty($rating) ? $rating : "0";
                    $MainStreamReleaseDate = isset($releaseDate) && !empty($releaseDate) ? $releaseDate : "n/A";
                    $posterImage = "";
                    if ($SeriesData["result"] == "success" && !empty($SeriesData["data"])) {
                        $SeriesTrailer = "";
                        if (isset($SeriesData["data"]->info->youtube_trailer) && !empty($SeriesData["data"]->info->youtube_trailer)) {
                            $SeriesTrailer = $SeriesData["data"]->info->youtube_trailer;
                        }
                        if (isset($SeriesData["data"]->episodes) && !empty($SeriesData["data"]->episodes)) {
                            $AllSeasonData = isset($SeriesData["data"]->seasons) && !empty($SeriesData["data"]->seasons) ? $SeriesData["data"]->seasons : "";
                            $SeasonCoverImage = [];
                            if ($AllSeasonData != "") {
                                foreach ($AllSeasonData as $SeasonDataKey) {
                                    $SeasonCoverImage[$SeasonDataKey->season_number] = !empty($SeasonDataKey->cover_big) ? $SeasonDataKey->cover_big : $SeasonDataKey->cover;
                                }
                            }
                            $OnloadAvtiveEpisode = 0;
                            $SeasonsIdData = [];
                            $Appepisodes = [];
                            $MainposterImage = $MainStreamCover;
                            $MainMovieName = $MainStreamName;
                            $MainMovieDesc = $MainStreamPlot;
                            $MainMoviegenre = $MainStreamGenre;
                            $MainMoviereleaseDate = $MainStreamReleaseDate;
                            $MainMovierrating_5based = $MainStreamRating != 0 ? $MainStreamRating / 2 : "0";
                            $MainMovierdirector = $MainStreamDirector;
                            $MainMoviercast = $MainStreamCast;
                            if (!empty($SeriesData["data"]->info)) {
                                $SeriesDetails = $SeriesData["data"]->info;
                                if ($SeriesDetails->cover != "") {
                                    $MainposterImage = $SeriesDetails->cover;
                                }
                                $MainMovieName = $SeriesDetails->name != "" ? $SeriesDetails->name : "n/A";
                                $MainMovieDesc = $SeriesDetails->plot != "" ? $SeriesDetails->plot : "n/A";
                                $MainMoviegenre = $SeriesDetails->genre != "" ? $SeriesDetails->genre : "n/A";
                                $MainMoviereleaseDate = $SeriesDetails->releaseDate != "" ? $SeriesDetails->releaseDate : "n/A";
                                $MainMovierrating_5based = $SeriesDetails->rating_5based != "" ? $SeriesDetails->rating_5based : "n/A";
                                $MainMovierdirector = $SeriesDetails->director != "" ? $SeriesDetails->director : "n/A";
                                $MainMoviercast = $SeriesDetails->cast != "" ? $SeriesDetails->cast : "n/A";
                            }
                            if (!empty($SeriesData["data"]->episodes)) {
                                $Appepisodes = $SeriesData["data"]->episodes;
                                foreach ($SeriesData["data"]->episodes as $episodes) {
                                    foreach ($episodes as $episodesData) {
                                        $SeasonsIdData[$episodesData->season] = "season";
                                    }
                                }
                            }
                            $checkFavExists = $clientcontrolfunctions->webtvpanel_getFavExists("series", $SessionStroedportallink, $streamid, $conn);
                            $BackGroundImageCover = $MainposterImage;
                            if (!empty($SeriesData["data"]->info->backdrop_path)) {
                                $backdropData = $SeriesData["data"]->info->backdrop_path;
                                $randkey = array_rand($backdropData);
                                $BackGroundImageCover = $backdropData[$randkey];
                            }
                            echo "\t\t\t   <div class=\"modal-content viewinfo-content custome-modelcontent1\" style=\"\n                  background-image: url('";
                            echo $BackGroundImageCover;
                            echo "');\n                 background-repeat: no-repeat;\n                  background-size: cover;\n                  background-position: center;\n                  background-color: #233035;\n                  background-blend-mode: exclusion;\n                  width:100vw;\n                  max-height: unset;\n                  height: 103%;\n              \">\n              <div class=\"detailsexploresection\">\t\n\t\t\t\t<div class=\"player_changeIssue alert alert-info\" style=\"position: fixed; top: -300px;left: 35%;\">\n\t\t\t\tUnable to play this format in Jw player trying with aj player.\n\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-header\" style=\"border:0;\"> \n\t\t\t\t<button type=\"button\" class=\"close view-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n\t\t\t\t<span aria-hidden=\"true\">&times;</span>\n\t\t\t\t</button>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-body body-content\">\n\t\t\t\t<div class=\"popup-content\">\n\t\t\t\t<div class=\"row\">\n\t\t\t\t<div class=\"col-md-2 col-sm-6 col-xs-6\">\n\t\t\t\t<div class=\"poster\">\n\t\t\t\t  <div class=\"poster-img series-posterimage\">\n\t\t\t\t  \t<img src=\"";
                            echo $MainposterImage;
                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"img-responsive\">\n\t\t\t\t  \t\n\t\t\t\t  </div>\n\t\t\t\t</div>\n\n\t\t\t\t<div class=\"list-group seasons mt-3\" id=\"myList\" role=\"tablist\">\n\t\t\t\t  ";
                            if (!empty($SeasonsIdData)) {
                                $ConditionCounter = 1;
                                foreach ($SeasonsIdData as $SeasonNumber => $val) {
                                    echo "\t\t\t\t        <a class=\"list-group-item list-group-item-action ";
                                    echo $ConditionCounter == 1 ? "active" : "";
                                    echo "\" data-toggle=\"list\" href=\"#s-";
                                    echo $SeasonNumber;
                                    echo "\" role=\"tab\">Season ";
                                    echo $SeasonNumber;
                                    echo "</a>\n\t\t\t\t        ";
                                    $ConditionCounter++;
                                }
                            }
                            echo "  \n\t\t\t\t</div>\n\n\t\t\t\t</div>        \n\t\t\t\t<div class=\"col-md-4 col-sm-6 col-xs-6\">\n\t\t\t\t<div class=\"poster-details1\">\n\t\t\t\t    <ul class=\"list-unstyled row text-light\">      \n\t\t\t\t      <div class=\"col-sm-6 col-xs-6\">\n\t\t\t\t        <li class=\"mb-2\"><i class=\"fas fa-film\"></i>&nbsp; ";
                            echo $MainMoviegenre;
                            echo "</li>\n\t\t\t\t        <li class=\"mb-2\"><i class=\"fas fa-calendar-alt\"></i>&nbsp;";
                            echo date("d,F Y", strtotime($MainMoviereleaseDate));
                            echo "</li>\n\t\t\t\t        <li>\n\t\t\t\t        \t<div class=\"rating-series\">";
                            echo $clientcontrolfunctions->WebTVClient_starRating($MainMovierrating_5based);
                            echo "&nbsp; <span>";
                            echo $MainMovierrating_5based . " Stars";
                            echo "</span>                                \t\t\n\t\t\t\t        \t</div>\n\t\t\t\t        \t \n\t\t\t\t        </li>\n\t\t\t\t      </div>        \n\t\t\t\t      <div class=\"col-sm-6 col-xs-6\">\n\t\t\t\t        <li class=\"mb-2\"><i class=\"fas fa-directions\"></i>&nbsp; ";
                            echo $MainMovierdirector;
                            echo "</li>\n\t\t\t\t        ";
                            if (!empty($MainMoviercast) && $MainMoviercast != "n/A") {
                                echo "\t\t\t\t            <li class=\"mb-2 cast\"><i class=\"fas fa-users\"></i> &nbsp;";
                                echo substr($MainMoviercast, 0, 40);
                                echo " <span class=\"badge badge-info showCast ";
                                echo MainMoviercast == "n/A" ? "d-none" : "";
                                echo "\" data-toggle=\"yes\">Show Full Cast</span></li>\n\t\t\t\t            <li class=\"mb-2 fullcast d-none\"><i class=\"fas fa-users\"></i> &nbsp;";
                                echo $MainMoviercast;
                                echo " <span class=\"badge badge-danger showCast\" data-toggle=\"no\">Hide</span></li>\n\t\t\t\t            ";
                            }
                            echo "\t\t\t\t         \n\t\t\t\t      </div>                   \n\t\t\t\t        \n\t\t\t\t    </ul>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"card bg-trans p-2\">\n\t\t\t\t    <h2 class=\"stream-title text-light\" data-toggle=\"tooltip\" title=\"";
                            echo $MainMovieName;
                            echo "\">\n\t\t\t\t      ";
                            echo $MainMovieName;
                            echo " \n\n\t\t\t\t    </h2>\n\t\t\t\t    <i class=\"fa fa-heart seriesheart makeFav ";
                            echo !empty($checkFavExists) ? "activefav" : "";
                            echo "\"\n\t\t\t\t              aria-hidden=\"true\"\n\t\t\t\t              data-favstreamid=\"";
                            echo $streamid;
                            echo "\" \n\t\t\t\t              data-cateid=\"";
                            echo $categoryID;
                            echo "\"\n\t\t\t\t              data-favstreamtype=\"series\" \n\t\t\t\t              data-favis=\"";
                            echo !empty($checkFavExists) ? "no" : "yes";
                            echo "\"  \n\t\t\t\t              style=\"z-index:999999;font-size: 22px;\"\n\t\t\t\t            > </i>\n\t\t\t\t    <div class=\"column episodes text-light scrollbar\"  id=\"style-1\">\n\t\t\t\t      <div class=\"tab-content\">\n\t\t\t\t      ";
                            if (!empty($SeasonsIdData)) {
                                $ConditionCounter2 = 1;
                                foreach ($SeasonsIdData as $SeasonNumber => $val) {
                                    echo "\t\t\t\t          <div class=\"tab-pane ";
                                    echo $ConditionCounter2 == 1 ? "active" : "";
                                    echo "\" id=\"s-";
                                    echo $SeasonNumber;
                                    echo "\" role=\"tabpanel\">\n\t\t\t\t              <div class=\"list-group bg-trans\" id=\"myList2\" role=\"tablist\">\n\t\t\t\t           ";
                                    $CounterCon2 = 1;
                                    foreach ($Appepisodes as $episodes) {
                                        foreach ($episodes as $episodesData) {
                                            $BlockedCateGoriesIDs = $CommonController->getBlockedSeriesEpisode($conn, $getActivePortalID, $streamid, $categoryID, $episodesData->id);
                                            if ($episodesData->season == $SeasonNumber && !in_array($episodesData->id, $BlockedCateGoriesIDs)) {
                                                if ($CounterCon2 == 1) {
                                                    $OnloadAvtiveEpisode = $episodesData->id;
                                                }
                                                echo "\t\t\t\t                    <a class=\"list-group-item list-group-item-action ";
                                                echo $CounterCon2 == 1 ? "active" : "";
                                                echo "\" data-episid=\"";
                                                echo $episodesData->id;
                                                echo "\" data-toggle=\"list\" href=\"#epis-";
                                                echo $episodesData->id;
                                                echo "\" data-toggle=\"tooltip\" title=\"";
                                                echo urldecode($episodesData->title);
                                                echo "\" role=\"tab\"><b>";
                                                echo $CounterCon2;
                                                echo " </b>";
                                                echo urldecode($episodesData->title);
                                                echo "</a>\n\t\t\t\t                    ";
                                                $CounterCon2++;
                                            }
                                        }
                                    }
                                    echo "\t\t\t\t              </div>\n\t\t\t\t             </div>\n\t\t\t\t            ";
                                    $ConditionCounter2++;
                                }
                            }
                            echo "\t\t\t\t      </div>\n\t\t\t\t    </div>\n\t\t\t\t</div> \n\t\t\t\t</div>\n\n\t\t\t\t<div class=\"col-md-6 col-sm-12 col-xs-12\">\n\t\t\t\t<div class=\"tab-content\">\n\t\t\t\t    <div class=\"PlayerHolder\" data-ajplayer=\"\" data-flowplayer=\"\" data-jwplayer=\"\">\n\t\t\t\t        <div id=\"player-holder\" class=\"d-none\"  style=\"border:solid 2px #fff; height: auto !important;\"  >\n\t\t\t\t        </div>\n\t\t\t\t    </div>\n\t\t\t\t";
                            $CounterCon3 = 1;
                            foreach ($Appepisodes as $episodes) {
                                foreach ($episodes as $episodesData) {
                                    $externlLinkValue = "";
                                    if (!empty($GetExternalLinkdetails)) {
                                        foreach ($GetExternalLinkdetails as $externlLinkseries) {
                                            if ($externlLinkseries["season_no"] == $episodesData->season && $externlLinkseries["episode_id"] == $episodesData->id) {
                                                $externlLinkValue = $externlLinkseries["externallink"];
                                            }
                                        }
                                    }
                                    $BlockedCateGoriesIDs = $CommonController->getBlockedSeriesEpisode($conn, $getActivePortalID, $streamid, $categoryID, $episodesData->id);
                                    if (!in_array($episodesData->id, $BlockedCateGoriesIDs)) {
                                        echo "\t\t\t\t        <div id=\"YoutubePLayerHolder-";
                                        echo $episodesData->id;
                                        echo "\" class=\"d-none youtubeholderCommon\">\n\t\t\t\t        </div>\n\t\t\t\t        <button id=\"backToInfo-";
                                        echo $episodesData->id;
                                        echo "\" data-episid=\"";
                                        echo $episodesData->id;
                                        echo "\" class=\"btn-backtoinfo btn btn-info d-none mt-3\">Back to Info</button>\n\t\t\t\t        <div id=\"epis-";
                                        echo $episodesData->id;
                                        echo "\" class=\"tab-pane fade in ";
                                        echo $CounterCon3 == 1 ? "active" : "";
                                        echo " ";
                                        echo $CounterCon3 == 1 ? "show" : "";
                                        echo "\"> \n\t\t\t\t            <h2 class=\"text-light\">\n\t\t\t\t              ";
                                        echo urldecode($episodesData->title);
                                        echo "                                    \n\t\t\t\t            </h2> \n\t\t\t\t            <h5 class=\"text-light\">Episode ";
                                        echo $episodesData->episode_num;
                                        echo "</h5>\n\t\t\t\t            \n\t\t\t\t            <div class=\"row\">\n\t\t\t\t                <div class=\"col-md-3 seasonIfb";
                                        echo $episodesData->season;
                                        echo " p-0\">\n\t\t\t\t                    ";
                                        $EpisodesCover = $MainposterImage;
                                        if (!empty($SeasonCoverImage[$episodesData->season])) {
                                            $EpisodesCover = $SeasonCoverImage[$episodesData->season];
                                        }
                                        echo "\t\t\t\t                    <div class=\"card bg-trans series-epi-poster\">\n\t\t\t\t                        <img src=\"";
                                        echo !empty($EpisodesCover) ? $EpisodesCover : "images/no_poster.png";
                                        echo "\" alt=\"\" class=\"img-responsive\" onerror=\"this.src='images/no_poster.png';\">\n\t\t\t\t                    </div>\n\t\t\t\t                        \n\t\t\t\t                </div>\n\t\t\t\t                <div class=\"col-md-9\">\n\t\t\t\t                <p class=\"card bg-trans p-2 text-light\">";
                                        echo $episodesData->info->plot != "" ? $episodesData->info->plot : "n/A";
                                        echo "</p>\n\t\t\t\t                </div>\n\t\t\t\t            </div>\n\t\t\t\t            <div class=\"row mt-3\">\n\t\t\t\t              ";
                                        if ($SeriesTrailer != "") {
                                            echo "\t\t\t\t                <div class=\"col-md-6 col-sm-12\">\n\t\t\t\t                    <button onclick=\"watchTrailer('";
                                            echo $SeriesTrailer;
                                            echo "','";
                                            echo $episodesData->id;
                                            echo "')\" class=\"btn btn-cus-watchTrailer btn-block m-2\">Watch Trailer</button>\n\t\t\t\t                </div>\n\t\t\t\t                \n\t\t\t\t                \n\t\t\t\t                ";
                                        }
                                        echo "\t\t\t\t              <div class=\"col-md-6 col-sm-12\">\n\t\t\t\t                  <button onclick=\"watchnow('";
                                        echo $episodesData->id;
                                        echo "','";
                                        echo $episodesData->container_extension;
                                        echo "')\" class=\"btn btn-cus-watchNow btn-block m-2\" data-extlink=\"";
                                        echo !empty($externlLinkValue) ? $externlLinkValue : "";
                                        echo "\">watch it now</button>\n\t\t\t\t              </div>\n\t\t\t\t              <div class=\"col-md-12\">\n\t\t\t\t                  <h3 class=\"descHeading text-light\">Description</h3>\n\t\t\t\t                  <p class=\"decription text-light\" id=\"scroll-dec\">";
                                        echo $MainMovieDesc;
                                        echo "</p>\n\t\t\t\t              </div>\n\t\t\t\t            </div>\n\t\t\t\t        </div>\n\t\t\t\t    \t";
                                        $CounterCon3++;
                                    }
                                }
                            }
                            echo "\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t</div>\n\n\n\t\t\t\t";
                            if (!empty($MainMoviercast) && $MainMoviercast != "n/A") {
                                $explodedCast = explode(",", $MainMoviercast);
                                if (!empty($explodedCast)) {
                                    echo "\t\t\t\t\t<div class=\"row slider-row castslidersection\">\n\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t<h3 class=\"related-slide text-light\">Cast & Crew</h3>\n\t\t\t\t\t\t<div class=\"view-slide\" id=\"viewSliderCast\">\n\t\t\t\t\t\t\t";
                                    $counterIs = 0;
                                    foreach ($explodedCast as $castName) {
                                        $castName = trim($castName);
                                        echo "\t\t\t\t\t\t\t\t<div class=\"viewslide-banner commomapicastpicture castselectorby-";
                                        echo $counterIs;
                                        echo "\" data-castnameis=\"";
                                        echo $castName;
                                        echo "\"  data-counteris=\"";
                                        echo $counterIs;
                                        echo "\">\n\t\t\t\t\t\t\t\t\t<img src=\"images/loading.jpg\" class=\"viewsilderImage\">\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t";
                                        $counterIs++;
                                    }
                                    if (6 < count($explodedCast)) {
                                        echo "\t\t\t\t        \t\t<div class=\"prev\">\n\t\t\t\t                    <img src=\"images/left-arrow-white.png\" alt=\"\" class=\"prev-icon d-none\" id=\"rightArrow_Cast\"/>\n\t\t\t\t                </div>\n\t\t\t\t                <div class=\"next\">\n\t\t\t\t                    <img src=\"images/right-arrow-white.png\" alt=\"\" class=\"next-icon\" id=\"leftArrow_Cast\">\n\t\t\t\t                </div>\n\t\t\t\t        \t\t";
                                    }
                                    echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
                                }
                            }
                            echo "\t\t\t\t</div>\n\n\n\t\t\t\t<!-- <div class=\"clearfix\"></div> -->\n\t\t\t\t<div class=\"ts-content\">\n\n\n\t\t\t\t<div class=\"clearfix\"></div>\n\n\t\t\t\t</div>\n\n\t\t\t\t</div>\n           \t</div>\n           \t<div class=\"castsectionexplorehere d-none\" style=\"min-height: 635px;\">\n\t        \t<div class=\"modal-header viewinfo-header\">\n\t\t          <button type=\"button\" class=\"close view-close\" data-dismiss=\"modal\" aria-label=\"Close\">\n\t\t            <span aria-hidden=\"true\">&times;</span>\n\t\t          </button>\n\t\t        </div>\n\t\t        <div class=\"modal-body text-light bodycontentforcastinfo\">\t\n\t\t       \t\t\n\t\t       \t</div>\n\t        </div>\n          </div>\n\t\t\t";
                            exit;
                        } else {
                            echo "<div style=\"position: absolute;left:568px;top: 250px;font-size: 26px;font-weight: 600;color:#f8f8f8;\"\">No Episode Available !!</div>";
                            exit;
                        }
                    } else {
                        echo "0";
                        exit;
                    }
                } else {
                    if (isset($_POST["action"]) && $_POST["action"] == "getLivestreamFromID") {
                        $view = $_POST["view"];
                        $categoryID = $_POST["categoryID"];
                        $DatabaseObj = new DBConnect();
                        $conn = $DatabaseObj->makeconnection();
                        $CommonController = new CommonController();
                        $clientcontrolfunctions = new clientcontrolfunctions();
                        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                        $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
                        $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "live", $categoryID);
                        $webtvpanel_getFavforCategories = $clientcontrolfunctions->webtvpanel_getFavforCategories("live", $conn);
                        $Arrayforsorting = [];
                        if ($categoryID == "favorite") {
                            if (!empty($webtvpanel_getFavforCategories)) {
                                foreach ($webtvpanel_getFavforCategories as $Favkey) {
                                    $StreamKeyIS = unserialize($Favkey["favdata"]);
                                    $Arrayforsorting[$SortingType == "topadded" ? $StreamKeyIS->added : $StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                }
                                if ($SortingType == "topadded") {
                                    ksort($Arrayforsorting);
                                    $Arrayforsorting = array_reverse($Arrayforsorting, false);
                                }
                                if ($SortingType == "asc") {
                                    array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                                }
                                if ($SortingType == "desc") {
                                    array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                                    $Arrayforsorting = array_reverse($Arrayforsorting, false);
                                }
                                $StreamData["data"] = $Arrayforsorting;
                            } else {
                                echo "<div class=\"text-center\">No Result Found!!</div>";
                                exit;
                            }
                        } else {
                            $StreamData = $CommonController->getliveStream($categoryID);
                            if ($StreamData["result"] == "success") {
                                $Arrayforsorting = [];
                                foreach ($StreamData["data"] as $StreamKeyIS) {
                                    $BackdropArray = [];
                                    if (!empty($StreamKeyIS->backdrop_path)) {
                                        foreach ($StreamKeyIS->backdrop_path as $backVal) {
                                            $BackdropArray[] = $backVal;
                                        }
                                    }
                                    if (!in_array($StreamKeyIS->stream_id, $getBlockedStreamsIts)) {
                                        $Arrayforsorting[$SortingType == "topadded" ? $StreamKeyIS->added : $StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                    }
                                }
                            }
                            if ($SortingType == "topadded") {
                                ksort($Arrayforsorting);
                                $Arrayforsorting = array_reverse($Arrayforsorting, false);
                            }
                            if ($SortingType == "asc") {
                                array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                            }
                            if ($SortingType == "desc") {
                                array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                                $Arrayforsorting = array_reverse($Arrayforsorting, false);
                            }
                            $StreamData["data"] = $Arrayforsorting;
                        }
                        foreach ($StreamData["data"] as $chanel) {
                            $chanelIcon = $chanel->stream_icon;
                            if ($chanelIcon == "") {
                                $chanelIcon = "images/no_logo.jpg";
                            }
                            if ($_POST["view"] == "epgview") {
                                $checkFavExists = $clientcontrolfunctions->webtvpanel_getFavExists("live", $SessionStroedportallink, $chanel->stream_id, $conn);
                                echo "\t            <li class=\"channellistcontainer chlist-";
                                echo $chanel->stream_id;
                                echo " \" data-datastreamid=\"";
                                echo $chanel->stream_id;
                                echo "\" data-channelname=\"";
                                echo $chanel->name;
                                echo "\" data-channellogois=\"";
                                echo $chanelIcon;
                                echo "\">\n\t              <input type=\"hidden\" class=\"serch_key\" value=\"";
                                echo $chanel->name;
                                echo "\" data-parentliclass=\"chlist-";
                                echo $chanel->stream_id;
                                echo "\" data-epgdataclass=\"epg-sec";
                                echo $chanel->stream_id;
                                echo "\">\t             \n\t              <input type=\"hidden\" value='";
                                echo !empty($checkFavExists) ? "no" : "yes";
                                echo "' class=\"checkepgfav-";
                                echo $chanel->stream_id;
                                echo "\">\n\t              <a href=\"";
                                echo $chanel->stream_id;
                                echo "\"  data-toggle=\"tooltip\" title=\"";
                                echo $chanel->name;
                                echo "\" class=\"paychannelbtn\">\n\t              <img src=\"";
                                echo $chanelIcon;
                                echo "\" alt=\"Channel Logo\" class=\"channel-logo\" onerror=\"this.src='images/no-image-available.png';\" />\n\t              <span class=\"liveChannel-title\">";
                                echo $chanel->name;
                                echo "</span>\n\t              </a>\n\t            </li> \n\t            ";
                            } else {
                                if ($_POST["view"] == "tabview") {
                                    echo "\t            <li id=\"video";
                                    echo $counter;
                                    echo "\" class=\"streamList commomplayclick sectionNo";
                                    echo $chanel->stream_id;
                                    echo "\">\n\t                <input type=\"hidden\" class=\"streamId\" data-streamtype=\"live\" value=\"";
                                    echo $chanel->stream_id;
                                    echo "\">\n\t                <span class=\"serial-no\">";
                                    echo $chanel->num;
                                    echo "</span>\n\t                <span class=\"number\">\n\t                  <img src=\"";
                                    echo $chanelIcon;
                                    echo "\" class=\"live-channel-logo\" onerror=\"this.src='images/no-image-available.png';\">\n\t                </span>\n\t                <!-- <i class=\"fa fa-television\" aria-hidden=\"true\"></i> -->   \n\t                <input type=\"hidden\" class=\"serch_key\" value=\"";
                                    echo $chanel->name;
                                    echo "\" data-parentliclass=\"sectionNo";
                                    echo $chanel->stream_id;
                                    echo "\">\n\t                <label>\n\t                  <div class=\"name-sec\">\n\t                    ";
                                    if (strlen($chanel->name) <= 20) {
                                        echo $chanel->name;
                                    } else {
                                        echo substr($chanel->name, 0, 20) . "<span class='showfullname' title='" . $chanel->name . "'>...</span>";
                                    }
                                    echo "\t                  </div>\n\t                </label>\n\t                ";
                                    $focuscounter = $focuscounter + 1;
                                    $focuscountersub = $focuscounter - 1;
                                    echo "\t                <a href=\"#\" class=\"Playclick clickablebtn clicksec-";
                                    echo $focuscounter;
                                    echo "\"  data-streamidon=\"";
                                    echo $chanel->stream_id;
                                    echo "\">\n\t                   <i class=\"fa fa-play-circle playstyle-live\" aria-hidden=\"true\"></i>\n\t                </a>\n\t                ";
                                    if (!empty($conn)) {
                                        $checkFavExists = $clientcontrolfunctions->webtvpanel_getFavExists("live", $SessionStroedportallink, $chanel->stream_id, $conn);
                                        echo "\t                    <i \n\t                        class=\"fa fa-heart makeFav ";
                                        echo !empty($checkFavExists) ? "activefav" : "";
                                        echo " selectedfav\" \n\t                        aria-hidden=\"true\" \n\t                        data-favstreamid='";
                                        echo $chanel->stream_id;
                                        echo "'  \n\t                        data-cateid='";
                                        echo $chanel->category_id;
                                        echo "'  \n\t                        data-favstreamtype='";
                                        echo $chanel->stream_type;
                                        echo "'  \n\t                        data-favis='";
                                        echo !empty($checkFavExists) ? "no" : "yes";
                                        echo "'\n\t                        style='float: right;z-index:999999;margin: 5px 10px;'\n\t                    >                 \n\t                    </i>\n\t                  ";
                                    }
                                    echo "\t                \n\t            </li>\n\t            ";
                                }
                            }
                            $counter++;
                            $focuscounter++;
                        }
                        exit;
                    } else {
                        if (isset($_POST["action"]) && $_POST["action"] == "GetCaptchaByStreamid") {
                            $view = $_POST["view"];
                            $categoryID = $_POST["categoryID"];
                            $DatabaseObj = new DBConnect();
                            $conn = $DatabaseObj->makeconnection();
                            $CommonController = new CommonController();
                            $clientcontrolfunctions = new clientcontrolfunctions();
                            $getBlockedStreamsIts = [];
                            $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                            $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
                            $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "catchup", $categoryID);
                            if ($categoryID == "favorite") {
                                $GetFavforCategories = $clientcontrolfunctions->webtvpanel_getFavforCategories("catchup", $conn);
                                if (!empty($GetFavforCategories)) {
                                    foreach ($GetFavforCategories as $Favkey) {
                                        $StreamKeyIS = unserialize($Favkey["favdata"]);
                                        $Arrayforsorting[$StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                    }
                                } else {
                                    echo "<div class='noresult'>No Result Found!!</div>";
                                    exit;
                                }
                            } else {
                                $StreamData = $CommonController->getcatchupStream($categoryID);
                                if ($StreamData["result"] == "success") {
                                    $Arrayforsorting = [];
                                    foreach ($StreamData["data"] as $StreamKeyIS) {
                                        $BackdropArray = [];
                                        if (!empty($StreamKeyIS->backdrop_path)) {
                                            foreach ($StreamKeyIS->backdrop_path as $backVal) {
                                                $BackdropArray[] = $backVal;
                                            }
                                        }
                                        if (!in_array($StreamKeyIS->stream_id, $getBlockedStreamsIts)) {
                                            $Arrayforsorting[$SortingType == "topadded" ? $StreamKeyIS->added : $StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                        }
                                    }
                                    if ($SortingType == "topadded") {
                                        ksort($Arrayforsorting);
                                        $Arrayforsorting = array_reverse($Arrayforsorting, false);
                                    }
                                    if ($SortingType == "asc") {
                                        array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                                    }
                                    if ($SortingType == "desc") {
                                        array_multisort(array_keys($Arrayforsorting), SORT_NATURAL | SORT_FLAG_CASE, $Arrayforsorting);
                                        $Arrayforsorting = array_reverse($Arrayforsorting, false);
                                    }
                                }
                            }
                            if (!empty($Arrayforsorting)) {
                                $StreamData["data"] = $Arrayforsorting;
                                foreach ($StreamData["data"] as $chanel) {
                                    if ($chanel->tv_archive == 1) {
                                        $chanelIcon = $chanel->stream_icon;
                                        if ($chanelIcon == "") {
                                            $chanelIcon = "images/no_logo.jpg";
                                        }
                                        $checkFavExists = $clientcontrolfunctions->webtvpanel_getFavExists("catchup", $SessionStroedportallink, $chanel->stream_id, $conn);
                                        echo "\t            <li id=\"video";
                                        echo $counter;
                                        echo "\" class=\"streamList commomplayclick sectionNo";
                                        echo $chanel->stream_id;
                                        echo "\">\n\t                <input type=\"hidden\" class=\"streamId\" data-streamtype=\"catchup\" value=\"";
                                        echo $chanel->stream_id;
                                        echo "\">\n\t                <span class=\"serial-no\">";
                                        echo $chanel->num;
                                        echo "</span>\n\t                <span class=\"number\">\n\t                  <img src=\"";
                                        echo $chanelIcon;
                                        echo "\" class=\"live-channel-logo\" onerror=\"this.src='images/no-image-available.png';\">\n\t                </span>\n\t                <!-- <i class=\"fa fa-television\" aria-hidden=\"true\"></i> -->   \n\t                <input type=\"hidden\" class=\"serch_key\" value=\"";
                                        echo $chanel->name;
                                        echo "\" data-parentliclass=\"sectionNo";
                                        echo $chanel->stream_id;
                                        echo "\">\n\t                <label>\n\t                  <div class=\"name-sec\">\n\t                    ";
                                        if (strlen($chanel->name) <= 20) {
                                            echo $chanel->name;
                                        } else {
                                            echo substr($chanel->name, 0, 20) . "<span class='showfullname' title='" . $chanel->name . "'>...</span>";
                                        }
                                        echo "\t                  </div>\n\t                </label>\n\t                ";
                                        $focuscounter = $focuscounter + 1;
                                        $focuscountersub = $focuscounter - 1;
                                        echo "\t                <a href=\"#\" class=\"Playclick clickablebtn clicksec-";
                                        echo $focuscounter;
                                        echo "\"  data-streamidon=\"";
                                        echo $chanel->stream_id;
                                        echo "\">\n\t                   <i class=\"fa fa-play-circle playstyle-live\" aria-hidden=\"true\"></i>\n\t                   <i  class=\"fa fa-heart makeFav ";
                                        echo !empty($checkFavExists) ? "activefav" : "";
                                        echo "\"\n\t\t                    aria-hidden=\"true\" \n\t\t                    data-favstreamid=\"";
                                        echo $chanel->stream_id;
                                        echo "\" \n\t\t                    data-favstreamtype=\"catchup\" \n\t\t                    data-cateid='";
                                        echo $categoryID;
                                        echo "' \n\t\t                    data-favis=\"";
                                        echo !empty($checkFavExists) ? "no" : "yes";
                                        echo "\"  \n\t\t                    style=\"float: right;z-index:999999;margin-right:10px;margin-top: 5px;\">             \n\t\t                </i>\n\t                </a>\n\t                               \n\t            </li>\n\t            ";
                                        $counter++;
                                        $focuscounter++;
                                    }
                                }
                                exit;
                            } else {
                                echo "0";
                                exit;
                            }
                        } else {
                            if (isset($_POST["action"]) && $_POST["action"] == "saveliveviewsettings") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_saveliveviewsettings($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "saveliveplayersettings") {
                                $DatabaseObj = new DBConnect();
                                $return = "";
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_saveliveplayersettings($_POST, $conn);
                                }
                                echo $return;
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "saveepgtimeshiftsettings") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_saveepgtimeshiftsettings($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "savetimeformatsettings") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_savetimeformatsettings($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "saveparentPin") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_saveparentpin($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "confirmandpindelete") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_confirmandpindelete($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "checkoldandupdatepin") {
                                $DatabaseObj = new DBConnect();
                                $return = [];
                                $conn = $DatabaseObj->makeconnection();
                                if (!empty($conn)) {
                                    unset($_POST["action"]);
                                    $return = $funconn->webtvpanel_checkoldandupdatepin($_POST, $conn);
                                }
                                echo json_encode($return);
                                exit;
                            }
                            if (isset($_POST["action"]) && $_POST["action"] == "savefavdata") {
                                $return = [];
                                $favtype = $_POST["favtype"];
                                $favview = $_POST["favview"];
                                $favcateid = $_POST["favcateid"];
                                $favstreamId = $_POST["favstreamId"];
                                $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                                $favis = $_POST["favis"];
                                $DatabaseObj = new DBConnect();
                                $CommonController = new CommonController();
                                $conn = $DatabaseObj->makeconnection();
                                $fullFavData = [];
                                $Arrayforsorting = [];
                                if ($favtype == "live") {
                                    $fullFavData = [""];
                                    $faveDataType = $CommonController->getliveStream($favcateid);
                                    foreach ($faveDataType["data"] as $StreamKeyIS) {
                                        if ($favstreamId == $StreamKeyIS->stream_id) {
                                            $fullFavData = serialize($StreamKeyIS);
                                        }
                                    }
                                    if ($favis == "yes") {
                                        $return = $funconn->webtvpanel_saveFavdata($SessionStroedportallink, $favtype, $favstreamId, $fullFavData, $conn);
                                    } else {
                                        $return = $funconn->webtvpanel_delFavdata($_POST, $conn);
                                    }
                                    echo json_encode($return);
                                    exit;
                                } else {
                                    if ($favtype == "movies") {
                                        $fullFavData = [""];
                                        $faveDataType = $CommonController->getMoviesByCateGoryID($favcateid);
                                        foreach ($faveDataType["data"] as $StreamData) {
                                            if ($favstreamId == $StreamData->stream_id) {
                                                $Arrayforsorting[$StreamData->name] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => $StreamData->stream_type, "stream_id" => $StreamData->stream_id, "stream_icon" => $StreamData->stream_icon, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "category_id" => $StreamData->category_id, "added" => $StreamData->added, "container_extension" => $StreamData->container_extension, "direct_source" => $StreamData->direct_source];
                                            }
                                        }
                                        foreach ($Arrayforsorting as $Data) {
                                            $fullFavData = serialize($Data);
                                        }
                                        if ($favis == "yes") {
                                            $return = $funconn->webtvpanel_saveFavdata($SessionStroedportallink, $favtype, $favstreamId, $fullFavData, $conn);
                                        } else {
                                            $return = $funconn->webtvpanel_delFavdata($_POST, $conn);
                                        }
                                        echo json_encode($return);
                                        exit;
                                    } else {
                                        if ($favtype == "series") {
                                            $fullFavData = [""];
                                            $faveDataType = $CommonController->getSeriesByCateGoryID($favcateid);
                                            foreach ($faveDataType["data"] as $StreamData) {
                                                if ($favstreamId == $StreamData->series_id) {
                                                    $Arrayforsorting[$StreamData->name] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => "series", "series_id" => $StreamData->series_id, "cover" => $StreamData->cover, "plot" => $StreamData->plot, "cast" => $StreamData->cast, "director" => $StreamData->director, "genre" => $StreamData->genre, "releaseDate" => $StreamData->releaseDate, "last_modified" => $StreamData->last_modified, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "youtube_trailer" => $StreamData->youtube_trailer, "episode_run_time" => $StreamData->episode_run_time, "category_id" => $StreamData->category_id, "rating" => $StreamData->rating];
                                                }
                                            }
                                            foreach ($Arrayforsorting as $Data) {
                                                $fullFavData = serialize($Data);
                                            }
                                            if ($favis == "yes") {
                                                $return = $funconn->webtvpanel_saveFavdata($SessionStroedportallink, $favtype, $favstreamId, $fullFavData, $conn);
                                            } else {
                                                $return = $funconn->webtvpanel_delFavdata($_POST, $conn);
                                            }
                                            echo json_encode($return);
                                            exit;
                                        } else {
                                            if ($favtype == "radio") {
                                                $faveDataType = $CommonController->getliveStream($favcateid);
                                                foreach ($faveDataType["data"] as $StreamData) {
                                                    if ($favstreamId == $StreamData->stream_id) {
                                                        $Arrayforsorting[$SortingType == "topadded" ? $StreamData->added : $StreamData->name] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => $StreamData->stream_type, "stream_id" => $StreamData->stream_id, "stream_icon" => $StreamData->stream_icon, "epg_channel_id" => $StreamData->epg_channel_id, "added" => $StreamData->added, "category_id" => $StreamData->category_id, "custom_sid" => $StreamData->custom_sid, "tv_archive" => $StreamData->tv_archive, "direct_source" => $StreamData->direct_source, "tv_archive_duration" => $StreamData->tv_archive_duration];
                                                    }
                                                }
                                                foreach ($Arrayforsorting as $Data) {
                                                    $fullFavData = serialize($Data);
                                                }
                                                if ($favis == "yes") {
                                                    $return = $funconn->webtvpanel_saveFavdata($SessionStroedportallink, $favtype, $favstreamId, $fullFavData, $conn);
                                                } else {
                                                    $return = $funconn->webtvpanel_delFavdata($_POST, $conn);
                                                }
                                                echo json_encode($return);
                                                exit;
                                            } else {
                                                if ($favtype == "catchup") {
                                                    $faveDataType = $CommonController->getcatchupStream($favcateid);
                                                    foreach ($faveDataType["data"] as $StreamData) {
                                                        if ($favstreamId == $StreamData->stream_id) {
                                                            $Arrayforsorting[$StreamData->name] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => $StreamData->stream_type, "stream_id" => $StreamData->stream_id, "stream_icon" => $StreamData->stream_icon, "epg_channel_id" => $StreamData->epg_channel_id, "added" => $StreamData->added, "category_id" => $StreamData->category_id, "custom_sid" => $StreamData->custom_sid, "tv_archive" => $StreamData->tv_archive, "direct_source" => $StreamData->direct_source, "tv_archive_duration" => $StreamData->tv_archive_duration];
                                                        }
                                                    }
                                                    foreach ($Arrayforsorting as $Data) {
                                                        $fullFavData = serialize($Data);
                                                    }
                                                    if ($favis == "yes") {
                                                        $return = $funconn->webtvpanel_saveFavdata($SessionStroedportallink, $favtype, $favstreamId, $fullFavData, $conn);
                                                    } else {
                                                        $return = $funconn->webtvpanel_delFavdata($_POST, $conn);
                                                    }
                                                    echo json_encode($return);
                                                    exit;
                                                } else {
                                                    exit;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (isset($_POST["action"]) && $_POST["action"] == "getRadioStreamsFromID") {
                                    $counter = 0;
                                    $categoryID = $_POST["categoryID"];
                                    if ($categoryID == "radio_streams") {
                                        $categoryID = "all";
                                    }
                                    $DatabaseObj = new DBConnect();
                                    $conn = $DatabaseObj->makeconnection();
                                    $CommonController = new CommonController();
                                    $clientcontrolfunctions = new clientcontrolfunctions();
                                    $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                                    $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
                                    $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "radio", $categoryID);
                                    if ($categoryID == "favorite") {
                                        $GetFavforCategories = $clientcontrolfunctions->webtvpanel_getFavforCategories("radio", $conn);
                                        if (!empty($GetFavforCategories)) {
                                            foreach ($GetFavforCategories as $Favkey) {
                                                $StreamKeyIS = unserialize($Favkey["favdata"]);
                                                $Arrayforsorting[$StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                            }
                                        } else {
                                            echo "<div class='noresult'>No Result Found!!</div>";
                                            exit;
                                        }
                                    } else {
                                        $StreamData = $CommonController->getliveStream($categoryID);
                                        if ($StreamData["result"] == "success") {
                                            $Arrayforsorting = [];
                                            foreach ($StreamData["data"] as $StreamKeyIS) {
                                                if ($StreamKeyIS->stream_type == "radio_streams" && !in_array($StreamKeyIS->stream_id, $getBlockedStreamsIts)) {
                                                    $Arrayforsorting[$SortingType == "topadded" ? $StreamKeyIS->added : $StreamKeyIS->name] = (int) ["num" => $StreamKeyIS->num, "name" => $StreamKeyIS->name, "stream_type" => $StreamKeyIS->stream_type, "stream_id" => $StreamKeyIS->stream_id, "stream_icon" => $StreamKeyIS->stream_icon, "epg_channel_id" => $StreamKeyIS->epg_channel_id, "added" => $StreamKeyIS->added, "category_id" => $StreamKeyIS->category_id, "custom_sid" => $StreamKeyIS->custom_sid, "tv_archive" => $StreamKeyIS->tv_archive, "direct_source" => $StreamKeyIS->direct_source, "tv_archive_duration" => $StreamKeyIS->tv_archive_duration];
                                                }
                                            }
                                        }
                                    }
                                    if (!empty($Arrayforsorting)) {
                                        $StreamData["data"] = $Arrayforsorting;
                                        foreach ($StreamData["data"] as $chanel) {
                                            $chanelIcon = $chanel->stream_icon;
                                            if ($chanelIcon == "") {
                                                $chanelIcon = "images/no_logo.jpg";
                                            }
                                            $checkFavExists = $clientcontrolfunctions->webtvpanel_getFavExists("radio", $SessionStroedportallink, $chanel->stream_id, $conn);
                                            $counter++;
                                            echo "\t\t            <li id=\"video";
                                            echo $counter;
                                            echo "\" class=\"streamList rippler rippler-inverse sectionNo";
                                            echo $chanel->stream_id;
                                            echo "\" data-streamidon=\"";
                                            echo $chanel->stream_id;
                                            echo "\">\n\t\t              <input type=\"hidden\" class=\"streamId\" data-streamtype=\"";
                                            echo $chanel->stream_type;
                                            echo "\" value=\"";
                                            echo $chanel->stream_id;
                                            echo "\">\n\t\t              <span style=\"font-weight: bold;width: 50px;text-align: center;padding-top: 8px;\">";
                                            echo $chanel->num;
                                            echo "</span>\n\t\t                        <span class=\"number\"><img src=\"";
                                            echo $chanel->stream_icon;
                                            echo "\" height=\"30px\" onerror=\"this.src='images/no_logo.jpg'\"></span>                \n\t\t                        <input type=\"hidden\" class=\"serch_key\" value=\"";
                                            echo $chanel->name;
                                            echo "\" data-parentliclass=\"sectionNo";
                                            echo $chanel->stream_id;
                                            echo "\">\n\t\t                        <label>\n\t\t                          ";
                                            echo $chanel->name;
                                            echo "                          \n\t\t                        </label>\n\t\t                        <a href=\"#\" class=\"Playclick clickablebtn clicksec-";
                                            echo $counter;
                                            echo "\"  data-streamidon=\"";
                                            echo $chanel->stream_id;
                                            echo "\">\n\t\t                        \t<i class=\"fa fa-play-circle\" aria-hidden=\"true\" style=\"float: right;\"></i>\n\t                    \t\t</a>\n\t\t                \t\t<i  class=\"fa fa-heart makeFav ";
                                            echo !empty($checkFavExists) ? "activefav" : "";
                                            echo "\"\n\t\t\t\t                    aria-hidden=\"true\" \n\t\t\t\t                    data-favstreamid=\"";
                                            echo $chanel->stream_id;
                                            echo "\" \n\t\t\t\t                    data-favstreamtype=\"radio\" \n\t\t\t\t                    data-cateid='";
                                            echo $categoryID;
                                            echo "' \n\t\t\t\t                    data-favis=\"";
                                            echo !empty($checkFavExists) ? "no" : "yes";
                                            echo "\"  \n\t\t\t\t                    style=\"float: right;z-index:999999;margin-right:10px;\">             \n\t\t\t\t                </i>\n\t\t                \n\t\t            </li>       \n\t\t            ";
                                        }
                                        exit;
                                    } else {
                                        echo "<div class=''>No Radio channel Available !!</div>";
                                        exit;
                                    }
                                } else {
                                    if (isset($_POST["action"]) && $_POST["action"] == "searchQueryWithCatgoryId") {
                                        $activepage = $_POST["activepage"];
                                        $searchCate = $_POST["searchCate"];
                                        $searchCate = base64_decode($searchCate);
                                        $SearchData = $_POST["SearchData"];
                                        $DatabaseObj = new DBConnect();
                                        $conn = $DatabaseObj->makeconnection();
                                        $CommonController = new CommonController();
                                        $clientcontrolfunctions = new clientcontrolfunctions();
                                        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
                                        $getActivePortalID = $CommonController->getActivePortal($conn, $SessionStroedportallink);
                                        if ($activepage == "movies") {
                                            $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "movies", $searchCate);
                                            $MoviesResult = $CommonController->getMoviesByCateGoryID($searchCate);
                                            if ($MoviesResult["result"] == "success") {
                                                foreach ($MoviesResult["data"] as $StreamData) {
                                                    if (!in_array($StreamData->stream_id, $getBlockedStreamsIts) && preg_match(strtoupper("/" . $SearchData . "/"), strtoupper($StreamData->name))) {
                                                        $Arrayforsorting[$StreamData->added] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => $StreamData->stream_type, "stream_id" => $StreamData->stream_id, "stream_icon" => $StreamData->stream_icon, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "added" => $StreamData->added, "category_id" => $StreamData->category_id, "container_extension" => $StreamData->container_extension, "direct_source" => $StreamData->direct_source];
                                                    }
                                                }
                                                if (!empty($Arrayforsorting)) {
                                                    ksort($Arrayforsorting);
                                                    $totalRes = count($Arrayforsorting);
                                                    $Arrayforsorting = array_reverse($Arrayforsorting, true);
                                                    echo "\t        \t<div class=\"searchResult text-light mb-2 ml-2\">\n\t        \t\t<p style=\"font-size: 24px;\">";
                                                    echo $totalRes;
                                                    echo " Result Found for <em>'";
                                                    echo $SearchData;
                                                    echo "'</em></p>\n\t        \t</div>\n\t        \t<div class=\"view-stream\">\n\t        \t";
                                                    $Counter = 0;
                                                    foreach ($Arrayforsorting as $data) {
                                                        $Icon = $data->stream_icon;
                                                        if ($Icon == "") {
                                                            $Icon = "images/no_poster.png";
                                                        }
                                                        echo "        \t\t\t<div class=\"sliderimage-view ";
                                                        echo $Counter;
                                                        echo "\" >\n                        <img src=\"";
                                                        echo $Icon;
                                                        echo "\" onerror=\"this.src='images/no_poster.png';\" alt=\"\" class=\"view-images\"/>\n                        <div class=\"img-title\" onclick='viewdetails(\"";
                                                        echo $data->stream_id;
                                                        echo "\",\"";
                                                        echo $SortingType;
                                                        echo "\")'>\n                            <span class=\"movies-title\">";
                                                        echo $data->name;
                                                        echo "</span>\n                            <div class=\"rating\">";
                                                        echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                                                        echo "</div>\n                        </div>\n                    </div>\n        \t\t\t";
                                                        $Counter++;
                                                    }
                                                    echo "\t\t\t\t</div>\n\t\t\t  \t";
                                                    exit;
                                                } else {
                                                    echo "0";
                                                    exit;
                                                }
                                            } else {
                                                echo "0";
                                                exit;
                                            }
                                        } else {
                                            if ($activepage == "series") {
                                                $SeriesResult = $CommonController->getSeriesByCateGoryID($searchCate);
                                                $getBlockedStreamsIts = $CommonController->getBlockedStreamsIts($conn, $getActivePortalID, "series", $searchCate);
                                                if ($SeriesResult["result"] == "success") {
                                                    foreach ($SeriesResult["data"] as $StreamData) {
                                                        if (!in_array($StreamData->stream_id, $getBlockedStreamsIts) && preg_match(strtoupper("/" . $SearchData . "/"), strtoupper($StreamData->name))) {
                                                            $Arrayforsorting[$StreamData->last_modified] = (int) ["num" => $StreamData->num, "name" => $StreamData->name, "stream_type" => "series", "series_id" => $StreamData->series_id, "cover" => $StreamData->cover, "plot" => $StreamData->plot, "cast" => $StreamData->cast, "director" => $StreamData->director, "genre" => $StreamData->genre, "releaseDate" => $StreamData->releaseDate, "last_modified" => $StreamData->last_modified, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "youtube_trailer" => $StreamData->youtube_trailer, "episode_run_time" => $StreamData->episode_run_time, "category_id" => $StreamData->category_id, "rating" => $StreamData->rating];
                                                        }
                                                    }
                                                    if (!empty($Arrayforsorting)) {
                                                        ksort($Arrayforsorting);
                                                        $totalRes = count($Arrayforsorting);
                                                        $Arrayforsorting = array_reverse($Arrayforsorting, true);
                                                        echo "\t        \t<div class=\"searchResult text-light mb-2 ml-2\">\n\t        \t\t<p style=\"font-size: 24px;\">";
                                                        echo $totalRes;
                                                        echo " Result Found for <em>'";
                                                        echo $SearchData;
                                                        echo "'</em></p>\n\t        \t</div>\n\t        \t<div class=\"view-stream\">\n\t        \t";
                                                        $Counter = 0;
                                                        foreach ($Arrayforsorting as $data) {
                                                            $Icon = $data->cover;
                                                            if ($Icon == "") {
                                                                $Icon = "images/no_poster.png";
                                                                $coverup = "noposter";
                                                            }
                                                            echo "\n\t\t\t\t\t<div class=\"sliderimage-view ";
                                                            echo $Counter;
                                                            echo "\" >\n\t\t\t\t\t\t<img src=\"";
                                                            echo $Icon;
                                                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"view-images\"/>\n\t\t\t\t\t\t<div class=\"img-title\" onclick='viewdetails(\"";
                                                            echo $data->series_id;
                                                            echo "\",\"";
                                                            echo $SortingType;
                                                            echo "\")'>\n\t\t\t\t\t\t    <span class=\"movies-title\">";
                                                            echo $data->name;
                                                            echo "</span>\n\t\t\t\t\t\t    <div class=\"rating\">";
                                                            echo $clientcontrolfunctions->WebTVClient_starRating($data->rating_5based);
                                                            echo "</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
                                                            $Counter++;
                                                        }
                                                        echo "\t\t\t\t</div>\n\t\t\t  \t";
                                                    } else {
                                                        echo "0";
                                                        exit;
                                                    }
                                                } else {
                                                    echo "0";
                                                    exit;
                                                }
                                            }
                                            exit;
                                        }
                                    } else {
                                        if (isset($_POST["action"]) && $_POST["action"] == "getCategorieslist") {
                                            $categoryActive = $_POST["categoryID"] ? $_POST["categoryID"] : "";
                                            $sectionPage = $_POST["section"] ? $_POST["section"] : "";
                                            $hostURL = $_POST["hostURL"] ? $_POST["hostURL"] : "";
                                            $username = $_SESSION["webTvplayer"]["username"];
                                            $password = $_SESSION["webTvplayer"]["password"];
                                            $FinalCategoriesArray = [];
                                            $bar = "/";
                                            $cate = "";
                                            if (substr($hostURL, -1) == "/") {
                                                $bar = "";
                                            }
                                            if ($hostURL != "" && $sectionPage != "") {
                                                if ($sectionPage == "live") {
                                                    $cate = "live";
                                                } else {
                                                    if ($sectionPage == "movies") {
                                                        $cate = "vod";
                                                    } else {
                                                        if ($sectionPage == "series") {
                                                            $cate = "series";
                                                        } else {
                                                            if ($sectionPage == "radio") {
                                                                $cate = "live";
                                                            } else {
                                                                if ($sectionPage == "catchup") {
                                                                    $cate = "live";
                                                                    $pagelink = "";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                $getCategories_by_page = webtvpanel_CallApiRequest($hostURL . $bar . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_" . $cate . "_categories");
                                                $FinalCategoriesArray = $getCategories_by_page;
                                                echo "        \n        <div class=\"row\" id=\"channel_list\">\n\t\t\t";
                                                if ($cate != "live") {
                                                    echo "\t\t\t\t<div class=\"col-md-6 col-sm-6 col-xs-12\">\t\t\t\t\t\n\t\t\t\t\t<a href=\"";
                                                    echo $pagelink;
                                                    echo "?cate=all\" class=\"showChannel midfocus topmidleftfocus leftstop\" data-channelid=\"1\">\n\t\t\t\t\t\t<div class=\"cate-title bg-cat-custom p-3 m-2 ";
                                                    echo $categoryActive == "all" ? "active-Class" : "";
                                                    echo "\">\t\t\t\t\t\n\t\t\t\t\t\t\t<span class=\"channel-icon\"><img src=\"img/player_video_play.png\" style=\"height: 2rem\"></span>\n\t\t\t\t\t\t\t<h4>All <span class=\"float-right\"><img src=\"img/right_icon_cat.png\" style=\"height:2rem;\"></span></h4>\t\t\t\t\t\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</a>\t\t\t\t\n\t\t\t\t</div>\n\t\t\t\t";
                                                }
                                                if (!empty($conn)) {
                                                    echo "      <div class=\"col-md-6 col-sm-6 col-xs-12\">         \n        <a href=\"";
                                                    echo $pagelink;
                                                    echo "?cate=FAVOURITES\" class=\"showChannel midfocus topmidrightfocus ";
                                                    echo $cate != "live" ? "rightstop" : "leftstop";
                                                    echo "\" data-channelid=\"1\">\n          <div class=\"cate-title bg-cat-custom p-3 m-2 ";
                                                    echo $categoryActive == "FAVOURITES" ? "active-Class" : "";
                                                    echo "\">          \n            <span class=\"channel-icon\"><img src=\"img/player_video_play.png\" style=\"height: 2rem\"></span>\n            <h4>Favourites <span class=\"float-right\"><img src=\"img/right_icon_cat.png\" style=\"height:2rem;\"></span></h4>          \n          </div>\n        </a>        \n      </div>\n      ";
                                                }
                                                if (!empty($FinalCategoriesArray["data"]) && $FinalCategoriesArray["result"] == "success") {
                                                    $upperConditionofOdd = $cate != "live" ? "leftstop" : "rightstop";
                                                    $innerConditionofOdd = $cate != "live" ? "rightstop" : "leftstop";
                                                    $oddevencounter = 1;
                                                    foreach ($FinalCategoriesArray["data"] as $catkey) {
                                                        $sectionis = $upperConditionofOdd;
                                                        if ($oddevencounter % 2 == 0) {
                                                            $sectionis = $innerConditionofOdd;
                                                        }
                                                        echo "\t\t\t\t\t<div class=\"col-md-6 col-sm-6 col-xs-12\" data-test=\"";
                                                        echo $oddevencounter;
                                                        echo "\">\t\t\t\t\t\n\t\t\t\t\t\t<a href=\"";
                                                        echo $pagelink;
                                                        echo "?cate=";
                                                        echo $catkey->category_id;
                                                        echo "\" class=\"showChannel midfocus ";
                                                        echo $sectionis;
                                                        echo "\" data-channelid=\"1\">\n\t\t\t\t\t\t\t<div class=\"cate-title bg-cat-custom p-3 m-2 ";
                                                        echo $catkey->category_id == $categoryActive ? "active-Class" : "";
                                                        echo "\">\t\t\t\t\t\n\t\t\t\t\t\t\t\t<span class=\"channel-icon\"><img src=\"img/player_video_play.png\" style=\"height: 2rem\"></span>\n\t\t\t\t\t\t\t\t<h4>";
                                                        echo $catkey->category_name;
                                                        echo " <span class=\"float-right\"><img src=\"img/right_icon_cat.png\" style=\"height:2rem;\"></span></h4>\t\t\t\t\t\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</a>\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\t";
                                                        $oddevencounter++;
                                                    }
                                                }
                                                echo "\t\t</div>\n\t\t";
                                                exit;
                                            } else {
                                                echo "0";
                                                exit;
                                            }
                                        } else {
                                            if (isset($_POST["action"]) && $_POST["action"] == "CheckparentControl") {
                                                $return = ["result" => "error", "message" => "Pin Not Matched", "golink" => "live.php"];
                                                $ParentPin = $_POST["ParentPin"];
                                                $golink = $_POST["golink"];
                                                $startlink = $_POST["startlink"];
                                                $startlink = base64_decode($startlink);
                                                $Fullurl = $startlink . $golink;
                                                $DatabaseObj = new DBConnect();
                                                $clientcontrolfunctions = new clientcontrolfunctions();
                                                $conn = $DatabaseObj->makeconnection();
                                                $Getparentpinformart = $clientcontrolfunctions->webtvpanel_getparentpinformart($conn);
                                                if ($Getparentpinformart === $ParentPin) {
                                                    $return = ["result" => "success", "message" => "Pin matched", "golink" => $Fullurl];
                                                }
                                                echo json_encode($return);
                                                exit;
                                            }
                                            if (isset($_POST["action"]) && $_POST["action"] == "getcastinformation") {
                                                $DatabaseObj = new DBConnect();
                                                $conn = $DatabaseObj->makeconnection();
                                                $CommonController = new CommonController();
                                                $clientcontrolfunctions = new clientcontrolfunctions();
                                                $controlfunctions = new controlfunctions();
                                                $mainImageIS = "images/no_poster.png";
                                                $galleryphotos = [];
                                                $CastIMBDid = $_POST["CastIMBDid"];
                                                $name = "n/A";
                                                $birthday = "n/A";
                                                $known_for_department = "n/A";
                                                $place_of_birth = "n/A";
                                                $biography = "n/A";
                                                if ($CastIMBDid != "") {
                                                    $QueryData = ["request" => "Get", "table" => "webtvtheme_cast_information", "data" => ["tmbd_cast_id" => $CastIMBDid]];
                                                    $GetCastFromDB = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                    if (!empty($GetCastFromDB)) {
                                                        $birthday = isset($GetCastFromDB[0]["dob"]) && !empty($GetCastFromDB[0]["dob"]) ? $GetCastFromDB[0]["dob"] : "n/A";
                                                        $known_for_department = isset($GetCastFromDB[0]["profession"]) && !empty($GetCastFromDB[0]["profession"]) ? $GetCastFromDB[0]["profession"] : "n/A";
                                                        $name = isset($GetCastFromDB[0]["name"]) && !empty($GetCastFromDB[0]["name"]) ? $GetCastFromDB[0]["name"] : "n/A";
                                                        $biography = isset($GetCastFromDB[0]["bio"]) && !empty($GetCastFromDB[0]["bio"]) ? $GetCastFromDB[0]["bio"] : "n/A";
                                                        $place_of_birth = isset($GetCastFromDB[0]["placeofbirth"]) && !empty($GetCastFromDB[0]["placeofbirth"]) ? $GetCastFromDB[0]["placeofbirth"] : "n/A";
                                                        $profile_path = isset($GetCastFromDB[0]["profile_path"]) && !empty($GetCastFromDB[0]["profile_path"]) ? $GetCastFromDB[0]["profile_path"] : "";
                                                        if ($profile_path != "") {
                                                            $mainImageIS = "https://image.tmdb.org/t/p/original" . $profile_path;
                                                        }
                                                        $QueryData = ["request" => "Get", "table" => "webtvtheme_cast_gallery", "data" => ["tmbd_cast_id" => $CastIMBDid]];
                                                        $GetCastGalleryFromDB = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                        if (!empty($GetCastGalleryFromDB)) {
                                                            foreach ($GetCastGalleryFromDB as $ImageSrc) {
                                                                $galleryphotos[] = $ImageSrc["img_src"];
                                                            }
                                                        }
                                                    } else {
                                                        $checkCastData = $CommonController->CallApiRequest("https://api.themoviedb.org/3/person/" . $CastIMBDid . "?api_key=f584f73e8848d9ace559deee1e5a849f&language=en-US");
                                                        if ($checkCastData["result"] == "success") {
                                                            $birthday = isset($checkCastData["data"]->birthday) && !empty($checkCastData["data"]->birthday) ? $checkCastData["data"]->birthday : "n/A";
                                                            $known_for_department = isset($checkCastData["data"]->known_for_department) && !empty($checkCastData["data"]->known_for_department) ? $checkCastData["data"]->known_for_department : "n/A";
                                                            $name = isset($checkCastData["data"]->name) && !empty($checkCastData["data"]->name) ? $checkCastData["data"]->name : "n/A";
                                                            $biography = isset($checkCastData["data"]->biography) && !empty($checkCastData["data"]->biography) ? $checkCastData["data"]->biography : "n/A";
                                                            $place_of_birth = isset($checkCastData["data"]->place_of_birth) && !empty($checkCastData["data"]->place_of_birth) ? $checkCastData["data"]->place_of_birth : "n/A";
                                                            $profile_path = isset($checkCastData["data"]->profile_path) && !empty($checkCastData["data"]->profile_path) ? $checkCastData["data"]->profile_path : "";
                                                            if ($profile_path != "") {
                                                                $mainImageIS = "https://image.tmdb.org/t/p/original" . $profile_path;
                                                            }
                                                            $QueryData = ["request" => "Insert", "table" => "webtvtheme_cast_information", "data" => ["name" => $name, "tmbd_cast_id" => $CastIMBDid, "dob" => $birthday, "profession" => $known_for_department, "placeofbirth" => $place_of_birth, "bio" => $biography, "profile_path" => $profile_path]];
                                                            $InsertData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                            $GetCastGallaryData = $CommonController->CallApiRequest("https://api.themoviedb.org/3/person/" . $CastIMBDid . "/images?api_key=f584f73e8848d9ace559deee1e5a849f");
                                                            if ($GetCastGallaryData["result"] == "success" && !empty($GetCastGallaryData["data"]->profiles)) {
                                                                $GallCondition = 1;
                                                                foreach ($GetCastGallaryData["data"]->profiles as $galData) {
                                                                    if ($GallCondition < 6 && $galData->file_path != "") {
                                                                        $fgalpath = "https://image.tmdb.org/t/p/original" . $galData->file_path;
                                                                        $QueryData = ["request" => "Insert", "table" => "webtvtheme_cast_gallery", "data" => ["tmbd_cast_id" => $CastIMBDid, "img_src" => $fgalpath]];
                                                                        $InsertData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                                        $galleryphotos[] = $fgalpath;
                                                                        $GallCondition++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    echo "\t\t<div class=\"row\">\n\t\t\t<div class=\"col-md-2 watchPoster\">\n\t\t\t\t<div class=\"stream-icon\">\n\t          <img src=\"";
                                                    echo $mainImageIS;
                                                    echo "\" class=\"stream-image\">\n\t        </div>\n\t        <button class=\"btn btn-info mt-3\" onclick=\"backtoinformationstats()\">Back to Info</button>\n\t\t\t</div>\n\t\t\t\t<div class=\"col-md-10\">\n\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h5>Name :&nbsp;&nbsp;&nbsp; ";
                                                    echo $name;
                                                    echo "</h5>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h5>Date of birth :&nbsp;&nbsp;&nbsp;\n\t\t\t\t\t\t\t\t";
                                                    if ($birthday != "n/A") {
                                                        echo date("d F Y", strtotime($birthday));
                                                    } else {
                                                        echo $birthday;
                                                    }
                                                    echo "\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t</h5>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h5>Profession : &nbsp;&nbsp;&nbsp;";
                                                    echo $known_for_department;
                                                    echo "</h5>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h5>Place of birth :&nbsp;&nbsp;&nbsp; ";
                                                    echo $place_of_birth;
                                                    echo "</h5>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t<h5>Bio :</h5>\n\t\t\t\t\t\t\t<p>";
                                                    echo $biography;
                                                    echo "</p>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t";
                                                    if (!empty($galleryphotos)) {
                                                        echo "\t\t\t\t<div class=\"row slider-row castslidersection\">\n\t\t   \t\t\t<div class=\"col-md-12\">\n\t\t      \t\t\t<h3 class=\"related-slide\">Photos</h3>\n\t\t      \t\t\t<div class=\"view-slide\" id=\"viewSliderCast\">\n\t\t      \t\t\t";
                                                        foreach ($galleryphotos as $galphotos) {
                                                            echo "\t\n\t\t      \t\t\t\t<div class=\"viewslide-banner commomapicastpicture\">\n\t\t\t\t\t            <div class=\"sliderimage-div 0\">\n\t\t\t\t\t               <img src=\"";
                                                            echo $galphotos;
                                                            echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images cast-image defaultactivecustom\">\n\t\t\t\t\t            </div>\n\t\t\t\t\t         </div>\n\t\t         \t\t";
                                                        }
                                                        echo "\t\t      \t\t\t</div>\n\t\t      \t\t</div>\n\t\t      \t</div>\t\t\n\t\t\t\t";
                                                    }
                                                }
                                            }
                                            if (isset($_POST["action"]) && $_POST["action"] == "getcastpictureanddata") {
                                                $DatabaseObj = new DBConnect();
                                                $conn = $DatabaseObj->makeconnection();
                                                $CommonController = new CommonController();
                                                $clientcontrolfunctions = new clientcontrolfunctions();
                                                $controlfunctions = new controlfunctions();
                                                $firstOrignal = $_POST["castname"];
                                                $castname = $orignalName = strtoupper($firstOrignal);
                                                $castname = str_replace(" ", "%20", $castname);
                                                $type = $_POST["typeis"];
                                                $QueryData = ["request" => "Get", "table" => "webtvtheme_cast_container", "data" => ["cast_name" => $orignalName, "type" => $type]];
                                                $ShowCastImageIS = "images/no_poster.png";
                                                $newactiveclass = "defaultactivecustom";
                                                $CastTmbdID = "";
                                                $CastData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                if (!empty($CastData)) {
                                                    $ShowCastImageIS = isset($CastData[0]["image_path"]) && !empty($CastData[0]["image_path"]) ? $CastData[0]["image_path"] : $ShowCastImageIS;
                                                    $CastTmbdID = isset($CastData[0]["tmbd_cast_id"]) && !empty($CastData[0]["tmbd_cast_id"]) ? $CastData[0]["tmbd_cast_id"] : $ShowCastImageIS;
                                                } else {
                                                    $checkCastData = $CommonController->CallApiRequest("https://api.themoviedb.org/3/search/person?include_adult=false&page=1&query=" . $castname . "&language=en-US&api_key=f584f73e8848d9ace559deee1e5a849f");
                                                    if (isset($checkCastData["data"]->results[0]->profile_path)) {
                                                        $CastTmbdID = isset($checkCastData["data"]->results[0]->id) && !empty($checkCastData["data"]->results[0]->id) ? $checkCastData["data"]->results[0]->id : "";
                                                        $popularity = isset($checkCastData["data"]->results[0]->popularity) && !empty($checkCastData["data"]->results[0]->popularity) ? $checkCastData["data"]->results[0]->popularity : "";
                                                        if (!empty($popularity)) {
                                                            $popularity = round($popularity);
                                                        }
                                                        $newactiveclass = "";
                                                        $ShowCastImageIS = "https://image.tmdb.org/t/p/original" . $checkCastData["data"]->results[0]->profile_path;
                                                        $QueryData = ["request" => "Insert", "table" => "webtvtheme_cast_container", "data" => ["cast_name" => $orignalName, "tmbd_cast_id" => $CastTmbdID, "popularity" => $popularity, "type" => $type, "image_path" => $ShowCastImageIS]];
                                                        $InsertData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                                    }
                                                }
                                                echo "\t<div class=\"sliderimage-div ";
                                                echo $_POST["index"];
                                                echo "\" onclick=\"explorecastdata('";
                                                echo $CastTmbdID;
                                                echo "')\">\n\t<img src=\"";
                                                echo $ShowCastImageIS;
                                                echo "\" alt=\"\" onerror=\"this.src='images/no_poster.png';\" class=\"sec-images cast-image ";
                                                echo $newactiveclass;
                                                echo "\">\n\t<div class=\"img-title\">\n\t  <span class=\"movies-title\">";
                                                echo $firstOrignal;
                                                echo "</span>\n\t</div>\n\t</div>\n\t";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>