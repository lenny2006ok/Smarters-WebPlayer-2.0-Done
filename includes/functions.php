<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("CLIENTFUNCTIONCONTROLLERABSPATH", dirname(dirname(dirname(__FILE__))) . "/");
class clientcontrolfunctions
{
    public function createrecommendedtablesclients($conn = [])
    {
    }
    public function getcurrentenvoirment($conn = [])
    {
        $returnData = "production";
        $QueryData = ["request" => "Get", "table" => "webtvtheme_settings", "data" => ["settings" => "environment"]];
        $settingData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($settingData)) {
            $returnData = $settingData[0]["value"];
        }
        return $returnData;
    }
    public function getcurrentwebtvplayerversion($conn = [])
    {
        $returnData = "1.6";
        $QueryData = ["request" => "Get", "table" => "webtvtheme_settings", "data" => ["settings" => "version"]];
        $settingData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($settingData)) {
            $returnData = $settingData[0]["value"];
        }
        return $returnData;
    }
    public function getcurrenttheme($conn = [])
    {
        $returnData = "default";
        $QueryData = ["request" => "Get", "table" => "webtvtheme_settings", "data" => ["settings" => "theme"]];
        $settingData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($settingData)) {
            $returnData = $settingData[0]["value"];
        }
        return $returnData;
    }
    public function WebTVClient_ExecuteQuery($QueryData, $conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $tableName = $QueryData["table"];
            $FinalQueryToExicute = "";
            if ($QueryData["request"] == "Get") {
                $FinalQueryToExicute .= "SELECT * FROM " . $tableName;
                if (isset($QueryData["data"]) && !empty($QueryData["data"])) {
                    $eachCounter = 1;
                    foreach ($QueryData["data"] as $KeyColumn => $val) {
                        $lastval = mysqli_real_escape_string($conn, $val);
                        if ($eachCounter == 1) {
                            $FinalQueryToExicute .= " WHERE " . $KeyColumn . " = '" . $lastval . "'";
                        } else {
                            $FinalQueryToExicute .= " AND " . $KeyColumn . " = '" . $lastval . "'";
                        }
                        $eachCounter++;
                    }
                }
                $FinalQueryToExicute .= ";";
                $result = $conn->query($FinalQueryToExicute);
                if (0 < $result->num_rows) {
                    while ($row = $result->fetch_assoc()) {
                        $returnData[] = $row;
                    }
                }
                return $returnData;
            } else {
                if ($QueryData["request"] == "Insert") {
                    $FinalQueryToExicute .= "INSERT INTO " . $tableName;
                    $columnline .= " (";
                    $dataline .= " VALUES (";
                    if (isset($QueryData["data"]) && !empty($QueryData["data"])) {
                        $totalColumn = count($QueryData["data"]);
                        $eachCounter = 1;
                        foreach ($QueryData["data"] as $KeyColumn => $val) {
                            $lastval = mysqli_real_escape_string($conn, $val);
                            $comma = ",";
                            if ($totalColumn == $eachCounter) {
                                $comma = "";
                            }
                            $columnline .= $KeyColumn . $comma;
                            $dataline .= "'" . $val . "'" . $comma;
                            $eachCounter++;
                        }
                        $FinalQueryToExicute .= $columnline . ")";
                        $FinalQueryToExicute .= $dataline . ")";
                    }
                    $FinalQueryToExicute .= ";";
                    $result = $conn->query($FinalQueryToExicute);
                    if ($result) {
                        $lastinsertid = mysqli_insert_id($conn);
                        $returnData["result"] = "success";
                        $returnData["insert_id"] = $lastinsertid;
                    }
                    return $returnData;
                } else {
                    if ($QueryData["request"] == "Delete") {
                        $FinalQueryToExicute .= "DELETE FROM " . $tableName;
                        if (isset($QueryData["data"]) && !empty($QueryData["data"])) {
                            $eachCounter = 1;
                            foreach ($QueryData["data"] as $KeyColumn => $val) {
                                $lastval = mysqli_real_escape_string($conn, $val);
                                if ($eachCounter == 1) {
                                    $FinalQueryToExicute .= " WHERE " . $KeyColumn . " = '" . $lastval . "'";
                                } else {
                                    $FinalQueryToExicute .= " AND " . $KeyColumn . " = '" . $lastval . "'";
                                }
                                $eachCounter++;
                            }
                        }
                        $FinalQueryToExicute .= ";";
                        $result = $conn->query($FinalQueryToExicute);
                        if (0 < $result->num_rows) {
                            $returnData["result"] = "success";
                        }
                        return $returnData;
                    } else {
                        if ($QueryData["request"] == "Update") {
                            $FinalQueryToExicute .= "UPDATE " . $tableName . " SET ";
                            if (isset($QueryData["updatedata"]) && !empty($QueryData["updatedata"])) {
                                $totalUpdateData = count($QueryData["updatedata"]);
                                $upCounter = 1;
                                foreach ($QueryData["updatedata"] as $KeyColumn => $val) {
                                    $commasel = ",";
                                    if ($upCounter == $totalUpdateData) {
                                        $commasel = "";
                                    }
                                    $lastval = mysqli_real_escape_string($conn, $val);
                                    $FinalQueryToExicute .= " " . $KeyColumn . " = '" . $lastval . "' " . $commasel;
                                    $upCounter++;
                                }
                            }
                            if (isset($QueryData["data"]) && !empty($QueryData["data"])) {
                                $eachCounter = 1;
                                foreach ($QueryData["data"] as $KeyColumn => $val) {
                                    $lastval = mysqli_real_escape_string($conn, $val);
                                    if ($eachCounter == 1) {
                                        $FinalQueryToExicute .= " WHERE " . $KeyColumn . " = '" . $lastval . "'";
                                    } else {
                                        $FinalQueryToExicute .= " AND " . $KeyColumn . " = '" . $lastval . "'";
                                    }
                                    $eachCounter++;
                                }
                            }
                            $FinalQueryToExicute .= ";";
                            $result = $conn->query($FinalQueryToExicute);
                            if ($result) {
                                $returnData["result"] = "success";
                            }
                            return $returnData;
                        }
                    }
                }
            }
        }
    }
    public function WebTVClient_starRating($rating = "")
    {
        if (is_float($rating)) {
            $floatVal = explode(".", $rating);
            $j = 0;
            for ($i = 0; $i < $floatVal[0]; $i++) {
                $j++;
                echo "<span class=\"fas fa-star yellow\"></span>";
            }
            if (5 <= $floatVal[1] || $floatVal[1] <= 5) {
                $j++;
                echo "<span class=\"fas fa-star-half-alt yellow\"></span>";
            }
            for ($remainigStar = 5 - intval($j); $j < 5; $j++) {
                echo "<span class=\"far fa-star yellow-empty\"></span>";
            }
        } else {
            $j = 0;
            for ($i = 0; $i < $rating; $i++) {
                $j++;
                echo "<span class=\"fas fa-star yellow\"></span>";
            }
            for ($remainigStar = 5 - intval($j); $j < 5; $j++) {
                echo "<span class=\"far fa-star yellow-empty\"></span>";
            }
        }
    }
    public function webtvpanel_noEPGresultfoundprogram($streamid = "")
    {
        $returnData = "";
        for ($i = 1; $i <= 48; $i++) {
            $returnData .= "<div class=\"programme noneresult notactive \">\n\t\t        <input type=\"hidden\" value=\"" . $Checking . "\" >\n\t\t                                     <a href=\"#\" class=\"inner-excepta\" data-epgdescription=\"No Data Found!!\" data-streamselector=\"" . $streamid . "\" data-epgtitle=\"No Epg Data Found!!\">\n\t\t                                        <h6 class=\"title\">No Information Found!!</h6>\n\t\t                                     </a>\n\t\t                                </div>";
        }
        return $returnData;
    }
    public function webtvpanel_getplayersettingsdata($type = "", $conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_playersettings", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "type" => $type]];
        $playersettingsData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($playersettingsData)) {
            $returnData = $playersettingsData[0]["data"];
        }
        return $returnData;
    }
    public function webtvpanel_saveliveplayersettings($playerdetails = [], $conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = "";
        $sql = "SELECT * FROM webtvtheme_playersettings WHERE username = '" . $SessionStroedUsername . "' AND portalink = '" . $SessionStroedportallink . "'";
        $result = $conn->query($sql);
        if (0 < $result->num_rows) {
            $sql = "DELETE FROM webtvtheme_playersettings WHERE username = '" . $SessionStroedUsername . "' AND portalink = '" . $SessionStroedportallink . "' ";
            $deleteRecored = mysqli_query($conn, $sql);
        }
        $sql = "INSERT INTO webtvtheme_playersettings (type,username,portalink,data) VALUES ";
        $checkcounter = 1;
        foreach ($playerdetails as $playerdata => $key) {
            $lastSign = ",";
            if ($checkcounter == 3) {
                $lastSign = ";";
            }
            $sql .= "('" . $playerdata . "','" . $SessionStroedUsername . "','" . $SessionStroedportallink . "','" . $key . "')" . $lastSign;
            $checkcounter++;
        }
        if (mysqli_query($conn, $sql)) {
            $returnData = "1";
        }
        return $returnData;
    }
    public function webtvpanel_getepgtimeshifting($conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_epgtime_shift", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $epgtimeshiftingData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($epgtimeshiftingData)) {
            $returnData = $epgtimeshiftingData[0]["data"];
        }
        return $returnData;
    }
    public function webtvpanel_saveepgtimeshiftsettings($postdata = [], $conn = [])
    {
        $epgtimeshift = $postdata["epgtimeshift"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_epgtime_shift", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $epgtime_shiftData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($epgtime_shiftData)) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_epgtime_shift", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
            $Deleteepgtime_shiftData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $Deleteepgtime_shiftData;
        }
        $QueryData = ["request" => "Insert", "table" => "webtvtheme_epgtime_shift", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $epgtimeshift]];
        $Insertepgtime_shiftData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        $returnData = $Insertepgtime_shiftData;
        return $returnData;
    }
    public function webtvpanel_gettimeformart($conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_time_format", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $time_formatData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($time_formatData)) {
            $returnData = $time_formatData[0]["data"];
        }
        return $returnData;
    }
    public function webtvpanel_savetimeformatsettings($postdata = [], $conn = [])
    {
        $timeformat = $postdata["timeformat"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_time_format", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $timeformatData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($timeformatData)) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_time_format", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
            $DeletetimeformatData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $DeletetimeformatData;
        }
        $QueryData = ["request" => "Insert", "table" => "webtvtheme_time_format", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $timeformat]];
        $InserttimeformatData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        $returnData = $InserttimeformatData;
        return $returnData;
    }
    public function webtvpanel_getparentpinformart($conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $parental_pinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($parental_pinData)) {
            $returnData = $parental_pinData[0]["data"];
        }
        return $returnData;
    }
    public function webtvpanel_saveparentpin($postdata = [], $conn = [])
    {
        $pin = $postdata["pin"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $parentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($parentpinData)) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
            $DeleteparentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $DeleteparentpinData;
        }
        $QueryData = ["request" => "Insert", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $pin]];
        $InsertparentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        $returnData = $InsertparentpinData;
        return $returnData;
    }
    public function webtvpanel_confirmandpindelete($postdata = [], $conn = [])
    {
        $oldpininput = $postdata["currentpininput"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = ["result" => "error", "message" => "OLD PIN does not exists!!"];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $oldpininput]];
        $parentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        $returnData = [""];
        if ($parentpinData[0]["data"] == $oldpininput) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $oldpininput]];
            $DeleteparentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = ["result" => "success", "message" => "PIN Deleted Successfully!"];
        } else {
            $returnData = ["result" => "error", "message" => "Current PIN does not matched!!"];
        }
        return $returnData;
    }
    public function webtvpanel_checkoldandupdatepin($postdata = [], $conn = [])
    {
        $oldpininput = $postdata["oldpininput"];
        $pin = $postdata["newpininput"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = ["result" => "error", "message" => "OLD PIN does not exists!!"];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $oldpininput]];
        $parentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if ($parentpinData[0]["data"] == $oldpininput) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $oldpininput]];
            $DeleteparentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $DeleteparentpinData;
            $QueryData = ["request" => "Insert", "table" => "webtvtheme_parental_pin", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $pin]];
            $InsertparentpinData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = ["result" => "success", "message" => "PIN updated successfully!!"];
        } else {
            $returnData = ["result" => "error", "message" => "Current PIN not matched!"];
        }
        return $returnData;
    }
    public function webtvpanel_getliveviewsettings($conn = [])
    {
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_live_view", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $live_viewData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($live_viewData)) {
            $returnData = $live_viewData[0]["data"];
        }
        return $returnData;
    }
    public function webtvpanel_saveliveviewsettings($postdata = [], $conn = [])
    {
        $selectedview = $postdata["selectedview"];
        $SessionStroedUsername = $_SESSION["webTvplayer"]["username"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $returnData = [];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_live_view", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
        $live_viewData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($live_viewData)) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_live_view", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink]];
            $Deletelive_viewData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $Deletelive_viewData;
        }
        $QueryData = ["request" => "Insert", "table" => "webtvtheme_live_view", "data" => ["username" => $SessionStroedUsername, "portalink" => $SessionStroedportallink, "data" => $selectedview]];
        $Insertlive_viewData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        $returnData = $Insertlive_viewData;
        return $returnData;
    }
    public function webtvpanel_getFavExists($streamtype = "", $portallink = "", $streamid = "", $conn = [])
    {
        $return = [];
        if (!empty($streamtype) && !empty($streamid)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $portallink, "streamid" => $streamid, "streamtype" => $streamtype]];
            $fav_Data = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        }
        $return = $fav_Data;
        return $return;
    }
    public function webtvpanel_getFavforCategories($streamtype = "", $conn = [])
    {
        $portallink = $_SESSION["webTvplayer"]["portallink"];
        $bar = "/";
        if (substr($portallink, -1) == "/") {
            $bar = "";
        }
        $portallink = $portallink . $bar;
        $return = [];
        if (!empty($streamtype)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $portallink, "streamtype" => $streamtype]];
            $fav_CatData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        }
        $return = $fav_CatData;
        return $return;
    }
    public function webtvpanel_saveFavdata($portallink = "", $favtype = "", $favstreamId = "", $fullFavData = [], $conn = [])
    {
        $fullFavData = str_replace("'", "\\'", $fullFavData);
        $returnData = ["result" => "error", "message" => "Stream already exists!"];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $portallink, "streamid" => $favstreamId, "streamtype" => $favtype]];
        $fav_Data = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (empty($fav_Data)) {
            $QueryData = ["request" => "Insert", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $portallink, "streamtype" => $favtype, "streamid" => $favstreamId, "favdata" => $fullFavData]];
            $InsertFavData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = $InsertFavData;
        }
        return $returnData;
    }
    public function webtvpanel_delFavdata($postdata = [], $conn = [])
    {
        $favtype = $postdata["favtype"];
        $favview = $postdata["favview"];
        $SessionStroedportallink = $_SESSION["webTvplayer"]["portallink"];
        $favstreamId = $postdata["favstreamId"];
        $returnData = ["result" => "error", "message" => "Stream already removed!"];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $SessionStroedportallink, "streamid" => $favstreamId, "streamtype" => $favtype]];
        $fav_Data = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
        if (!empty($fav_Data)) {
            $QueryData = ["request" => "Delete", "table" => "webtvtheme_fav_streams", "data" => ["portallink" => $SessionStroedportallink, "streamtype" => $favtype, "streamid" => $favstreamId]];
            $DeleteFavData = $this->WebTVClient_ExecuteQuery($QueryData, $conn);
            $returnData = ["result" => "success", "message" => "Stream remove from Favourite!!"];
        }
        return $returnData;
    }
    public function webtvpanel_like_match($pattern, $subject)
    {
        $pattern = str_replace("%", ".*", preg_quote($pattern, "/"));
        return (int) preg_match("/^" . $pattern . "\$/i", $subject);
    }
    public function webtvpanel_parentcondition($Text = "", $conn = [])
    {
        $parentpassword = $this->webtvpanel_getparentpinformart($conn);
        $returnData = "0";
        $parentenable = $parentpassword != "" ? "on" : "";
        $parentpassword = "";
        if ($parentenable == "on" && ($this->webtvpanel_like_match("%adults%", $Text) == 1 || $this->webtvpanel_like_match("%adult%", $Text) == 1 || $this->webtvpanel_like_match("%Adults%", $Text) == 1 || $this->webtvpanel_like_match("%XXX%", $Text) == 1 || $this->webtvpanel_like_match("%Porn%", $Text) == 1 || $this->webtvpanel_like_match("%xxx%", $Text) == 1 || $this->webtvpanel_like_match("%Sexy%", $Text) == 1 || $this->webtvpanel_like_match("%foradults%", $Text) == 1 || $this->webtvpanel_like_match("%ADULTE%", $Text) == 1 || $this->webtvpanel_like_match("%adulte%", $Text) == 1)) {
            $returnData = "1";
        }
        return $returnData;
    }
}

?>