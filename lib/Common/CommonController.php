<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("CLIENTCONTROLLERABSPATH3", dirname(dirname(dirname(__FILE__))) . "/");
class CommonController
{
    public $funconn = NULL;
    public $adminfunconn = NULL;
    public $phpmailercall = NULL;
    public function __construct()
    {
        if (file_exists(CLIENTCONTROLLERABSPATH3 . "includes/functions.php")) {
            include_once CLIENTCONTROLLERABSPATH3 . "includes/functions.php";
            $this->funconn = new clientcontrolfunctions();
        }
        if (file_exists(CLIENTCONTROLLERABSPATH3 . "admin/includes/functions.php")) {
            include_once CLIENTCONTROLLERABSPATH3 . "admin/includes/functions.php";
            $this->adminfunconn = new controlfunctions();
        }
        if (file_exists(CLIENTCONTROLLERABSPATH3 . "admin/phpmailer/src/PHPMailer.php")) {
            include_once CLIENTCONTROLLERABSPATH3 . "admin/phpmailer/src/PHPMailer.php";
            include_once CLIENTCONTROLLERABSPATH3 . "admin/phpmailer/src/SMTP.php";
            include_once CLIENTCONTROLLERABSPATH3 . "admin/phpmailer/src/Exception.php";
            $this->phpmailercall = new PHPMailer\PHPMailer\PHPMailer();
        }
    }
    public function updatelocalkey($NewLocalKey, $conn = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Update", "table" => "webtvtheme_settings", "data" => ["settings" => "localKey"], "updatedata" => ["value" => $NewLocalKey]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if ($ExecuteQuery["result"] == "success") {
                $returnData = $ExecuteQuery;
            }
        }
        return $returnData;
    }
    public function getconfigurationoption($conn = "", $format = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_settings", "data" => []];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if ($format == "") {
                $returnData = $ExecuteQuery;
            } else {
                if (!empty($ExecuteQuery)) {
                    foreach ($ExecuteQuery as $ConfigFetch) {
                        $returnData[$ConfigFetch["settings"]] = $ConfigFetch["value"];
                    }
                }
            }
        }
        return $returnData;
    }
    public function checkportalvalid($portallink = "")
    {
        $returnData = [];
        if (!empty($portallink)) {
            $ApiLinkIs = $portallink;
            $http = curl_init($ApiLinkIs);
            curl_setopt($http, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($http, CURLOPT_SSL_VERIFYHOST, 0);
            $result = curl_exec($http);
            $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
            curl_close($http);
            $returnData = $http_status;
        }
        return $returnData;
    }
    public function CallApiRequest($ApiLinkIs = "")
    {
        $returnData = "0";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ApiLinkIs);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (curl_exec($ch) === false) {
            return ["result" => "error", "data" => "Invalid Host Url"];
        }
        $Result = json_decode(curl_exec($ch));
        if (!empty($Result)) {
            $returnData = $Result;
            return ["result" => "success", "data" => $returnData];
        }
        return ["result" => "error"];
    }
    public function checklicense($licensekey, $localkey = "")
    {
    $results["status"] = "Active";
    return $results;
    }
    public function client_ipaddress()
    {
        $ipaddress = "";
        if (getenv("HTTP_CLIENT_IP")) {
            $ipaddress = getenv("HTTP_CLIENT_IP");
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ipaddress = getenv("HTTP_X_FORWARDED_FOR");
            } else {
                if (getenv("HTTP_X_FORWARDED")) {
                    $ipaddress = getenv("HTTP_X_FORWARDED");
                } else {
                    if (getenv("HTTP_FORWARDED_FOR")) {
                        $ipaddress = getenv("HTTP_FORWARDED_FOR");
                    } else {
                        if (getenv("HTTP_FORWARDED")) {
                            $ipaddress = getenv("HTTP_FORWARDED");
                        } else {
                            if (getenv("REMOTE_ADDR")) {
                                $ipaddress = getenv("REMOTE_ADDR");
                            } else {
                                $ipaddress = "UNKNOWN";
                            }
                        }
                    }
                }
            }
        }
        return $ipaddress;
    }
    public function getCategoriesBySection($section = "")
    {
        $returnData = [];
        if (!empty($section)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = "";
            if ($section == "live") {
                $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_categories";
            }
            if ($section == "movies") {
                $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_categories";
            }
            if ($section == "series") {
                $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series_categories";
            }
            if ($section == "catchup") {
                $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams";
            }
            if ($section == "radio") {
                $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams";
            }
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getMoviesByCateGoryID($categoryid = "")
    {
        $returnData = [];
        if (!empty($categoryid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_streams&category_id=" . $categoryid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getMoviesInfo($categoryid = "", $streamid = "")
    {
        $returnData = [];
        if (!empty($categoryid) && !empty($streamid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_info&vod_id=" . $streamid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getSeriesInfo($categoryid = "", $streamid = "")
    {
        $returnData = [];
        if (!empty($categoryid) && !empty($streamid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series_info&series_id=" . $streamid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getliveStream($categoryid = "")
    {
        $returnData = [];
        if (!empty($categoryid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $categoryid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getcatchupStream($categoryid = "")
    {
        $returnData = [];
        if (!empty($categoryid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $categoryid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getEpgDataByCateGoryID($StreamId = "")
    {
        $returnData = [];
        if (!empty($StreamId)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_simple_data_table&stream_id=" . $StreamId;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getSeriesByCateGoryID($categoryid = "")
    {
        $returnData = [];
        if (!empty($categoryid)) {
            $PortalLink = isset($_SESSION["webTvplayer"]["portallink"]) ? $_SESSION["webTvplayer"]["portallink"] : "";
            $username = isset($_SESSION["webTvplayer"]["username"]) ? $_SESSION["webTvplayer"]["username"] : "";
            $password = isset($_SESSION["webTvplayer"]["password"]) ? $_SESSION["webTvplayer"]["password"] : "";
            $bar = "/";
            if (substr($PortalLink, -1) == "/") {
                $bar = "";
            }
            $PortalLink = $PortalLink . $bar;
            $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series&category_id=" . $categoryid;
            if ($FinalLink != "") {
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function checkblockedip($conn = "")
    {
        $returnData = "1";
        if (!empty($conn)) {
            $currenttime = time();
            $clientIPaddress = $this->client_ipaddress();
            $QueryData = ["request" => "Get", "table" => "webtvtheme_blockedips", "data" => ["ipaddress" => $clientIPaddress]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $returnData = "0";
                $blockedon = isset($ExecuteQuery[0]["created_on"]) && $ExecuteQuery[0]["created_on"] != "" ? $ExecuteQuery[0]["created_on"] : "";
                $lastblockedtime = strtotime($blockedon);
                $hoursdiff = ceil(round($currenttime - $lastblockedtime) / 3600);
                if (24 <= $hoursdiff) {
                    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $clientIPaddress]];
                    $this->adminfunconn->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blockedips", "data" => ["ipaddress" => $clientIPaddress]];
                    $this->adminfunconn->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                    $returnData = "1";
                }
            }
        }
        return $returnData;
    }
    public function getblockedipsaddresslist($conn = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $currenttime = time();
            $clientIPaddress = $this->client_ipaddress();
            $QueryData = ["request" => "Get", "table" => "webtvtheme_blockedips", "data" => [], "extra" => ["ORDER BY id DESC"]];
            $returnData = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
        }
        return $returnData;
    }
    public function DeleteBlockedIdsWithIdsArray($ExplodedIDS = [], $conn = "")
    {
        $returnData = 0;
        if (!empty($conn) && !empty($ExplodedIDS)) {
            $TotalsIDS = count($ExplodedIDS);
            foreach ($ExplodedIDS as $IDis) {
                $QueryData = ["request" => "Get", "table" => "webtvtheme_blockedips", "data" => ["id" => $IDis]];
                $RecordData = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (isset($RecordData[0]["ipaddress"]) && $RecordData[0]["ipaddress"] != "") {
                    $clientIPaddress = $RecordData[0]["ipaddress"];
                    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $clientIPaddress]];
                    $this->adminfunconn->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blockedips", "data" => ["ipaddress" => $clientIPaddress]];
                    $this->adminfunconn->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                }
            }
            $returnData = 1;
        }
        return $returnData;
    }
    public function addActivityOnload($conn = "")
    {
        $returnData = 0;
        if (!empty($conn)) {
            $currenttime = time();
            $clientIPaddress = $this->client_ipaddress();
            $QueryData = ["request" => "Get", "table" => "webtvtheme_activitylogs", "data" => ["ipaddress" => $clientIPaddress]];
            $RecordData = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($RecordData)) {
                $ID = isset($RecordData[0]["id"]) && $RecordData[0]["id"] != "" ? $RecordData[0]["id"] : "";
                if ($ID != "") {
                    $QueryData = ["request" => "Update", "table" => "webtvtheme_activitylogs", "data" => ["id" => $ID], "updatedata" => ["lastactive" => $currenttime]];
                    $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                }
            } else {
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_activitylogs", "data" => ["ipaddress" => $clientIPaddress, "lastactive" => $currenttime]];
                $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            }
        }
        return $returnData;
    }
    public function getloggeduserslist($conn = "", $limit = "", $offset = "", $user = "", $portal = "", $status = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $string = [];
            if ($user != "") {
                $string["username"] = $user;
            }
            if ($portal != "") {
                $string["portallink"] = $portal;
            }
            if ($status != "") {
                $string["status"] = $status;
            }
            $currenttime = time();
            $clientIPaddress = $this->client_ipaddress();
            $QueryData = ["request" => "Count", "table" => "webtvtheme_userdetails", "data" => $string, "extra" => []];
            $TotalRecords = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            $QueryData = ["request" => "Get", "table" => "webtvtheme_userdetails", "data" => $string, "extra" => ["ORDER BY id DESC", "LIMIT " . $offset . "," . $limit]];
            $returnData["total"] = $TotalRecords;
            $returnData["data"] = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
        }
        return $returnData;
    }
    public function getloggedusersfulllist($conn = "", $limit = "", $offset = "", $uid)
    {
        $returnData = [];
        if (!empty($conn)) {
            $currenttime = time();
            $clientIPaddress = $this->client_ipaddress();
            $QueryData = ["request" => "Count", "table" => "webtvtheme_log", "data" => ["user_id" => $uid], "extra" => []];
            $TotalRecords = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            $QueryData = ["request" => "Get", "table" => "webtvtheme_log", "data" => ["user_id" => $uid], "extra" => ["ORDER BY id DESC", "LIMIT " . $offset . "," . $limit]];
            $returnData["total"] = $TotalRecords;
            $returnData["data"] = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
        }
        return $returnData;
    }
    public function checkCurrentUserStatus($conn = "", $limit = "", $offset = "")
    {
        $returnData = "Active";
        if (!empty($conn)) {
            $username = $_SESSION["webTvplayer"]["username"];
            $Pass = $_SESSION["webTvplayer"]["password"];
            $EncPassword = $this->adminfunconn->webtvtheme_encrypt($Pass);
            $Fportallink = $_SESSION["webTvplayer"]["portallink"];
            $bar = "/";
            if (substr($Fportallink, -1) == "/") {
                $bar = "";
            }
            $Fportallink = $Fportallink . $bar;
            $QueryData = ["request" => "Get", "table" => "webtvtheme_userdetails", "data" => ["username" => $username, "password" => $EncPassword, "portallink" => $Fportallink]];
            $UserData = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($UserData)) {
                $returnData = $UserData[0]["status"];
            }
        }
        return $returnData;
    }
    public function getWorkingTestlineByListID($conn = "", $ListID = "")
    {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $portallink = $portallink . $bar;
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $CallApiRequest = $portallink . "player_api.php?username=" . $username . "&password=" . $password;
                $APIresponse = $this->CallApiRequest($CallApiRequest);
                $Result = $APIresponse;
                if ($Result["result"] == "success" && isset($Result["data"]->user_info->auth) && $Result["data"]->user_info->auth != 0 && $Result["data"]->user_info->status == "Active") {
                    $returnData = ["result" => "success", "message" => "Valid Details", "insertid" => base64_encode($ExecuteQuery[0]["id"]), "portallink" => $portallink];
                }
            }
        }
        return $returnData;
    }
    public function GetBannersListByCateGoryData($conn = "", $ListID = "", $section = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $FinalCateGoryWithCOunt = [];
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $FullQuery = "SELECT category,  COUNT(category) as totalresult FROM webtvtheme_banners WHERE portalurl = '" . mysqli_real_escape_string($conn, $PortalLink) . "' AND type = '" . mysqli_real_escape_string($conn, $section) . "' GROUP BY category";
                $QueryData = ["request" => "FullCustomQuery", "query" => $FullQuery];
                $result = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (0 < $result->num_rows) {
                    while ($row = $result->fetch_assoc()) {
                        $returnData[$row["category"]] = $row["totalresult"];
                    }
                }
            }
        }
        return $returnData;
    }
    public function getCategoriesBySectionAndListID($conn = "", $ListID = "", $section = "")
    {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $FinalLink = "";
                if ($section == "live") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_categories";
                }
                if ($section == "movies") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_categories";
                }
                if ($section == "series") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series_categories";
                }
                if ($section == "catchup") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams";
                }
                if ($section == "radio") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams";
                }
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getStreamsByCateIDSectionAndListID($conn = "", $ListID = "", $section = "", $categoryid = "")
    {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $FinalLink = "";
                if ($section == "live") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $categoryid;
                }
                if ($section == "movies") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_streams&category_id=" . $categoryid;
                }
                if ($section == "series") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series&category_id=" . $categoryid;
                }
                if ($section == "catchup") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $categoryid;
                }
                if ($section == "radio") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $categoryid;
                }
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function GetSingleStreamDataByStreamIDListID($conn = "", $ListID = "", $section = "", $StreamID = "")
    {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $FinalLink = "";
                if ($section == "movies") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_info&vod_id=" . $StreamID;
                }
                if ($section == "series") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series_info&series_id=" . $StreamID;
                }
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function BannerSliderFromExternalAPI($name = "", $type = "")
    {
        $name = str_replace(" ", "%20", $name);
        $ApiLinkIs = "https://api.themoviedb.org/3/search/tv?api_key=f584f73e8848d9ace559deee1e5a849f&query=" . $name;
        if ($type == "movies") {
            $ApiLinkIs = "https://api.themoviedb.org/3/search/movie?api_key=f584f73e8848d9ace559deee1e5a849f&query=" . $name;
        }
        $GetSliderFromTMBD = $this->CallApiRequest($ApiLinkIs);
        if ($GetSliderFromTMBD["result"] == "success" && !empty($GetSliderFromTMBD["data"]->results)) {
            if ($GetSliderFromTMBD["data"]->results[0]->backdrop_path != "") {
                return "https://image.tmdb.org/t/p/w1280" . $GetSliderFromTMBD["data"]->results[0]->backdrop_path;
            }
            return "";
        }
    }
    public function GetBlockedDataByPortalLInk($conn = "", $PortalLInk = "")
    {
        $returndata = [];
        if (!empty($conn)) {
            if (substr($PortalLInk, -1) == "/") {
                $bar = "";
            }
            $portallink = $PortalLInk . $bar;
            $QueryData = ["request" => "Get", "table" => "webtvtheme_blocked_section", "data" => ["portallink" => $portallink]];
            $GetData = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($GetData)) {
                foreach ($GetData as $Sval) {
                    $returndata[$Sval["section"]] = $Sval["section"];
                }
            }
        }
        return $returndata;
    }
    public function getBlockedCategoriesIts($conn = "", $ListID = "", $section = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $QueryData = ["request" => "Get", "table" => "webtvtheme_blocked_categories", "data" => ["type" => $section, "portallink" => $PortalLink]];
                $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (!empty($ExecuteQuery)) {
                    foreach ($ExecuteQuery as $getkey) {
                        $returnData[] = $getkey["category_id"];
                    }
                }
            }
        }
        return $returnData;
    }
    public function getBlockedSeriesEpisode($conn = "", $ListID = "", $StreamIDis = "", $categoryIs = "", $episodesid = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $QueryData = ["request" => "Get", "table" => "webtvtheme_blocked_Seriesstreams", "data" => ["streams_id" => $StreamIDis, "category_id" => $categoryIs, "episode_id" => $episodesid, "portallink" => $PortalLink]];
                $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (!empty($ExecuteQuery)) {
                    foreach ($ExecuteQuery as $getkey) {
                        $returnData[] = $getkey["episode_id"];
                    }
                }
            }
        }
        return $returnData;
    }
    public function GetStreamsByCategoryAndSecForBlock($conn = "", $ListID = "", $section = "", $category = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $FinalLink = $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_live_streams&category_id=" . $category;
                if ($section == "movies") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_vod_streams&category_id=" . $category;
                }
                if ($section == "series") {
                    $FinalLink = $PortalLink . "player_api.php?username=" . $username . "&password=" . $password . "&action=get_series&category_id=" . $category;
                }
                $returnData = $this->CallApiRequest($FinalLink);
            }
        }
        return $returnData;
    }
    public function getBlockedStreamsIts($conn = "", $ListID = "", $section = "", $cate = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $ListID]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $portallink = $ExecuteQuery[0]["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $PortalLink = $portallink . $bar;
                $QueryData = ["request" => "Get", "table" => "webtvtheme_blocked_streams", "data" => ["category_id" => $cate, "section" => $section, "portallink" => $PortalLink]];
                $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
                if (!empty($ExecuteQuery)) {
                    foreach ($ExecuteQuery as $getkey) {
                        $returnData[] = $getkey["streams_id"];
                    }
                }
            }
        }
        return $returnData;
    }
    public function getActivePortal($conn = "", $portallink = "")
    {
        $returnData = "";
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["portallink" => $portallink]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $this->adminfunconn->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $ApiRequestCall = $portallink . "player_api.php?username=" . $username . "&password=" . $password;
                $APIresponse = $this->CallApiRequest($ApiRequestCall);
                $Result = $APIresponse;
                if ($Result["result"] == "success" && isset($Result["data"]->user_info->auth) && $Result["data"]->user_info->auth != 0 && $Result["data"]->user_info->status == "Active") {
                    $returnData = $ExecuteQuery[0]["id"];
                }
            }
        }
        return $returnData;
    }
    public function GetThemeActivationCode($conn = "", $theme = "")
    {
        $returnData = "";
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_theme_activation", "data" => ["theme" => $theme]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $returnData = isset($ExecuteQuery[0]["code"]) && !empty($ExecuteQuery[0]["code"]) ? $ExecuteQuery[0]["code"] : "";
            }
        }
        return $returnData;
    }
    public function CheckThemeActivationCode($conn = "", $ThemeCodeIS = "", $theme = "", $LicenseIS = "")
    {
        $returnData = "Active";
        return $returnData;
    }
    public function GenerateThemeActivationCodesForDefault($conn = "", $license = "")
    {
        $returnData = "Active";
        return $returnData;
    }
    public function GetCastDataByTypeCount($conn = "", $Type = "")
    {
        $returnData = "0";
        if (!empty($conn)) {
            $QueryData = ["request" => "Count", "table" => "webtvtheme_cast_container", "data" => ["type" => $Type]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $returnData = $ExecuteQuery;
            }
        }
        return $returnData;
    }
    public function GetCastDataByTypeCountSearch($conn = "", $Type = "", $filtertext = "")
    {
        $returnData = "0";
        if (!empty($conn)) {
            $QueryData = ["request" => "FullCustomQuery", "query" => "SELECT * FROM `webtvtheme_cast_container` WHERE `cast_name` LIKE '%" . $filtertext . "%' AND `type` LIKE '" . $Type . "'"];
            $result = $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (0 < $result->num_rows) {
                $returnData = $result->num_rows;
            }
        }
        return $returnData;
    }
    public function GetCastDataByTypeSearch($conn = "", $Type = "", $filtertext = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "FullCustomQuery", "query" => "SELECT * FROM `webtvtheme_cast_container` WHERE `cast_name` LIKE '%" . $filtertext . "%' AND `type` LIKE '" . $Type . "' ORDER BY popularity DESC"];
            $result = $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (0 < $result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $returnData[] = $row;
                }
            }
        }
        return $returnData;
    }
    public function GetCastDataByType($conn = "", $Type = "", $offset = "0", $Limit = "40", $Orderby = "ORDER BY cast_name ASC")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_cast_container", "data" => ["type" => $Type], "extra" => [$Orderby, "LIMIT " . $offset . "," . $Limit]];
            $ExecuteQuery = $this->adminfunconn->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $returnData = $ExecuteQuery;
            }
        }
        return $returnData;
    }
    public function SendEmailcall($configdata = "", $to = "", $subject = "", $message = "")
    {
        if (isset($configdata["smtphost"]) && !empty($configdata["smtphost"])) {
            $smtphost = isset($configdata["smtphost"]) && !empty($configdata["smtphost"]) ? $configdata["smtphost"] : "";
            $smtpport = isset($configdata["smtpport"]) && !empty($configdata["smtpport"]) ? $configdata["smtpport"] : "";
            $smtpusername = isset($configdata["smtpusername"]) && !empty($configdata["smtpusername"]) ? $configdata["smtpusername"] : "";
            $smtppassword = isset($configdata["smtppassword"]) && !empty($configdata["smtppassword"]) ? $configdata["smtppassword"] : "";
            $smtyssltype = isset($configdata["smtyssltype"]) && !empty($configdata["smtyssltype"]) ? $configdata["smtyssltype"] : "";
            $decodedpass = $this->adminfunconn->webtvtheme_decrypt($smtppassword);
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->From = $smtpusername;
            $mail->FromName = $smtpusername;
            $mail->Host = $smtphost;
            $mail->Port = $smtpport;
            $mail->Hostname = "Testing Email";
            $mail->SMTPSecure = $smtyssltype;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpusername;
            $mail->Password = $decodedpass;
            $mail->Sender = $smtpusername;
            $mail->AddAddress(trim($to));
            $mail->Subject = $subject;
            $message = $message;
            $mail->Body = $message;
            $mail->IsHTML(true);
            $mail->IsSMTP();
            if ($mail->smtpConnect()) {
                if (!$mail->Send()) {
                }
            }
            $mail->ClearAddresses();
        }
    }
    public function checkSMTPDetails($smtpdetails = [])
    {
        $return = "Invalid SMTP Details";
        if (!empty($smtpdetails)) {
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = trim($smtpdetails["smtphost"]);
            $mail->SMTPAuth = true;
            $mail->Username = trim($smtpdetails["smtpusername"]);
            $mail->Password = trim($smtpdetails["smtppassword"]);
            $mail->SMTPSecure = trim($smtpdetails["smtyssltype"]);
            $mail->Port = trim($smtpdetails["smtpport"]);
            $mail->isHTML(true);
            if (!$mail->smtpConnect()) {
                $return = "Invalid SMTP Details";
            } else {
                $return = "Connected";
            }
        }
        return $return;
    }
}

?>