<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("FUNCTIONCONTROLLERABSPATH", dirname(dirname(dirname(__FILE__))) . "/");
class controlfunctions
{
    public function createallrecommendedtablesfunction($conn = [])
    {
        if (!empty($conn)) {
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_blocked_section (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,section VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_admin (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(255) NOT NULL,password VARCHAR(255) NOT NULL,role INT(11) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_settings (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,settings VARCHAR(255) NOT NULL,value LONGTEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_clientslogs (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(255) NOT NULL,password VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,ipaddress VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_blockedips (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,ipaddress VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_blocked_section_table = "CREATE TABLE IF NOT EXISTS webtvtheme_loginattempts (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,ipaddress VARCHAR(255) NOT NULL,attempts INT(11) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_blocked_section_table);
            $create_webplayer_userdetails = "CREATE TABLE IF NOT EXISTS webtvtheme_userdetails (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(255) NOT NULL,password VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,status VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_userdetails);
            $create_webplayer_log = "CREATE TABLE IF NOT EXISTS webtvtheme_log (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id VARCHAR(255) NOT NULL,login_time datetime,dns VARCHAR(255) NOT NULL,ip_address VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_log);
            $create_webplayer_testlinedetails = "CREATE TABLE IF NOT EXISTS webtvtheme_testlinedetails (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,username VARCHAR(255) NOT NULL,password VARCHAR(255),portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_testlinedetails);
            $create_webplayer_activitylogs_details = "CREATE TABLE IF NOT EXISTS webtvtheme_activitylogs (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,ipaddress VARCHAR(255) NOT NULL,lastactive VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_activitylogs_details);
            $create_webplayer_playersettings = "CREATE TABLE IF NOT EXISTS webtvtheme_playersettings (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalink VARCHAR(255) NOT NULL,username VARCHAR(255) NOT NULL,type VARCHAR(255) NOT NULL,data TEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_playersettings);
            $create_webplayer_epgtime_shift = "CREATE TABLE IF NOT EXISTS webtvtheme_epgtime_shift (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalink VARCHAR(255) NOT NULL,username VARCHAR(255) NOT NULL,data TEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_epgtime_shift);
            $create_webplayer_time_format = "CREATE TABLE IF NOT EXISTS webtvtheme_time_format (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalink VARCHAR(255) NOT NULL,username VARCHAR(255) NOT NULL,data TEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_time_format);
            $create_webplayer_parental_pin = "CREATE TABLE IF NOT EXISTS webtvtheme_parental_pin (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalink VARCHAR(255) NOT NULL,username VARCHAR(255) NOT NULL,data TEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_parental_pin);
            $create_webplayer_live_view = "CREATE TABLE IF NOT EXISTS webtvtheme_live_view (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalink VARCHAR(255) NOT NULL,username VARCHAR(255) NOT NULL,data TEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_live_view);
            $create_webplayer_fav_streams = "CREATE TABLE IF NOT EXISTS webtvtheme_fav_streams (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portallink VARCHAR(255) NOT NULL,streamtype VARCHAR(255) NOT NULL,streamid INT(11) NOT NULL,favdata LONGTEXT NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webplayer_fav_streams);
            $create_webtvtheme_banners = "CREATE TABLE IF NOT EXISTS webtvtheme_banners (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,portalurl VARCHAR(255) NOT NULL,type VARCHAR(255) NOT NULL,category VARCHAR(20) NOT NULL,streamid INT(20) NOT NULL,streamdata LONGTEXT NOT NULL,banner VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webtvtheme_banners);
            $create_webtv_blocked_categories = "CREATE TABLE IF NOT EXISTS webtvtheme_blocked_categories (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,category_id INT(11) NOT NULL,type VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webtv_blocked_categories);
            $create_webtv_block_Streams = "CREATE TABLE IF NOT EXISTS webtvtheme_blocked_streams (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,streams_id VARCHAR(255) NOT NULL,category_id VARCHAR(255) NOT NULL,section VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webtv_block_Streams);
            $create_webtv_blocked_Seriesstreams = "CREATE TABLE IF NOT EXISTS webtvtheme_blocked_Seriesstreams (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,streams_id VARCHAR(255) NOT NULL,category_id VARCHAR(255) NOT NULL,episode_id VARCHAR(255) NULL,season_no VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webtv_blocked_Seriesstreams);
            $create_webtv_external_streamlink = "CREATE TABLE IF NOT EXISTS webtvtheme_external_streamlink (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,type VARCHAR(255) NOT NULL,streams_id VARCHAR(255) NOT NULL,category_id VARCHAR(255) NOT NULL,episode_id VARCHAR(255) NULL,season_no VARCHAR(255) NULL,externallink VARCHAR(255) NOT NULL,portallink VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($create_webtv_external_streamlink);
            $webtvtheme_theme_activation = "CREATE TABLE IF NOT EXISTS webtvtheme_theme_activation (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,theme VARCHAR(255) NOT NULL,code VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($webtvtheme_theme_activation);
            $webtvtheme_theme_activation = "CREATE TABLE IF NOT EXISTS webtvtheme_cast_container (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,cast_name VARCHAR(255) NOT NULL,type VARCHAR(255) NOT NULL,tmbd_cast_id VARCHAR(255) NOT NULL,popularity INT(255) NOT NULL,image_path VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($webtvtheme_theme_activation);
            $webtvtheme_cast_information = "CREATE TABLE IF NOT EXISTS webtvtheme_cast_information (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(255) NOT NULL,tmbd_cast_id VARCHAR(255) NOT NULL,dob VARCHAR(255) NOT NULL,profession VARCHAR(255) NOT NULL,placeofbirth VARCHAR(255) NOT NULL,profile_path VARCHAR(255) NOT NULL,bio LONGTEXT NULL,created_on TIMESTAMP)";
            $conn->query($webtvtheme_cast_information);
            $webtvtheme_cast_gallery = "CREATE TABLE IF NOT EXISTS webtvtheme_cast_gallery (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,tmbd_cast_id VARCHAR(255) NOT NULL,img_src VARCHAR(255) NOT NULL,created_on TIMESTAMP)";
            $conn->query($webtvtheme_cast_gallery);
        }
    }
    public function webtvtheme_encrypt($q, $salt = "WEBTVPLAYER")
    {
        $string = $q;
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $salt;
        $secret_iv = $salt;
        $key = hash("sha256", $secret_key);
        $iv = substr(hash("sha256", $secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }
    public function webtvtheme_decrypt($q, $salt = "WEBTVPLAYER")
    {
        $string = $q;
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $salt;
        $secret_iv = $salt;
        $iv = substr(hash("sha256", $secret_iv), 0, 16);
        $key = hash("sha256", $secret_key);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
    public function webtvtheme_getconfigurationoption($conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_settings", "data" => []];
            $ExecuteQuery = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
            $returnData = $ExecuteQuery;
        }
        return $returnData;
    }
    public function webtvtheme_getUserAlldetails($conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_userdetails", "data" => []];
            $ExecuteQuery = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
            $returnData = $ExecuteQuery;
        }
        return $returnData;
    }
    public function webtvtheme_getExternalLinkdetails($conn = [], $type = "", $streams_id = "", $portallink = "")
    {
        $returnData = [];
        if (!empty($conn)) {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "streams_id" => $streams_id, "portallink" => $portallink]];
            $ExecuteQuery = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
            $returnData = $ExecuteQuery;
        }
        return $returnData;
    }
    public function webtvtheme_Saveadminemailaddress($postfields = [], $conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $adminemail = $postfields["adminemail"];
            $CustomizeArray = ["adminemail" => $adminemail];
            $SuccessCounter = 0;
            foreach ($CustomizeArray as $Kdata => $Vdata) {
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata]];
                $this->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata, "value" => $Vdata]];
                $QueryExicute = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
                if ($QueryExicute["result"] == "success") {
                    $SuccessCounter++;
                }
            }
            if ($SuccessCounter == 1) {
                $returnData = ["result" => "success", "message" => "Changes saved successfully.."];
            }
        }
        return $returnData;
    }
    public function webtvtheme_Saveconfigdetails($postfields = [], $conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $SiteTitle = $postfields["sitetitle"];
            $logo = $postfields["logo"];
            $logo2 = $postfields["logo2"];
            if (isset($postfields["portalidentifire"]) && !empty($postfields["portalidentifire"])) {
                $postfields["portallinks"] = array_combine($postfields["portalidentifire"], $postfields["portallinks"]);
            }
            $portallinks = serialize($postfields["portallinks"]);
            $CustomizeArray = ["sitetitle" => $SiteTitle, "logo" => $logo, "version" => "2.0", "portallinks" => $portallinks];
            $SuccessCounter = 0;
            foreach ($CustomizeArray as $Kdata => $Vdata) {
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata]];
                $this->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata, "value" => $Vdata]];
                $QueryExicute = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
                if ($QueryExicute["result"] == "success") {
                    $SuccessCounter++;
                }
            }
            if ($SuccessCounter == 4) {
                $returnData = ["result" => "success", "message" => "Changes saved successfully.."];
            }
        }
        return $returnData;
    }
    public function webtvtheme_SaveSecuritySettings($postfields = [], $conn = [])
    {
        $returnData = [];
        if (!empty($conn)) {
            $totalFields = count($postfields);
            $SuccessCounter = 0;
            foreach ($postfields as $Kdata => $Vdata) {
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata]];
                $this->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata, "value" => $Vdata]];
                $QueryExicute = $this->webtvtheme_ExecuteQuery($QueryData, $conn);
                if ($QueryExicute["result"] == "success") {
                    $SuccessCounter++;
                }
            }
            if ($SuccessCounter == $totalFields) {
                $returnData = ["result" => "success", "message" => "Changes saved successfully.."];
            }
        }
        return $returnData;
    }
    public function webtvtheme_ExecuteQuery($QueryData, $conn = [])
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
                if (isset($QueryData["extra"]) && !empty($QueryData["extra"])) {
                    $eachCounter2 = 1;
                    foreach ($QueryData["extra"] as $val) {
                        $lastval = mysqli_real_escape_string($conn, $val);
                        $FinalQueryToExicute .= " " . $lastval;
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
                            $dataline .= "'" . $lastval . "'" . $comma;
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
                        } else {
                            if ($QueryData["request"] == "Count") {
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
                                if (isset($QueryData["extra"]) && !empty($QueryData["extra"])) {
                                    $eachCounter2 = 1;
                                    foreach ($QueryData["extra"] as $val) {
                                        $lastval = mysqli_real_escape_string($conn, $val);
                                        $FinalQueryToExicute .= " " . $lastval;
                                    }
                                }
                                $FinalQueryToExicute .= ";";
                                $result = $conn->query($FinalQueryToExicute);
                                $returnData = $result->num_rows;
                                return $returnData;
                            } else {
                                if ($QueryData["request"] == "FullCustomQuery") {
                                    $FinalQueryToExicute = $QueryData["query"];
                                    $FinalQueryToExicute .= ";";
                                    $result = $conn->query($FinalQueryToExicute);
                                    return $result;
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