<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("AJAXCONROLERDIRPATHORGPAATH", dirname(dirname(__FILE__)) . "/");
define("AJAXCONROLERDIRPATH", dirname(dirname(dirname(dirname(__FILE__)))) . "/");
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
if (file_exists(AJAXCONROLERDIRPATHORGPAATH . "includes/functions.php")) {
    include_once AJAXCONROLERDIRPATHORGPAATH . "includes/functions.php";
}
if (isset($_POST["action"]) && $_POST["action"] == "webtvlogin") {
    $portablinkfromback = "";
    $Runtime = 0;
    $anyName = $_POST["anyname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $portalKey = isset($_POST["portalkey"]) && !empty($_POST["portalkey"]) ? $_POST["portalkey"] : "";
    $portallink = isset($_POST["portallink"]) && !empty($_POST["portallink"]) ? $_POST["portallink"] : "";
    $rememberMe = $_POST["rememberme"];
    $returnData = ["result" => "error", "message" => "Invalid Details"];
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->makeconnection();
    if (!empty($conn)) {
        $CommonController = new CommonController();
        $controlfunctions = new controlfunctions();
        if (isset($_POST["fromlist"]) && $_POST["fromlist"] == "listuser") {
            $password = $controlfunctions->webtvtheme_decrypt($password);
            $portablinkfromback = $controlfunctions->webtvtheme_decrypt($portallink);
        }
        $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
        if (isset($ConfigDetails["portallinks"]) && !empty($ConfigDetails["portallinks"])) {
            $PortalLinks = unserialize($ConfigDetails["portallinks"]);
            $newArrayportal = [];
            if (!empty($PortalLinks)) {
                foreach ($PortalLinks as $inname => $valportal) {
                    $bar = "/";
                    if (substr($valportal, -1) == "/") {
                        $bar = "";
                    }
                    $valportal = $valportal . $bar;
                    $newArrayportal[$inname] = $valportal;
                }
            }
            $PortalLinks = isset($newArrayportal) && !empty($newArrayportal) ? $newArrayportal : $PortalLinks;
            $Fportallink = "";
            if ($portablinkfromback != "") {
                $bar = "/";
                if (substr($portablinkfromback, -1) == "/") {
                    $bar = "";
                }
                $portablinkfromback = $portablinkfromback . $bar;
                if (!in_array($portablinkfromback, $PortalLinks)) {
                    $returnData = ["result" => "error", "message" => "Invalid Details"];
                    echo json_encode($returnData);
                    exit;
                }
                $Fportallink = $portablinkfromback;
            } else {
                if ($portalKey != "") {
                    if (isset($PortalLinks[$portalKey]) && !empty($PortalLinks[$portalKey])) {
                        $Fportallink = $PortalLinks[$portalKey];
                    }
                } else {
                    $indexaray = [];
                    $in = 0;
                    foreach ($PortalLinks as $identifire => $val) {
                        $indexaray[$in] = $identifire;
                        $in++;
                    }
                    $indexii = $indexaray[$Runtime];
                    $Fportallink = isset($PortalLinks[$indexii]) && $PortalLinks[$indexii] != "" ? $PortalLinks[$indexii] : "";
                }
            }
            if ($Fportallink != "") {
                $bar = "/";
                if (substr($Fportallink, -1) == "/") {
                    $bar = "";
                }
                $Fportallink = $Fportallink . $bar;
            }
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
                            $SessionArray = ["username" => $Result["data"]->user_info->username, "password" => $Result["data"]->user_info->password, "portallink" => $Fportallink, "auth" => $Result["data"]->user_info->auth, "status" => $Result["data"]->user_info->status, "exp_date" => $Result["data"]->user_info->exp_date, "active_cons" => $Result["data"]->user_info->active_cons, "is_trial" => $Result["data"]->user_info->is_trial, "max_connections" => $Result["data"]->user_info->max_connections, "created_at" => $Result["data"]->user_info->created_at, "allowed_output_formats" => $Result["data"]->user_info->allowed_output_formats, "url" => $Result["data"]->server_info->url, "port" => $Result["data"]->server_info->port, "rtmp_port" => $Result["data"]->server_info->rtmp_port, "anyname" => $anyName, "protheme" => "yes", "timezone" => $Result["data"]->server_info->timezone];
                            $_SESSION["webTvplayer"] = $SessionArray;
                            if (substr($Fportallink, -1) == "/") {
                                $bar = "";
                                $Fportallink = substr($Fportallink, 0, -1);
                            }
                            $Fportallink = $Fportallink . $bar;
                            $_SESSION["selectedhost"] = $Fportallink;
                            $enchosturl = $controlfunctions->webtvtheme_encrypt($Fportallink);
                            $returnData = ["result" => "success", "message" => $SessionArray, "detailsarr" => [$anyName => ["username" => $username, "password" => $EncPassword, "portallink" => $enchosturl]]];
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
    echo json_encode($returnData);
    exit;
} else {
    if (isset($_POST["action"]) && $_POST["action"] == "logoutProcess") {
        unset($_SESSION["webTvplayer"]);
        session_destroy();
        echo "1";
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "callApiRequest") {
        $Result = "";
        $Fportallink = isset($_SESSION["selectedhost"]) && !empty($_SESSION["selectedhost"]) ? $_SESSION["selectedhost"] : "";
        $bar = "/";
        if (substr($Fportallink, -1) == "/") {
            $bar = "";
        }
        $Fportallink = $Fportallink . $bar;
        $UserName = $_SESSION["webTvplayer"]["username"];
        $UserPassword = $_SESSION["webTvplayer"]["password"];
        $subaction = $_POST["subaction"];
        if (isset($_POST["chanelhisallow"]) && !empty($_POST["chanelhisallow"])) {
            $channelHisallowlive = $_POST["chanelhisallow"];
        } else {
            $channelHisallowlive = "";
        }
        if (isset($_POST["chanelwatchallow"]) && !empty($_POST["chanelwatchallow"])) {
            $channelWatchallow = $_POST["chanelwatchallow"];
        } else {
            $channelWatchallow = "";
        }
        $ApiLinkIs = $Fportallink . "player_api.php?username=" . $UserName . "&password=" . $UserPassword . "&action=" . $subaction;
        $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
        if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
            if ($subaction == "get_live_categories") {
                if ($channelHisallowlive != "") {
                    if ($channelHisallowlive == "true") {
                        $newCategoryIDArray[-2] = (int) ["category_id" => "all", "category_name" => "All", "parent_id" => "0"];
                        $newCategoryIDArray[-1] = (int) ["category_id" => "favourite", "category_name" => "Favourite", "parent_id" => "0"];
                    }
                } else {
                    $newCategoryIDArray[-3] = (int) ["category_id" => "all", "category_name" => "All", "parent_id" => "0"];
                    $newCategoryIDArray[-2] = (int) ["category_id" => "favourite", "category_name" => "Favourite", "parent_id" => "0"];
                    $newCategoryIDArray[-1] = (int) ["category_id" => "channels-history", "category_name" => "CHANNELS HISTORY", "parent_id" => "0"];
                }
                $merge = array_merge($newCategoryIDArray, $checkLogin["data"]);
                echo json_encode($merge);
            } else {
                if ($subaction == "get_vod_categories" || $subaction == "get_series_categories") {
                    if ($channelWatchallow != "") {
                        if ($channelWatchallow == "true") {
                            $newCategoryIDArray[-2] = (int) ["category_id" => "all", "category_name" => "All", "parent_id" => "0"];
                            $newCategoryIDArray[-1] = (int) ["category_id" => "favourite", "category_name" => "Favourite", "parent_id" => "0"];
                        }
                    } else {
                        $newCategoryIDArray[-3] = (int) ["category_id" => "all", "category_name" => "All", "parent_id" => "0"];
                        $newCategoryIDArray[-2] = (int) ["category_id" => "favourite", "category_name" => "Favourite", "parent_id" => "0"];
                        $newCategoryIDArray[-1] = (int) ["category_id" => "continue-watching", "category_name" => "CONTINUE WATCHING", "parent_id" => "0"];
                    }
                    $merge = array_merge($newCategoryIDArray, $checkLogin["data"]);
                    echo json_encode($merge);
                } else {
                    $Result = json_encode($checkLogin["data"]);
                }
            }
        } else {
            $Result = json_encode(["result" => "error"]);
        }
        echo $Result;
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "callApiRequestEpg") {
        $Formatis = "h:i A";
        if (isset($_POST["tformat"]) && $_POST["tformat"] == "24") {
            $Formatis = "H:i";
        }
        $epgtimeshft = "0";
        if (isset($_POST["etimes"]) && !empty($_POST["etimes"])) {
            $epgtimeshft = $_POST["etimes"];
        }
        $Fportallink = isset($_SESSION["selectedhost"]) && !empty($_SESSION["selectedhost"]) ? $_SESSION["selectedhost"] : "";
        $bar = "/";
        if (substr($Fportallink, -1) == "/") {
            $bar = "";
        }
        $Fportallink = $Fportallink . $bar;
        $UserName = $_SESSION["webTvplayer"]["username"];
        $UserPassword = $_SESSION["webTvplayer"]["password"];
        $subaction = $_POST["subaction"];
        $ApiLinkIs = $Fportallink . "player_api.php?username=" . $UserName . "&password=" . $UserPassword . "&action=" . $subaction;
        parse_str($subaction, $newarray);
        $onlystreamid = isset($newarray["stream_id"]) && !empty($newarray["stream_id"]) ? $newarray["stream_id"] : "";
        $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
        if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"]->epg_listings)) {
            $newpcoffset = "<script>new Date().getTimezoneOffset();</script>";
            if ($epgtimeshft < 0) {
                $epgshiftimer = abs($epgtimeshft);
            } else {
                $epgshiftimer = abs($epgtimeshft);
            }
            $countertogetfirst = 1;
            foreach ($checkLogin["data"]->epg_listings as $epgdata) {
                $starttime = strtotime($epgdata->start);
                $endtime = strtotime($epgdata->end);
                $firstclassis = "";
                if ($countertogetfirst == 1) {
                    $firstclassis = "firstepgis-" . $onlystreamid;
                }
                echo "\t\t\t<div class=\"liveEpgTime ";
                echo $firstclassis;
                echo "\" data-titleis=\"";
                echo base64_decode($epgdata->title);
                echo "\" style=\"padding: 10px 0px 0px 0px\">\n\t\t\t\t<span >";
                if ($epgtimeshft < 0) {
                    echo date($Formatis, $starttime - 3600 * abs($epgtimeshft));
                } else {
                    echo date($Formatis, $starttime + 3600 * abs($epgtimeshft));
                }
                echo " - ";
                if ($epgtimeshft < 0) {
                    echo date($Formatis, $endtime - 3600 * abs($epgtimeshft));
                } else {
                    echo date($Formatis, $endtime + 3600 * abs($epgtimeshft));
                }
                echo "  &nbsp; ";
                echo base64_decode($epgdata->title);
                echo "</span>\n                <p>\n                \t";
                echo base64_decode($epgdata->description);
                echo "                </p>\n\t\t\t</div>\n\t\t\t";
                $countertogetfirst++;
            }
            exit;
        } else {
            $Result = "<center class='notfoundepg'><h4 class='notfoundepg'> </h4></center>";
            echo $Result;
            exit;
        }
    } else {
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForCastRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "movie/" . $subaction . "?api_key=" . $tmdbtokan . "&append_to_response=credits";
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForPersonInfoRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "person/" . $subaction . "?api_key=" . $tmdbtokan;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForPersonImagesRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "person/" . $subaction . "/images?api_key=" . $tmdbtokan;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForSeriNameRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $subaction = str_replace(" ", "%20", $subaction);
            $ApiLinkIs = $tmdburl . "search/tv?api_key=" . $tmdbtokan . "&query=" . $subaction;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForSeriCastRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "tv/" . $subaction . "/credits?api_key=" . $tmdbtokan;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForPersonInfoRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "person/" . $subaction . "?api_key=" . $tmdbtokan;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiForPersonImagesRequest") {
            $XCtmdbtokan = "f584f73e8848d9ace559deee1e5a849f";
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            if (!empty($conn)) {
                $CommonController = new CommonController();
                $controlfunctions = new controlfunctions();
                $ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
                if ($ConfigDetails["tbmdid"] && !empty($ConfigDetails["tbmdid"])) {
                    $XCtmdbtokan = $ConfigDetails["tbmdid"];
                }
            }
            $tmdburl = "https://api.themoviedb.org/3/";
            $tmdbtokan = $XCtmdbtokan;
            $subaction = $_POST["subaction"];
            $ApiLinkIs = $tmdburl . "person/" . $subaction . "/images?api_key=" . $tmdbtokan;
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"])) {
                $Result = json_encode($checkLogin["data"]);
            } else {
                $Result = json_encode(["result" => "error"]);
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiRequestFullEPG") {
            $bar = "/";
            $Fportallink = isset($_SESSION["selectedhost"]) && !empty($_SESSION["selectedhost"]) ? $_SESSION["selectedhost"] : "";
            if (substr($Fportallink, -1) == "/") {
                $bar = "";
            }
            $Fportallink = $Fportallink . $bar;
            $UserName = $_SESSION["webTvplayer"]["username"];
            $UserPassword = $_SESSION["webTvplayer"]["password"];
            $subaction = $_POST["subaction"];
            $StreamId = $_POST["streamId"];
            $ApiLinkIs = $Fportallink . "player_api.php?username=" . $UserName . "&password=" . $UserPassword . "&action=" . $subaction . "&stream_id=" . $StreamId;
            parse_str($subaction, $newarray);
            $onlystreamid = isset($newarray["stream_id"]) && !empty($newarray["stream_id"]) ? $newarray["stream_id"] : "";
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"]->epg_listings)) {
                $Result = json_encode($checkLogin["data"]->epg_listings);
            } else {
                $Result = "<center class='notfoundepg'><h4 class='notfoundepg'>No Epg Found</h4></center>";
            }
            echo $Result;
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "callApiRequestsepg") {
            $runtime = "1";
            $Formatis = "h:i A";
            if (isset($GlobalTimeFormat) && $GlobalTimeFormat == "24") {
                $Formatis = "H:i";
            }
            $Fportallink = isset($_SESSION["selectedhost"]) && !empty($_SESSION["selectedhost"]) ? $_SESSION["selectedhost"] : "";
            $bar = "/";
            if (substr($Fportallink, -1) == "/") {
                $bar = "";
            }
            $Fportallink = $Fportallink . $bar;
            $UserName = isset($_SESSION["webTvplayer"]["username"]) && !empty($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $UserPassword = isset($_SESSION["webTvplayer"]["password"]) && !empty($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $subaction = $_POST["subaction"];
            $streamid = $_POST["streamid"];
            $ApiLinkIs = $Fportallink . "player_api.php?username=" . $UserName . "&password=" . $UserPassword . "&action=" . $subaction . "&stream_id=" . $streamid;
            parse_str($subaction, $newarray);
            $onlystreamid = isset($newarray["stream_id"]) && !empty($newarray["stream_id"]) ? $newarray["stream_id"] : "";
            $checkLogin = webtvpanel_CallApiRequest($ApiLinkIs);
            if (isset($checkLogin["result"]) && $checkLogin["result"] == "success" && !empty($checkLogin["data"]->epg_listings)) {
                $CurrentPcDateTime = new DateTime($_POST["currentTime"]);
                $CurrentTime = $CurrentPcDateTime->getTimestamp();
                $night12AM = strtotime(date("Ymd", $CurrentTime));
                $RequestForEpg = $checkLogin;
                if (!empty($RequestForEpg["data"]->epg_listings)) {
                    $OnlyDate = date("Y:m:d");
                    $totalvaidEPG = 1;
                    foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                        $OnlyDateVal = date("Y:m:d", strtotime($ResVal->start));
                        if ($OnlyDateVal == $OnlyDate) {
                            $StartTimming = $ResVal->start_timestamp;
                            $EndTimming = $ResVal->stop_timestamp;
                            $timedifference = ($EndTimming - $StartTimming) / 60;
                            $widthbymins = $timedifference * 8;
                            $ACtiveClass = "notactive";
                            $NowPLaying = "";
                            $NowPLayingselector = "";
                            if ($StartTimming <= $CurrentTime && $CurrentTime <= $EndTimming) {
                                $ACtiveClass = "NowPlayingActive";
                                $NowPLaying = "(Now Playing)";
                                $NowPLayingselector = "NowPLayingselector-" . $runtime;
                            }
                            $Checking = date("h:i", $StartTimming) . " - " . date("h:i", $EndTimming) . " - " . $timedifference . " - " . $StartTimming . " - " . $CurrentTime . " - " . $EndTimming;
                            $gettimefotheader = date("h:i", $StartTimming) . " - " . date("h:i", $EndTimming);
                            if ($totalvaidEPG == 1) {
                                $starttimedifference = ($StartTimming - $night12AM) / 60;
                                if (0 < $starttimedifference) {
                                    $startwidthbymins = $starttimedifference * 8;
                                    echo "\t\t\t\t\t        <div class=\"programme noneresult ";
                                    echo $ACtiveClass;
                                    echo "\" style=\"width: ";
                                    echo $startwidthbymins;
                                    echo "px;\" >\n\t\t\t\t\t           <input type=\"hidden\" value=\"";
                                    echo $Checking;
                                    echo "\" data-orgstrstart=\"";
                                    echo $ResVal->start_timestamp;
                                    echo "\" data-orgstrstart=\"";
                                    echo $ResVal->stop_timestamp;
                                    echo "\" >\n\t\t\t\t\t           <a href=\"#\" class=\"inner-excepta\" data-epgdescription=\"";
                                    echo base64_decode($ResVal->description);
                                    echo "\" data-streamselector=\"";
                                    echo $streamid;
                                    echo "\" data-epgtitle=\"";
                                    echo base64_decode($ResVal->title);
                                    echo "\">\n\t\t\t\t\t              <h6 class=\"title\">";
                                    echo base64_decode($ResVal->title);
                                    echo "</h6>\n\t\t\t\t\t           </a>\n\t\t\t\t\t          </div>\n\n\t\t\t\t\t    ";
                                }
                            }
                            echo "\t                <div class=\"programme successfound ";
                            echo $ACtiveClass;
                            echo " prgrmstream-";
                            echo $streamid;
                            echo "\" style=\"width: ";
                            echo $widthbymins;
                            echo "px;\" data-timefotH=\"";
                            echo $gettimefotheader;
                            echo "\">\n\t                  <input type=\"hidden\" value=\"";
                            echo $Checking;
                            echo "\" data-orgstrstart=\"";
                            echo $ResVal->start_timestamp;
                            echo "\" data-orgstrstart=\"";
                            echo $ResVal->stop_timestamp;
                            echo "\" >\n\t                    <a href=\"#\" class=\"inner-excepta ";
                            echo $NowPLayingselector;
                            echo "\" data-epgdescription=\"";
                            echo base64_decode($ResVal->description);
                            echo "\" data-streamselector=\"";
                            echo $streamid;
                            echo "\" data-epgtitle=\"";
                            echo base64_decode($ResVal->title);
                            echo "\">\n\t                      <h6 class=\"title\">\n\t                        ";
                            echo base64_decode($ResVal->title);
                            echo "\t                      </h6>\n\t                    </a>\n\t                  </div>\n\t              ";
                            $totalvaidEPG++;
                        }
                    }
                }
            } else {
                echo "\t\t<li>\n\t\t\t";
                for ($i = 0; $i <= 48; $i++) {
                    echo "\t\t\t\t\n\t\t            <div class=\"programme successfound\">\n\t\t                No Information\n\t\t            </div>\n\t\t         ";
                }
                echo "\t\t</li>\n\t\t";
            }
            exit;
        } else {
            if (isset($_POST["action"]) && $_POST["action"] == "GetCaptchaEPGByStreamid") {
                echo "\t<style type=\"text/css\">\n        select.form-control.custom-selectboz {\n\t\t    padding: 10px !important;\n\t\t    color: #fff;\n\t\t    background: url(images/whitearrow.png) 94% 58% !important;\n\t\t    background-repeat: no-repeat !important;\n\t\t    background-size: 26px !important;\n\t\t}\n\t\tselect.form-control.custom-selectboz * {\n\t\t    border-radius: 15px;\n\t\t    background-color: #000000;\n\t\t    color: #fff;\n\t\t    padding: 10px !important;\n\t\t}\n\t\t.hidetabcustom{\n\t\t\tdisplay: none !important;\n\t\t}\n\t\timg.placycatchup {\n\t\t    width: 80%;\n\t\t    float: right;\n\t\t}\n    </style>\n\t";
                $CurrentPcDateTime = new DateTime($_POST["currentTime"]);
                $CurrentTime = $CurrentPcDateTime->getTimestamp();
                $Formatis = "h:i A";
                if (isset($_POST["tformat"]) && $_POST["tformat"] == "24") {
                    $Formatis = "H:i";
                }
                $epgtimeshft = "0";
                if (isset($_POST["etimes"]) && !empty($_POST["etimes"])) {
                    $epgtimeshft = $_POST["etimes"];
                }
                $StreamId = $_POST["StreamId"];
                $currentthemename = $_POST["currentthemename"];
                $CommonController = new CommonController();
                $clientcontrolfunctions = new clientcontrolfunctions();
                $RequestForEpg = $CommonController->getEpgDataByCateGoryID($StreamId);
                if (!empty($RequestForEpg) && $RequestForEpg["result"] == "success") {
                    $CurrentDate = date("Y:m:d");
                    $OnlyDates = [];
                    foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                        if ($ResVal->has_archive == 1) {
                            $OnlyDateVar = date("Y:m:d", strtotime($ResVal->start));
                            $ValDate = date("d/m/Y", strtotime($ResVal->start));
                            if ($OnlyDateVar <= $CurrentDate) {
                                $OnlyDates[$OnlyDateVar] = $ValDate;
                            }
                        }
                    }
                    if (!empty($OnlyDates)) {
                        $OnlyDates = array_reverse($OnlyDates);
                        $TotalDates = count($OnlyDates);
                        $Counter = 1;
                        echo "            \n            <script type=\"text/javascript\">\n            \$(document).ready(function(){\n            \t\$(\".tabchanger\").change(function(){\n            \t\t\$(\".commontabsection\").addClass(\"hidetabcustom\");\n            \t\tvalis = \$(this).val();\n            \t\t\$(\".tabnumber-\"+valis).removeClass(\"hidetabcustom\");\n            \t});\n            });\n            </script>\n            <div class=\"row\">\n            \t<div class=\"col-md-12 col-sm-12 col-12 col-xl-12\" style=\"padding: 5px !important;\">\n            \t\t<select class=\"form-control custom-selectboz tabchanger\">\n\t\t\t\t\t";
                        foreach ($OnlyDates as $OnlyDate => $Val) {
                            echo "\t\t\t\t\t\t<option value=\"";
                            echo $Counter;
                            echo "\" >";
                            echo $Val;
                            echo "</option>\n\t\t\t\t\t\t";
                            $Counter++;
                        }
                        echo "\t\t      \t</select>\n            \t</div>\n            \t<div class=\"col-md-12 col-sm-12 col-12 col-xl-12 catchuperscrol\" style=\"padding: 5px !important;overflow: auto;height:348px;\">\n            \t\t";
                        if ($epgtimeshft < 0) {
                            $epgshiftimer = abs($epgtimeshft);
                        } else {
                            $epgshiftimer = abs($epgtimeshft);
                        }
                        $TabCounter = 1;
                        $CaptchaCounter = 1;
                        foreach ($OnlyDates as $OnlyDate => $Val) {
                            $activeclass = "hidetabcustom";
                            if ($TabCounter == 1) {
                                $activeclass = "";
                            }
                            echo "\t\t\t\t\t\t<div class=\"col-md-12 col-sm-12 col-12 col-xl-12 commontabsection tabnumber-";
                            echo $TabCounter;
                            echo " ";
                            echo $activeclass;
                            echo "\">\n\t\t\t\t\t\t\t\t";
                            foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                                if ($ResVal->has_archive == 1) {
                                    $OnlyDateVal = date("Y:m:d", strtotime($ResVal->start));
                                    if ($OnlyDateVal == $OnlyDate) {
                                        $starttime = strtotime($ResVal->start);
                                        $endtime = strtotime($ResVal->end);
                                        $interval = abs($endtime - $starttime);
                                        $minutes = round($interval / 60);
                                        echo "  \n\n\t\t\t\t\t\t\t\t\t\t\t<div class=\"liveEpgTime\" data-titleis=\"";
                                        echo base64_decode($ResVal->title);
                                        echo "\" style=\"\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row cpparent-";
                                        echo $CaptchaCounter;
                                        echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-11 col-sm-11 col-11 col-xl-11\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span >\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                        if ($epgtimeshft < 0) {
                                            echo date($Formatis, $starttime - 3600 * abs($epgtimeshft));
                                        } else {
                                            echo date($Formatis, $starttime + 3600 * abs($epgtimeshft));
                                        }
                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t- \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                        if ($epgtimeshft < 0) {
                                            echo date($Formatis, $endtime - 3600 * abs($epgtimeshft));
                                        } else {
                                            echo date($Formatis, $endtime + 3600 * abs($epgtimeshft));
                                        }
                                        echo "  &nbsp; ";
                                        echo base64_decode($epgdata->title);
                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t \t&nbsp; ";
                                        echo base64_decode($ResVal->title);
                                        echo " \n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t\t\t\t                \t";
                                        echo base64_decode($ResVal->description);
                                        echo "\t\t\t\t\t\t\t\t\t\t                </p>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1 col-sm-1 col-1 col-xl-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<img src=\"themes/";
                                        echo $currentthemename;
                                        echo "/images/play.png\" class=\"placycatchup ";
                                        echo $ACtiveClass;
                                        echo " cp-";
                                        echo $CaptchaCounter;
                                        echo "\" data-timediff=\"";
                                        echo $minutes;
                                        echo "\" data-starttime=\"";
                                        echo date("Y-m-d:H-i", $starttime);
                                        echo "\" data-streamid=\"";
                                        echo $StreamId;
                                        echo "\" data-cpcounter=\"";
                                        echo $CaptchaCounter;
                                        echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t\t\t";
                                        $CaptchaCounter++;
                                    }
                                }
                            }
                            echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t\t";
                            $TabCounter++;
                        }
                        echo "            \t</div>\n            </div>           \n            ";
                    } else {
                        echo "        \t <div class=\"row\">\n            \t<div class=\"col-md-12 col-sm-12 col-12 col-xl-12\" style=\"padding: 5px !important;\">\n            \t\t<center class=\"notfoundepg\"><h4 class=\"notfoundepg\">No Catch UP Found </h4></center>\n        \t \t</div>\n        \t </div>\n        \t";
                    }
                    exit;
                } else {
                    echo "\t\t <div class=\"row\">\n            \t<div class=\"col-md-12 col-sm-12 col-12 col-xl-12\" style=\"padding: 5px !important;\">\n            \t\t<center class=\"notfoundepg\"><h4 class=\"notfoundepg\"> No Catch UP Found </h4></center>\n        \t \t</div>\n        \t </div>\n\t\t";
                    exit;
                }
            }
        }
    }
}

?>