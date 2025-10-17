<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("ABSPATH", dirname(dirname(dirname(__FILE__))) . "/");
if (file_exists(ABSPATH . "connection.php")) {
    include_once ABSPATH . "connection.php";
}
if (file_exists(ABSPATH . "lib/Admin/AdminContoller.php")) {
    include_once ABSPATH . "lib/Admin/AdminContoller.php";
}
if (file_exists(ABSPATH . "lib/Admin/Controller.php")) {
    include_once ABSPATH . "lib/Admin/Controller.php";
}
if (file_exists(ABSPATH . "lib/Common/CommonController.php")) {
    include_once ABSPATH . "lib/Common/CommonController.php";
}
if (file_exists(ABSPATH . "admin/includes/functions.php")) {
    include_once ABSPATH . "admin/includes/functions.php";
}
if (file_exists(ABSPATH . "lib/twoFaLib.php")) {
    include_once ABSPATH . "lib/twoFaLib.php";
}
if (isset($_POST["action"]) && $_POST["action"] == "loginadminprocess") {
    $returnData = ["result" => "error", "message" => "Invalid Details"];
    $dispatcher = new AdminContoller();
    $controlfunctions = new controlfunctions();
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->makeconnection();
    $CommonController = new CommonController();
    $ClintIpaddress = $CommonController->client_ipaddress();
    $GCDATA = $CommonController->getconfigurationoption($conn, "1");
    if (isset($_POST["checkreacptha"]) && !empty($_POST["checkreacptha"])) {
        $recptchasecret = isset($GCDATA["recptchasecret"]) && !empty($GCDATA["recptchasecret"]) ? $GCDATA["recptchasecret"] : "";
        $secret = $recptchasecret;
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $_POST["captharesponse"]);
        $responseData = json_decode($verifyResponse);
        if (!$responseData->success) {
            $ss = "Invalid g-recaptcha";
            $returnData = ["result" => "error", "message" => $ss];
            $returnData = json_encode($returnData);
            echo $returnData;
            exit;
        }
    }
    if (array_key_exists("dberror", $conn)) {
        $returnData = ["result" => "error", "message" => "Your are not connected to database"];
    } else {
        $adminemail = "";
        $GCDATA = $CommonController->getconfigurationoption($conn, "1");
        if (isset($GCDATA["adminemail"]) && !empty($GCDATA["adminemail"])) {
            $adminemail = $GCDATA["adminemail"];
        }
        unset($_POST["action"]);
        $rememberme = $_POST["rememberme"];
        $username = $_POST["username"];
        $timeforemail = date("l, d F Y h:i A");
        $unameforemail = $_POST["username"];
        $passforemail = $_POST["password"];
        $EncPasswordToCheck = $controlfunctions->webtvtheme_encrypt($_POST["password"]);
        $QueryData = ["request" => "Get", "table" => "webtvtheme_admin", "data" => ["username" => $username, "password" => $EncPasswordToCheck, "role" => "1"]];
        $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        if (!empty($ExecuteQuery)) {
            $twofa = isset($GCDATA["twofa"]) && $GCDATA["twofa"] == "on" ? "on" : "";
            if ($twofa == "on") {
                if (!isset($_POST["validatetwofacode"])) {
                    $returnData = ["result" => "verify2fa", "message" => "verify2fa"];
                    $returnData = json_encode($returnData);
                    echo $returnData;
                    exit;
                }
                $secret = isset($GCDATA["gasecret"]) && !empty($GCDATA["gasecret"]) ? $controlfunctions->webtvtheme_decrypt($GCDATA["gasecret"]) : "";
                if ($secret != "") {
                    $googleauthenticator = new GoogleAuthenticator();
                    $checkResult = $googleauthenticator->verifyCode($secret, $_POST["validatetwofacode"], 2);
                    if ($checkResult != "1") {
                        $returnData = ["result" => "error", "message" => "Invalid Or Expire Code"];
                        $returnData = json_encode($returnData);
                        echo $returnData;
                        exit;
                    }
                }
            }
            $AdminUsername = $controlfunctions->webtvtheme_encrypt($ExecuteQuery[0]["username"]);
            $AdminID = $controlfunctions->webtvtheme_encrypt($ExecuteQuery[0]["id"]);
            $_SESSION["webadmin"] = ["usename" => $AdminUsername, "id" => $AdminID];
            if ($rememberme == "on") {
                setcookie("adminusername", $AdminUsername, time() + 1209600, "/", $_SERVER["SERVER_NAME"], false);
                setcookie("adminuserpassword", $EncPasswordToCheck, time() + 1209600, "/", $_SERVER["SERVER_NAME"], false);
            }
            $returnData = ["result" => "success", "message" => "Your are successfully logged in"];
        } else {
            $returnData = ["result" => "error", "message" => "Invalid Details"];
        }
        if ($returnData["result"] != "success") {
            $TotalAttempts = 0;
            $NextAttempts = 1;
            $QueryData = ["request" => "Get", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $ClintIpaddress]];
            $getattempts = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($getattempts)) {
                if (isset($getattempts[0]["attempts"]) && $getattempts[0]["attempts"] != "") {
                    $TotalAttempts = $getattempts[0]["attempts"];
                }
                $NextAttempts = $TotalAttempts + 1;
                $QueryData = ["request" => "Update", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $ClintIpaddress], "updatedata" => ["attempts" => $NextAttempts, "created_on" => date("Y-m-d h:i:s")]];
                $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            } else {
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $ClintIpaddress, "attempts" => $NextAttempts, "created_on" => date("Y-m-d h:i:s")]];
                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            }
            if (2 < $NextAttempts) {
                $QueryData2 = ["request" => "Insert", "table" => "webtvtheme_blockedips", "data" => ["ipaddress" => $ClintIpaddress, "created_on" => date("Y-m-d h:i:s")]];
                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData2, $conn);
                $returnData = ["result" => "error", "message" => "Your account is blocked due maximun wrong attempts", "blocked" => "yes"];
            }
            if ($NextAttempts == 2) {
                $returnData = ["result" => "error", "message" => "Be carefull one more wrong attempt will block you"];
            }
        }
        if ($returnData["result"] == "error" && $adminemail != "") {
            $blockedmsg = "";
            if (isset($returnData["blocked"]) && !empty($returnData["blocked"])) {
                $blockedmsg = "IP is bloclked Now";
            }
            $message = isset($returnData["message"]) && !empty($returnData["message"]) ? $returnData["message"] : "Attempt 1";
            $GCDATA = $CommonController->SendEmailcall($GCDATA, $adminemail, "Alert : WebTV Player Admin Wrong Attempt From IP " . $ClintIpaddress, "Dear Admin,\n\t\t\t\t<br>\n\t\t\t\t<br>\n\t\t\t\tGetting Wrong Login Attempts For Your WebTV Player Admin Area\n\t\t\t\t<br> From IP " . $ClintIpaddress . ",\n\t\t\t\t<br> Username : " . $unameforemail . " ,\n\t\t\t\t<br> Password : " . $passforemail . ",\n\t\t\t\t<br> At : " . $timeforemail . " \n\t\t\t\t<br> Attempt No : " . $NextAttempts);
        }
        if ($returnData["result"] == "success") {
            $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_loginattempts", "data" => ["ipaddress" => $ClintIpaddress]];
            $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
            $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blockedips", "data" => ["ipaddress" => $ClintIpaddress]];
            $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
        }
        $returnData = json_encode($returnData);
        echo $returnData;
    }
    exit;
}
if (isset($_POST["action"]) && $_POST["action"] == "checkcurrentpassword") {
    $returnData = ["result" => "error", "message" => "Invalid Details"];
    $dispatcher = new AdminContoller();
    $controlfunctions = new controlfunctions();
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->makeconnection();
    $currentPassword = $controlfunctions->webtvtheme_encrypt($_POST["currentpassword"]);
    $newpassword = $controlfunctions->webtvtheme_encrypt($_POST["newpassword"]);
    if (array_key_exists("dberror", $conn)) {
        $returnData = ["result" => "error", "message" => "Your are not connected to database"];
    } else {
        $QueryData = ["request" => "Get", "table" => "webtvtheme_admin", "data" => ["username" => "admin", "password" => $currentPassword, "role" => "1"]];
        $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        if (!empty($ExecuteQuery)) {
            $QueryData = ["request" => "Update", "table" => "webtvtheme_admin", "data" => ["username" => "admin", "password" => $currentPassword, "role" => "1"], "updatedata" => ["password" => $newpassword]];
            $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if ($ExecuteQuery["result"] == "success") {
                $returnData = ["result" => "success", "message" => "Password successfully updated."];
            } else {
                $returnData = ["result" => "error", "message" => "Unable to update password!!"];
            }
        } else {
            $returnData = ["result" => "error", "message" => "Invalid Current Password!!"];
        }
    }
    echo json_encode($returnData);
    exit;
}
if (isset($_POST["action"]) && $_POST["action"] == "checkvalidlicense") {
    $returnData = ["result" => "error", "message" => "Invalid License"];
    $license = $_POST["licenseval"];
    $CommonController = new CommonController();
    $checkLicense = $CommonController->checklicense($license);
    if ($checkLicense["status"] == "Active") {
        $localKey = $checkLicense["localkey"];
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $SuccessCounter = 0;
        $ForEachData = ["license" => $license, "localKey" => $localKey];
        foreach ($ForEachData as $Kdata => $Vdata) {
            $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata]];
            $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
            $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata, "value" => $Vdata]];
            $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if ($QueryExicute["result"] == "success") {
                $SuccessCounter++;
            }
        }
        if ($SuccessCounter == 2) {
            $returnData = ["result" => "success", "message" => "License saved successfully.."];
        }
    }
    echo json_encode($returnData);
    exit;
} else {
    if (isset($_POST["action"]) && $_POST["action"] == "GetMediaContent") {
        $SeleCtedImagePath = $_POST["bgimgvalue"];
        echo "\t <div class=\"row clearfix\">\n          ";
        $all_files = glob("../../mediafiles/*.*");
        foreach (array_reverse($all_files) as $imagepath) {
            $image_name = $imagepath;
            $supported_format = ["gif", "jpg", "jpeg", "png"];
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (in_array($ext, $supported_format)) {
                $image_name = str_replace("../../", "../", $image_name);
                echo "                  <div class=\"col-md-2 nopad text-center\">                    \n                    <label class=\"image-checkbox\">\n                      <img class=\"img-responsive\" src=\"";
                echo $image_name;
                echo "\" style=\"height: 87px;\"/>\n                      <input type=\"checkbox\" class=\"checkboxcommon\" name=\"image\" value=\"";
                echo $image_name;
                echo "\" ";
                echo $SeleCtedImagePath == $image_name ? "checked" : "";
                echo " />\n                      <i class=\"fa fa-check hidden\" ></i>\n                    </label>\n                  </div>\n                  ";
            }
        }
        echo "        </div> \t\n\t";
    }
    if (isset($_POST["action"]) && $_POST["action"] == "checkportallink") {
        $portalLink = $_POST["portvalue"];
        $CommonController = new CommonController();
        echo $checkLicense = $CommonController->checkportalvalid($portalLink);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "checkportallinkwithtestline") {
        $portalLink = $_POST["portvalue"];
        $checkfor = $_POST["checkfor"];
        $errormsg = "No test line added for Block Content!!";
        if ($checkfor != "" && $checkfor == "addbanner") {
            $errormsg = "No test line added for Add Banner!!";
        }
        $bar = "/";
        if (substr($portalLink, -1) == "/") {
            $bar = "";
        }
        $portalLink = $portalLink . $bar;
        $returnData = ["result" => "error", "message" => $errormsg];
        $dispatcher = new AdminContoller();
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $CommonController = new CommonController();
        $conn = $DatabaseObj->makeconnection();
        if (array_key_exists("dberror", $conn)) {
            $returnData = ["result" => "error", "message" => "Your are not connected to database"];
        } else {
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["portallink" => $portalLink]];
            $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $username = $ExecuteQuery[0]["username"];
                $password = $controlfunctions->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                $ApiRequestCall = $portalLink . "player_api.php?username=" . $username . "&password=" . $password;
                $APIresponse = $CommonController->CallApiRequest($ApiRequestCall);
                $Result = $APIresponse;
                if ($Result["result"] == "success" && isset($Result["data"]->user_info->auth) && $Result["data"]->user_info->auth != 0 && $Result["data"]->user_info->status == "Active") {
                    $returnData = ["result" => "success", "message" => "Valid Details", "insertid" => base64_encode($ExecuteQuery[0]["id"])];
                }
            }
        }
        echo json_encode($returnData);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "checktestlineforblockconetent") {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        $portalLink = $_POST["portalis"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $bar = "/";
        if (substr($portalLink, -1) == "/") {
            $bar = "";
        }
        $portalLink = $portalLink . $bar;
        $CheckLIneApiRequest = $portalLink . "player_api.php?username=" . $username . "&password=" . $password;
        $CommonController = new CommonController();
        $APIresponse = $CommonController->CallApiRequest($CheckLIneApiRequest);
        $DatabaseObj = new DBConnect();
        $controlfunctions = new controlfunctions();
        $conn = $DatabaseObj->makeconnection();
        $Result = $APIresponse;
        if ($Result["result"] == "success" && isset($Result["data"]->user_info->auth) && $Result["data"]->user_info->auth != 0 && $Result["data"]->user_info->status == "Active") {
            $EncPasswordToCheck = $controlfunctions->webtvtheme_encrypt($password);
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["portallink" => $portalLink]];
            $TestLineData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($TestLineData)) {
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_testlinedetails", "data" => ["portallink" => $portalLink]];
                $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
            }
            $QueryData = ["request" => "Insert", "table" => "webtvtheme_testlinedetails", "data" => ["username" => $username, "password" => $EncPasswordToCheck, "portallink" => $portalLink]];
            $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            $InsertID = isset($QueryExicute["insert_id"]) && !empty($QueryExicute["insert_id"]) ? base64_encode($QueryExicute["insert_id"]) : "";
            $returnData = ["result" => "success", "message" => "Valid Details", "insertid" => $InsertID];
        }
        echo json_encode($returnData);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "activatetheme") {
        $returnData = ["result" => "error", "message" => "Invalid Details"];
        $dispatcher = new AdminContoller();
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $activatetheme = $_POST["activate_theme"];
        if (array_key_exists("dberror", $conn)) {
            $returnData = ["result" => "error", "message" => "Your are not connected to database"];
        } else {
            $QueryData = ["request" => "Update", "table" => "webtvtheme_settings", "data" => ["settings" => "theme"], "updatedata" => ["value" => $activatetheme]];
            $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if ($ExecuteQuery["result"] == "success") {
                $returnData = ["result" => "success", "message" => "Theme Successfully Activated."];
            } else {
                $returnData = ["result" => "error", "message" => "Unable to Activate theme!!"];
            }
        }
        echo json_encode($returnData);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "checklastactivity") {
        $returnData = ["action" => ""];
        $currenttime = time();
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $CommonController = new CommonController();
        $clientIPaddress = $CommonController->client_ipaddress();
        $QueryData = ["request" => "Get", "table" => "webtvtheme_activitylogs", "data" => ["ipaddress" => $clientIPaddress]];
        $lastactivity = 0;
        $RecordData = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        if (!empty($RecordData)) {
            $lastactivity = isset($RecordData[0]["lastactive"]) && !empty($RecordData[0]["lastactive"]) ? $RecordData[0]["lastactive"] : "";
            $time = ceil(round($currenttime - $lastactivity) / 60);
            if (20 <= $time) {
                session_destroy();
                session_unset();
                session_reset();
                $returnData = ["action" => "logout"];
            }
        }
        echo json_encode($returnData);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "userstatusaction") {
        $StatusShouldBe = $_POST["currentis"] == "1" ? "Blocked" : "Active";
        $rowid = $_POST["rowid"];
        $returnData = ["result" => "error"];
        $currenttime = time();
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $CommonController = new CommonController();
        $QueryData = ["request" => "Update", "table" => "webtvtheme_userdetails", "data" => ["id" => $rowid], "updatedata" => ["status" => $StatusShouldBe]];
        $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        if ($ExecuteQuery["result"] == "success") {
            $returnData = ["result" => "success"];
        }
        echo json_encode($returnData);
        exit;
    }
    if (isset($_POST["action"]) && $_POST["action"] == "getstreamlisttestdetails") {
        $dispatcher = new AdminContoller();
        $controlfunctions = new controlfunctions();
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $CommonController = new CommonController();
        $PortSelectorID = $_POST["portalID"];
        $section = $_POST["SectionIS"];
        $Categoryid = $_POST["Categoryid"];
        $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $PortSelectorID]];
        $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        $portallink = $ExecuteQuery[0]["portallink"];
        $bar = "/";
        if (substr($portallink, -1) == "/") {
            $bar = "";
        }
        $portallink = $portallink . $bar;
        $QueryData = ["request" => "Get", "table" => "webtvtheme_banners", "data" => ["portalurl" => $portallink, "type" => $section, "category" => $Categoryid], "extra" => ["ORDER BY id DESC"]];
        $adddedList = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
        $validateStreams = [];
        if (!empty($adddedList)) {
            foreach ($adddedList as $Skey) {
                $validateStreams[] = $Skey["streamid"];
            }
        }
        $Arrayforsorting = [];
        $StreamSData = $CommonController->getStreamsByCateIDSectionAndListID($conn, $PortSelectorID, $section, $Categoryid);
        if ($StreamSData["result"] == "success" && !empty($StreamSData["data"])) {
            $SortingType = "topadded";
            foreach ($StreamSData["data"] as $StreamData) {
                $compareId = $section == "movies" ? $StreamData->stream_id : $StreamData->series_id;
                if (!in_array($compareId, $validateStreams)) {
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
                    $Arrayforsorting[$customKey] = (int) ["name" => $StreamData->name, "rating" => $StreamData->rating, "rating_5based" => $StreamData->rating_5based, "stream_id" => $section == "movies" ? $StreamData->stream_id : $StreamData->series_id];
                }
            }
            ksort($Arrayforsorting);
            $Arrayforsorting = array_reverse($Arrayforsorting, true);
            if (!empty($Arrayforsorting)) {
                echo "        \t<option value=\"\">Top 50 Added</option>\n        \t";
                $MaxCounter = 0;
                foreach ($Arrayforsorting as $data) {
                    if ($Counter <= 50) {
                        echo "\t        \t\t<option value=\"";
                        echo $data->stream_id;
                        echo "\" data-ratingis=\"";
                        echo $data->rating_5based;
                        echo "\">";
                        echo $data->name;
                        echo "</option>\n\t        \t\t";
                    }
                    $Counter++;
                }
            }
        } else {
            echo "";
        }
        exit;
    } else {
        if (isset($_POST["action"]) && $_POST["action"] == "GetStreamDataByStremID") {
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            $PortSelectorID = $_POST["portalID"];
            $section = $_POST["SectionIS"];
            $StreamID = $_POST["StreamID"];
            $Arrayforsorting = [];
            $StreamSData = $CommonController->GetSingleStreamDataByStreamIDListID($conn, $PortSelectorID, $section, $StreamID);
            if ($StreamSData["result"] == "success" && !empty($StreamSData["data"])) {
                if ($section == "movies") {
                    $MOvieData = isset($StreamSData["data"]->movie_data) && !empty($StreamSData["data"]->movie_data) ? $StreamSData["data"]->movie_data : "";
                    $MOvieinfo = $StreamSData["data"]->info;
                    $MovieName = isset($MOvieData->name) && !empty($MOvieData->name) ? $MOvieData->name : "";
                    $MovieName = isset($MOvieinfo->name) && !empty($MOvieinfo->name) ? $MOvieinfo->name : "";
                    $backdrop_path = isset($MOvieinfo->backdrop_path) && !empty($MOvieinfo->backdrop_path) ? $MOvieinfo->backdrop_path : "";
                    if (!empty($backdrop_path)) {
                        $randkey = array_rand($backdrop_path);
                        echo $BackGroundImageCover = $backdrop_path[$randkey];
                        exit;
                    }
                    echo $CommonController->BannerSliderFromExternalAPI($MovieName, "movies");
                }
                if ($section == "series") {
                    $SeriesData = isset($StreamSData["data"]->info) && !empty($StreamSData["data"]->info) ? $StreamSData["data"]->info : "";
                    $SeriesName = isset($SeriesData->name) && !empty($SeriesData->name) ? $SeriesData->name : "";
                    $backdrop_path = isset($SeriesData->backdrop_path) && !empty($SeriesData->backdrop_path) ? $SeriesData->backdrop_path : "";
                    if (!empty($backdrop_path)) {
                        $randkey = array_rand($backdrop_path);
                        echo $BackGroundImageCover = $backdrop_path[$randkey];
                        exit;
                    }
                    echo $CommonController->BannerSliderFromExternalAPI($SeriesName, "series");
                }
            }
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "GetStreambyBannerName") {
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            echo $CommonController->BannerSliderFromExternalAPI($_POST["streamName"], $_POST["SectionIS"]);
        }
        if (isset($_POST["action"]) && $_POST["action"] == "SaveSliderBannerDetails") {
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            $potallistID = $_POST["portalID"];
            $SectionIS = $_POST["SectionIS"];
            $Categoryid = $_POST["Categoryid"];
            $streamID = $_POST["streamID"];
            $bannerImage = $_POST["bannerImage"];
            $streamRating = $_POST["streamRating"];
            $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $potallistID]];
            $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
            if (!empty($ExecuteQuery)) {
                $portalLink = isset($ExecuteQuery[0]["portallink"]) && !empty($ExecuteQuery[0]["portallink"]) ? $ExecuteQuery[0]["portallink"] : "";
                $bar = "/";
                if (substr($portalLink, -1) == "/") {
                    $bar = "";
                }
                $portalLink = $portalLink . $bar;
                $StreamSData = $CommonController->GetSingleStreamDataByStreamIDListID($conn, $potallistID, $SectionIS, $streamID);
                $FinalStreamDataArray = [];
                if ($StreamSData["result"] == "success" && !empty($StreamSData["data"])) {
                    if ($SectionIS == "movies") {
                        $MOvieData = isset($StreamSData["data"]->movie_data) && !empty($StreamSData["data"]->movie_data) ? $StreamSData["data"]->movie_data : "";
                        $MOvieinfo = $StreamSData["data"]->info;
                        $MovieName = isset($MOvieData->name) && !empty($MOvieData->name) ? $MOvieData->name : "";
                        $MovieName = isset($MOvieinfo->name) && !empty($MOvieinfo->name) ? $MOvieinfo->name : $MovieName;
                        $MovieReleasedate = isset($MOvieinfo->releasedate) && !empty($MOvieinfo->releasedate) ? $MOvieinfo->releasedate : "";
                        $Moviedirector = isset($MOvieinfo->director) && !empty($MOvieinfo->director) ? $MOvieinfo->director : "";
                        $Moviecast = isset($MOvieinfo->cast) && !empty($MOvieinfo->cast) ? $MOvieinfo->cast : "";
                        $Moviegenre = isset($MOvieinfo->genre) && !empty($MOvieinfo->genre) ? $MOvieinfo->genre : "";
                        $Moviedescription = isset($MOvieinfo->description) && !empty($MOvieinfo->description) ? $MOvieinfo->description : "";
                        $Moviecast = str_replace("'", " ", $Moviecast);
                        $Moviedescription = str_replace("'", " ", $Moviedescription);
                        $FinalStreamDataArray["name"] = str_replace("'", " ", $MovieName);
                        $FinalStreamDataArray["releasedate"] = str_replace("'", " ", $MovieReleasedate);
                        $FinalStreamDataArray["director"] = str_replace("'", " ", $Moviedirector);
                        $FinalStreamDataArray["cast"] = $Moviecast;
                        $FinalStreamDataArray["description"] = $Moviedescription;
                        $FinalStreamDataArray["genre"] = str_replace("'", " ", $Moviegenre);
                        $FinalStreamDataArray["rating"] = str_replace("'", " ", $streamRating);
                    } else {
                        $SeriesInfoS = $StreamSData["data"]->info;
                        $SeriesName = isset($SeriesInfoS->name) && !empty($SeriesInfoS->name) ? $SeriesInfoS->name : $SeriesName;
                        $SeriesReleasedate = isset($SeriesInfoS->releasedate) && !empty($SeriesInfoS->releasedate) ? $SeriesInfoS->releasedate : "";
                        $Seriesdirector = isset($SeriesInfoS->director) && !empty($SeriesInfoS->director) ? $SeriesInfoS->director : "";
                        $Seriescast = isset($SeriesInfoS->cast) && !empty($SeriesInfoS->cast) ? $SeriesInfoS->cast : "";
                        $Seriesgenre = isset($SeriesInfoS->genre) && !empty($SeriesInfoS->genre) ? $SeriesInfoS->genre : "";
                        $Seriesdescription = isset($SeriesInfoS->plot) && !empty($SeriesInfoS->plot) ? $SeriesInfoS->plot : "";
                        $Seriescast = str_replace("'", " ", $Seriescast);
                        $Seriesdescription = str_replace("'", " ", $Seriesdescription);
                        $FinalStreamDataArray["name"] = str_replace("'", " ", $SeriesName);
                        $FinalStreamDataArray["releasedate"] = str_replace("'", " ", $SeriesReleasedate);
                        $FinalStreamDataArray["director"] = str_replace("'", " ", $Seriesdirector);
                        $FinalStreamDataArray["cast"] = $Seriescast;
                        $FinalStreamDataArray["description"] = $Seriesdescription;
                        $FinalStreamDataArray["genre"] = str_replace("'", " ", $Seriesgenre);
                        $FinalStreamDataArray["rating"] = str_replace("'", " ", $streamRating);
                    }
                    $FinalStreamDataSearialize = serialize($FinalStreamDataArray);
                    $QueryData = ["request" => "Get", "table" => "webtvtheme_banners", "data" => ["portalurl" => $portalLink, "type" => $SectionIS, "category" => $Categoryid, "streamid" => $streamID]];
                    $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                    if (!empty($ExecuteQuery)) {
                        $RowID = $ExecuteQuery[0]["id"];
                        $QueryData = ["request" => "Update", "table" => "webtvtheme_banners", "data" => ["id" => $RowID], "updatedata" => ["portalurl" => $portalLink, "type" => $SectionIS, "category" => $Categoryid, "streamid" => $streamID, "banner" => $bannerImage, "streamdata" => $FinalStreamDataSearialize]];
                        $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                        if ($QueryExicute["result"] == "success") {
                            echo "1";
                        }
                    } else {
                        $QueryData = ["request" => "Insert", "table" => "webtvtheme_banners", "data" => ["portalurl" => $portalLink, "type" => $SectionIS, "category" => $Categoryid, "streamid" => $streamID, "banner" => $bannerImage, "streamdata" => $FinalStreamDataSearialize]];
                        $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                        if ($QueryExicute["result"] == "success") {
                            echo "1";
                        }
                    }
                } else {
                    echo "121212";
                }
            }
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "deletebannerbyid") {
            $RowID = $_POST["selectorID"];
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_banners", "data" => ["id" => $RowID]];
            $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
            echo "1";
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "blockfullsection") {
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            $Section = isset($_POST["rowid"]) && !empty($_POST["rowid"]) ? $_POST["rowid"] : "";
            $currentis = isset($_POST["currentis"]) ? $_POST["currentis"] : "";
            $portallink = isset($_POST["portallink"]) && !empty($_POST["portallink"]) ? $_POST["portallink"] : "";
            $bar = "/";
            if (substr($portallink, -1) == "/") {
                $bar = "";
            }
            $portallink = $portallink . $bar;
            if ($currentis == "0") {
                $QueryData = ["request" => "Insert", "table" => "webtvtheme_blocked_section", "data" => ["section" => $Section, "portallink" => $portallink]];
                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                if ($QueryExicute["result"] == "success") {
                    echo "1";
                }
                exit;
            }
            $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blocked_section", "data" => ["section" => $Section, "portallink" => $portallink]];
            $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
            echo "1";
            exit;
        }
        if (isset($_POST["action"]) && $_POST["action"] == "GetCateGoriesForBlockConntent") {
            $dispatcher = new AdminContoller();
            $controlfunctions = new controlfunctions();
            $DatabaseObj = new DBConnect();
            $conn = $DatabaseObj->makeconnection();
            $CommonController = new CommonController();
            $CommonController->addActivityOnload($conn);
            $listid = $_POST["listid"];
            $section = $_POST["section"];
            $FirstCategoriesIDS = [];
            $newCategoryIDArray = [];
            $BlockedCateGoriesIDs = $CommonController->getBlockedCategoriesIts($conn, $listid, $section);
            $CateGoreisList = $CommonController->getCategoriesBySectionAndListID($conn, $listid, $section);
            if (isset($CateGoreisList["result"]) && $CateGoreisList["result"] == "success") {
                if ($section == "catchup") {
                    $CateGoreisList = [];
                    foreach ($CateGoreisList["data"] as $StreamsData) {
                        if ($StreamsData->tv_archive == 1) {
                            $FirstCategoriesIDS[$StreamsData->category_id] = "catregoryid";
                        }
                    }
                    sleep(1);
                }
                if ($section == "radio") {
                    $CateGoreisList = [];
                    foreach ($CateGoreisList["data"] as $StreamsData) {
                        if ($StreamsData->stream_type == "radio_streams") {
                            $FirstCategoriesIDS[$StreamsData->category_id] = "catregoryid";
                        }
                    }
                    sleep(1);
                }
                if (!empty($FirstCategoriesIDS)) {
                    $GetOnlyCateGories = $CommonController->getCategoriesBySection("live");
                    if ($GetOnlyCateGories["result"] == "success" && !empty($GetOnlyCateGories["data"])) {
                        $FinalCategoriesArray["result"] = "success";
                        $counter = 0;
                        foreach ($GetOnlyCateGories["data"] as $CatKey) {
                            if (array_key_exists($CatKey->category_id, $FirstCategoriesIDS)) {
                                $newCategoryIDArray[$CatKey->category_id] = (int) ["category_id" => $CatKey->category_id, "category_name" => $CatKey->category_name, "parent_id" => "0"];
                                $counter++;
                            }
                            $FinalCategoriesArray["data"] = $newCategoryIDArray;
                        }
                        $CateGoreisList = $FinalCategoriesArray;
                    }
                }
                if (!empty($CateGoreisList["data"])) {
                    foreach ($CateGoreisList["data"] as $CatData) {
                        echo "\t\t\t\t<div class=\"col-md-6 commonblock\">\n\t\t\t\t\t<div class=\"inner-common\">\n\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t<span class=\"text-containeris\">\n\t\t\t\t\t\t\t\t\t";
                        echo $CatData->category_name;
                        echo "\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"col-md-6 btncontianers\">\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t<a href=\"explore-block.php?p=";
                        echo base64_encode($listid);
                        echo "&sec=";
                        echo $section;
                        echo "&cate=";
                        echo base64_encode($CatData->category_id);
                        echo "\" class=\"explorecategories ex_cate exploreBtnNo-";
                        echo $CatData->category_id;
                        echo " ";
                        echo in_array($CatData->category_id, $BlockedCateGoriesIDs) ? "d-none" : "";
                        echo "\" data-cateforlocal=\"";
                        echo $CatData->category_name;
                        echo "\">EXPLORE</a>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"blockbtncontainer\">\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"onoffswitch\" class=\"onoffswitch-checkbox blockcatsecbtn\" id=\"myonoffswitch-";
                        echo $CatData->category_id;
                        echo "\" data-currentis=\"";
                        echo in_array($CatData->category_id, $BlockedCateGoriesIDs) ? "1" : "0";
                        echo "\"\n\t\t\t\t\t\t\t\t\t\t\t data-categoryidis=\"";
                        echo $CatData->category_id;
                        echo "\" data-rowid=\"";
                        echo $section;
                        echo "\" data-nameselector=\"";
                        echo $CatData->category_name;
                        echo "\" ";
                        echo in_array($CatData->category_id, $BlockedCateGoriesIDs) ? "checked" : "";
                        echo ">\n\t\t\t\t\t\t\t\t\t\t\t<label class=\"onoffswitch-label onblockswitch\" for=\"myonoffswitch-";
                        echo $CatData->category_id;
                        echo "\">\n\t\t\t                                </label>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t";
                    }
                } else {
                    echo "\t\t\t<div class=\"col-md-12\">\n\t\t\t\t<h4 class=\"text-center\">No Categories Available !!</h4 >\n\t\t\t</div>\t\n\t\t";
                }
            }
            exit;
        } else {
            if (isset($_POST["action"]) && $_POST["action"] == "blockCategory") {
                $dispatcher = new AdminContoller();
                $controlfunctions = new controlfunctions();
                $DatabaseObj = new DBConnect();
                $conn = $DatabaseObj->makeconnection();
                $CommonController = new CommonController();
                $CommonController->addActivityOnload($conn);
                $SestionIS = $_POST["secttionis"];
                $currentis = $_POST["currentis"];
                $categoryidis = $_POST["categoryidis"];
                $portallink = $_POST["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $portallink = $portallink . $bar;
                if ($currentis == "0") {
                    $QueryData = ["request" => "Insert", "table" => "webtvtheme_blocked_categories", "data" => ["category_id" => $categoryidis, "type" => $SestionIS, "portallink" => $portallink]];
                    $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                    if ($QueryExicute["result"] == "success") {
                        echo "1";
                    }
                    exit;
                }
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blocked_categories", "data" => ["category_id" => $categoryidis, "type" => $SestionIS, "portallink" => $portallink]];
                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                echo "1";
                exit;
            }
            if (isset($_POST["action"]) && $_POST["action"] == "blockseriesEpisodes") {
                $dispatcher = new AdminContoller();
                $controlfunctions = new controlfunctions();
                $DatabaseObj = new DBConnect();
                $conn = $DatabaseObj->makeconnection();
                $CommonController = new CommonController();
                $CommonController->addActivityOnload($conn);
                $seasonumber = $_POST["seasonumber"];
                $streamname = $_POST["streamname"];
                $categoryidis = $_POST["categoryidis"];
                $streamid = $_POST["streamid"];
                $episodeid = $_POST["episodeid"];
                $currentis = $_POST["currentis"];
                $portallink = $_POST["portallink"];
                $bar = "/";
                if (substr($portallink, -1) == "/") {
                    $bar = "";
                }
                $portallink = $portallink . $bar;
                if ($currentis == "0") {
                    $QueryData = ["request" => "Insert", "table" => "webtvtheme_blocked_Seriesstreams", "data" => ["streams_id" => $streamid, "category_id" => $categoryidis, "episode_id" => $episodeid, "season_no" => $seasonumber, "portallink" => $portallink]];
                    $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                    if ($QueryExicute["result"] == "success") {
                        echo "1";
                    }
                    exit;
                }
                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blocked_Seriesstreams", "data" => ["category_id" => $categoryidis, "season_no" => $seasonumber, "episode_id" => $episodeid, "portallink" => $portallink]];
                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                echo "1";
                exit;
            }
            if (isset($_POST["action"]) && $_POST["action"] == "GetStreamsByCategoryAndSecForBlock") {
                $dispatcher = new AdminContoller();
                $controlfunctions = new controlfunctions();
                $DatabaseObj = new DBConnect();
                $conn = $DatabaseObj->makeconnection();
                $CommonController = new CommonController();
                $CommonController->addActivityOnload($conn);
                $listid = $_POST["listid"];
                $section = $_POST["section"];
                $cate = $_POST["cate"];
                $StreamListForBlock = $CommonController->GetStreamsByCategoryAndSecForBlock($conn, $listid, $section, $cate);
                $BlockedCateStreamsIDs = $CommonController->getBlockedStreamsIts($conn, $listid, $section, $cate);
                if (isset($StreamListForBlock["result"]) && $StreamListForBlock["result"] == "success" && !empty($StreamListForBlock["data"])) {
                    foreach ($StreamListForBlock["data"] as $StreamData) {
                        $StreamIdSelector = $StreamData->stream_id;
                        if ($section == "series") {
                            $StreamIdSelector = $StreamData->series_id;
                        }
                        echo "\t\t\t\t<div class=\"col-md-6 commonblock\">\n\t\t\t\t\t<div class=\"inner-common\">\n\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t<span class=\"text-containeris\">\n\t\t\t\t\t\t\t\t\t";
                        echo $StreamData->name;
                        echo "\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"col-md-6 btncontianers\">\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t";
                        if ($section != "live") {
                            echo "\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t<a href=\"\" class=\"explorecategories ex_streams exploreBtnNo-";
                            echo $StreamIdSelector;
                            echo " ";
                            echo in_array($StreamIdSelector, $BlockedCateStreamsIDs) ? "d-none" : "";
                            echo "\" data-sectype=\"";
                            echo $section;
                            echo "\" data-streamidtoexplore=\"";
                            echo $StreamIdSelector;
                            echo "\" data-listid=\"";
                            echo $listid;
                            echo "\" data-categorystreams=\"";
                            echo $cate;
                            echo "\" data-streamname=\"";
                            echo $StreamData->name;
                            echo "\">EXPLORE</a>\n\t\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t\t\t";
                        }
                        echo "\t\t\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"blockbtncontainer\">\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"onoffswitch\" class=\"onoffswitch-checkbox blockcatsecbtn\" id=\"myonoffswitch-";
                        echo $StreamIdSelector;
                        echo "\" data-currentis=\"";
                        echo in_array($StreamIdSelector, $BlockedCateStreamsIDs) ? "1" : "0";
                        echo "\" data-streamidtoadd=\"";
                        echo $StreamIdSelector;
                        echo "\" data-rowid=\"";
                        echo $section;
                        echo "\" data-nameselector=\"";
                        echo $StreamData->name;
                        echo "\" ";
                        echo in_array($StreamIdSelector, $BlockedCateStreamsIDs) ? "checked" : "";
                        echo ">\n\t\t\t\t\t\t\t\t\t\t\t<label class=\"onoffswitch-label onblockswitch\" for=\"myonoffswitch-";
                        echo $StreamIdSelector;
                        echo "\">\n\t\t\t                                </label>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t";
                    }
                }
                exit;
            } else {
                if (isset($_POST["action"]) && $_POST["action"] == "blockStreams") {
                    $dispatcher = new AdminContoller();
                    $controlfunctions = new controlfunctions();
                    $DatabaseObj = new DBConnect();
                    $conn = $DatabaseObj->makeconnection();
                    $CommonController = new CommonController();
                    $CommonController->addActivityOnload($conn);
                    $SestionIS = $_POST["secttionis"];
                    $currentis = $_POST["currentis"];
                    $streamidtoadd = $_POST["streamidtoadd"];
                    $categroyid = $_POST["categroyid"];
                    $portallink = $_POST["portallink"];
                    $bar = "/";
                    if (substr($portallink, -1) == "/") {
                        $bar = "";
                    }
                    $portallink = $portallink . $bar;
                    if ($currentis == "0") {
                        $QueryData = ["request" => "Insert", "table" => "webtvtheme_blocked_streams", "data" => ["category_id" => $categroyid, "section" => $SestionIS, "portallink" => $portallink, "streams_id" => $streamidtoadd]];
                        $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                        if ($QueryExicute["result"] == "success") {
                            echo "1";
                        }
                        exit;
                    }
                    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_blocked_streams", "data" => ["category_id" => $categroyid, "section" => $SestionIS, "portallink" => $portallink, "streams_id" => $streamidtoadd]];
                    $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                    echo "1";
                    exit;
                }
                if (isset($_POST["action"]) && $_POST["action"] == "getStreamDataExplore") {
                    $dispatcher = new AdminContoller();
                    $controlfunctions = new controlfunctions();
                    $DatabaseObj = new DBConnect();
                    $conn = $DatabaseObj->makeconnection();
                    $CommonController = new CommonController();
                    $CommonController->addActivityOnload($conn);
                    $PortSelectorID = $_POST["listid"];
                    $categoryIs = $_POST["categoryIs"];
                    $StreamIDis = $_POST["StreamIDis"];
                    $sectionIs = $_POST["sectionIs"];
                    $streamname = $_POST["streamname"];
                    $QueryData = ["request" => "Get", "table" => "webtvtheme_testlinedetails", "data" => ["id" => $PortSelectorID]];
                    $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                    if (!empty($ExecuteQuery)) {
                        $portalLink = isset($ExecuteQuery[0]["portallink"]) && !empty($ExecuteQuery[0]["portallink"]) ? $ExecuteQuery[0]["portallink"] : "";
                        $bar = "/";
                        if (substr($portalLink, -1) == "/") {
                            $bar = "";
                        }
                        $portalLink = $portalLink . $bar;
                        $username = $ExecuteQuery[0]["username"];
                        $password = $controlfunctions->webtvtheme_decrypt($ExecuteQuery[0]["password"]);
                        $restLink = "";
                        if ($sectionIs == "movies") {
                            $restLink = "&action=get_vod_info&vod_id=" . $StreamIDis;
                        } else {
                            if ($sectionIs == "series") {
                                $restLink = "&action=get_series_info&series_id=" . $StreamIDis;
                            }
                        }
                        $ApiRequestCall = $portalLink . "player_api.php?username=" . $username . "&password=" . $password . $restLink;
                        $APIresponse = $CommonController->CallApiRequest($ApiRequestCall);
                        $Result = $APIresponse;
                        if ($Result["result"] == "success" && !empty($Result["data"])) {
                            if ($sectionIs == "movies") {
                                $GetExternalLinkdetails = $controlfunctions->webtvtheme_getExternalLinkdetails($conn, $sectionIs, $StreamIDis, $portalLink);
                                echo "\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t<div class=\"col-md-12\" id=\"maininformationsec\">\n\t\t\t\t\t\t\t<center><b>Codec Information</b></center>\n\t\t\t\t\t\t\t<div class=\"row main-explorecontainer\" style=\"margin-top: 10px\">\n\t\t\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t\t\t<b style=\"text-decoration: underline;\">VIDEO</b>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tCodec Name\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                echo isset($Result["data"]->info->video->codec_name) ? $Result["data"]->info->video->codec_name : "n/A";
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tFull Codec Name\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                echo isset($Result["data"]->info->video->codec_long_name) ? $Result["data"]->info->video->codec_long_name : "n/A";
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-12\" style=\"margin-top: 10px\">\n\t\t\t\t\t\t\t\t\t<b style=\"text-decoration: underline;\">AUDIO</b>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tCodec Name\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                echo isset($Result["data"]->info->audio->codec_name) ? $Result["data"]->info->audio->codec_name : "n/A";
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tFull Codec Name\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                echo isset($Result["data"]->info->audio->codec_long_name) ? $Result["data"]->info->audio->codec_long_name : "n/A";
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-12\" style=\"margin-top: 10px\">\n\t\t\t\t\t\t\t\t\t<b style=\"text-decoration: underline;\">OTHERS INFORMATION</b>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tExtension\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                $Extension = isset($Result["data"]->movie_data->container_extension) ? $Result["data"]->movie_data->container_extension : "n/A";
                                echo $Extension;
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tDuration\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t";
                                echo isset($Result["data"]->info->duration) ? $Result["data"]->info->duration : "n/A";
                                echo "\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\tCheck Video with embed player's\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t<select id=\"PlayerCheckSec\" class=\"form-control PlayerCheckSec-";
                                echo $StreamIDis;
                                echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"JW player\">JW player</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"Flow player\">Flow player</option>\n\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t<button id=\"Checkplayer\" class=\"CheckplayerBtn btn btn-info\" data-playlinktocheck=\"";
                                echo $portalLink . "movie/" . $username . "/" . $password . "/" . $StreamIDis . "." . $Extension;
                                echo "\" data-uniqueselector=\"";
                                echo $StreamIDis;
                                echo "\">CHECK</button>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-4 mt-2\">\n\t\t\t\t\t\t\t\t\tAdd External link to play\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class=\"col-md-8\">\n\t\t\t\t\t\t\t\t\t<div class=\"row mt-2\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" id=\"externallink-";
                                echo $StreamIDis;
                                echo "\" placeholder=\"Enter the External link to play\" value=\"";
                                echo $GetExternalLinkdetails[0]["externallink"];
                                echo "\">\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-6\">\n\t\t\t\t\t\t\t\t\t\t\t<button class=\"btn btn-primary extrernalsubmit\" data-categoryidis=\"";
                                echo $categoryIs;
                                echo "\" data-streamname=\"";
                                echo $streamname;
                                echo "\" data-streamid=\"";
                                echo $StreamIDis;
                                echo "\" data-type=\"movies\">";
                                echo !empty($GetExternalLinkdetails) ? "Change" : "Add";
                                echo " External link</button>\n\t\t\t\t\t\t\t\t\t\t\t<i class=\"far fa-trash-alt delExternal delcheck-";
                                echo $StreamIDis;
                                echo " ";
                                echo empty($GetExternalLinkdetails) ? "d-none" : "";
                                echo "\" data-categoryidis=\"";
                                echo $categoryIs;
                                echo "\" data-streamid=\"";
                                echo $StreamIDis;
                                echo "\" data-type=\"movies\"></i>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"col-md-12 d-none\" id=\"player-sectionis\">\n\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t<div class=\"col-md-12\" id=\"playerholder\">\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t";
                            }
                            if ($sectionIs == "series") {
                                $GetExternalLinkdetails = $controlfunctions->webtvtheme_getExternalLinkdetails($conn, $sectionIs, $StreamIDis, $portalLink);
                                echo "\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t";
                                $SeriesData = [];
                                $SeriesData["data"] = $Result["data"];
                                if (isset($SeriesData["data"]->episodes) && !empty($SeriesData["data"]->episodes)) {
                                    $SeasonsIdData = [];
                                    $Appepisodes = [];
                                    if (!empty($SeriesData["data"]->episodes)) {
                                        $Appepisodes = $SeriesData["data"]->episodes;
                                        foreach ($SeriesData["data"]->episodes as $episodes) {
                                            foreach ($episodes as $episodesData) {
                                                $SeasonsIdData[$episodesData->season] = "season";
                                            }
                                        }
                                    }
                                    if (!empty($SeasonsIdData)) {
                                        echo "\t\t\t\t\t\t\t<div class=\"col-md-12\" id=\"maininformationsec\">\t\n\t\t\t\t\t\t\t\t<div class=\"accordion\" id=\"SeasonAccordion\">\n\t\t\t\t\t\t\t\t";
                                        $counterSeasons = 0;
                                        foreach ($SeasonsIdData as $SeasonNumber => $val) {
                                            echo "\t\t\t\t            \t\t<div class=\"card\">\n\t\t\t\t            \t\t\t<div class=\"card-header\" id=\"heading";
                                            echo $SeasonNumber;
                                            echo "\">\n\t\t\t\t\t\t\t\t\t      <h2 class=\"mb-0\">\n\t\t\t\t\t\t\t\t\t        <button class=\"btn btn-link\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapse";
                                            echo $SeasonNumber;
                                            echo "\" aria-expanded=\"";
                                            echo $counterSeasons == 0 ? "true" : "false";
                                            echo "\" aria-controls=\"collapse";
                                            echo $SeasonNumber;
                                            echo "\">\n\t\t\t\t\t\t\t\t\t          Season ";
                                            echo $SeasonNumber;
                                            echo "\t\t\t\t\t\t\t\t\t        </button>\n\t\t\t\t\t\t\t\t\t      </h2>\n\t\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t\t\t\t\t\t    <div id=\"collapse";
                                            echo $SeasonNumber;
                                            echo "\" class=\"collapse ";
                                            echo $counterSeasons == 0 ? "show" : "notshow";
                                            echo "\" aria-labelledby=\"heading";
                                            echo $SeasonNumber;
                                            echo "\" data-parent=\"#SeasonAccordion\">\n\t\t\t\t\t\t\t\t\t      <div class=\"card-body row\">\n\t\t\t\t\t\t\t\t\t      \t<div class=\"col-md-12 episodeselectior\">\n\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tEpisode Number\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tVIDEO CODEC\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tAUDIO CODEC\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tEXTENSION\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tCHECK VIDEO\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-3\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tUnblock / Block\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t</div>\t\t\n\t\t\t\t\t\t\t\t\t        ";
                                            foreach ($Appepisodes as $episodes) {
                                                foreach ($episodes as $episodesData) {
                                                    $externlLinkValue = "";
                                                    if ($episodesData->season == $SeasonNumber) {
                                                        if (!empty($GetExternalLinkdetails)) {
                                                            foreach ($GetExternalLinkdetails as $externlLinkseries) {
                                                                if ($externlLinkseries["season_no"] == $episodesData->season && $externlLinkseries["episode_id"] == $episodesData->id) {
                                                                    $externlLinkValue = $externlLinkseries["externallink"];
                                                                }
                                                            }
                                                        }
                                                        $BlockedCateGoriesIDs = $CommonController->getBlockedSeriesEpisode($conn, $PortSelectorID, $StreamIDis, $categoryIs, $episodesData->id);
                                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-12 episodeselectior\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-2\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tEpisode ";
                                                        echo $episodesData->episode_num;
                                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                                        echo isset($episodesData->info->video->codec_name) ? $episodesData->info->video->codec_name : "n/A";
                                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
                                                        echo isset($episodesData->info->audio->codec_name) ? $episodesData->info->audio->codec_name : "n/A";
                                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-1\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t ";
                                                        $Extension = isset($episodesData->container_extension) ? $episodesData->container_extension : "n/A";
                                                        echo $Extension;
                                                        echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-4\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t <div class=\"row\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"playercheckdiv\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<select id=\"PlayerCheckSec\" class=\"form-control   PlayerCheckSec-";
                                                        echo $episodesData->id;
                                                        echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t \t<option value=\"JW player\">JW player</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"Flow player\">Flow player</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button id=\"Checkplayer\" class=\"CheckplayerBtn btn btn-info ml-2\"  data-playlinktocheck=\"";
                                                        echo $portalLink . "series/" . $username . "/" . $password . "/" . $episodesData->id . "." . $Extension;
                                                        echo "\" data-uniqueselector=\"";
                                                        echo $episodesData->id;
                                                        echo "\">CHECK</button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-3\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"blockbtncontainer\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"onoffswitch\" class=\"onoffswitch-checkbox blockcatsecbtnseries\" id=\"myonoffseriesswitch-";
                                                        echo $episodesData->id;
                                                        echo "\" data-currentis=\"";
                                                        echo in_array($episodesData->id, $BlockedCateGoriesIDs) ? "1" : "0";
                                                        echo "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t data-categoryidis=\"";
                                                        echo $categoryIs;
                                                        echo "\" data-seasonum=\"";
                                                        echo $episodesData->season;
                                                        echo "\" data-streamname=\"";
                                                        echo $streamname;
                                                        echo "\" data-streamid=\"";
                                                        echo $StreamIDis;
                                                        echo "\" data-episodeid=\"";
                                                        echo $episodesData->id;
                                                        echo "\" ";
                                                        echo in_array($episodesData->id, $BlockedCateGoriesIDs) ? "checked" : "";
                                                        echo ">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<label class=\"onoffswitch-label onblockswitch\" for=\"myonoffseriesswitch-";
                                                        echo $episodesData->id;
                                                        echo "\">\n\t\t\t\t\t\t\t                                </label>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"externallinkshow linkfor-";
                                                        echo $episodesData->id;
                                                        echo " float-right mr-4 ";
                                                        echo in_array($episodesData->id, $BlockedCateGoriesIDs) ? "d-none" : "";
                                                        echo " ";
                                                        echo !empty($externlLinkValue) ? "text-danger" : "";
                                                        echo "\" data-showid=\"";
                                                        echo $episodesData->id;
                                                        echo "\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Add External link\"><i class=\"fa fa-link fa-2x\"></i></span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"col-md-12\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"externallink-field d-none\" id=\"myextrnal-";
                                                        echo $episodesData->id;
                                                        echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<input type=\"url\" class=\"form-control\" name=\"externallinkseries\" id=\"externallink-";
                                                        echo $episodesData->id;
                                                        echo "\" placeholder=\"Enter External link for This Episode only\" value=\"";
                                                        echo !empty($externlLinkValue) ? $externlLinkValue : "";
                                                        echo "\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<i class=\"far fa-trash-alt delExternal delcheck-";
                                                        echo $episodesData->id;
                                                        echo " ";
                                                        echo empty($externlLinkValue) ? "d-none" : "";
                                                        echo "\" data-seasonum=\"";
                                                        echo $episodesData->season;
                                                        echo "\" data-categoryidis=\"";
                                                        echo $categoryIs;
                                                        echo "\" data-streamid=\"";
                                                        echo $StreamIDis;
                                                        echo "\" data-type=\"series\" data-episodeid=\"";
                                                        echo $episodesData->id;
                                                        echo "\"></i>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary extrernalsubmit ml-2\" data-categoryidis=\"";
                                                        echo $categoryIs;
                                                        echo "\" data-seasonum=\"";
                                                        echo $episodesData->season;
                                                        echo "\" data-streamname=\"";
                                                        echo $streamname;
                                                        echo "\" data-streamid=\"";
                                                        echo $StreamIDis;
                                                        echo "\" data-type=\"series\" data-episodeid=\"";
                                                        echo $episodesData->id;
                                                        echo "\">";
                                                        echo !empty($externlLinkValue) ? "Change" : "Save";
                                                        echo " Link</button>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t\t\t";
                                                    }
                                                }
                                            }
                                            echo "\t\t\t\t\t\t\t\t\t      </div>\n\t\t\t\t\t\t\t\t\t    </div>\n\t\t\t\t            \t\t</div>\n\t\t\t\t            \t\t";
                                            $counterSeasons++;
                                        }
                                        echo "\t\t\t\t            \t</div>\n\t\t\t            \t</div>\n\t\t\t            \t<div class=\"col-md-12 d-none\" id=\"player-sectionis\">\n\t\t\t\t\t\t\t\t<div class=\"row\">\n\t\t\t\t\t\t\t\t\t<div class=\"col-md-12\" id=\"playerholder\">\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t";
                                    }
                                }
                                echo "\t\t\t\t\t</div>\t\n\t\t\t\t\t";
                            }
                        }
                    }
                    exit;
                } else {
                    if (isset($_POST["action"]) && $_POST["action"] == "saveExternalink") {
                        $return = "0";
                        $DatabaseObj = new DBConnect();
                        $controlfunctions = new controlfunctions();
                        $conn = $DatabaseObj->makeconnection();
                        $categoryidis = $_POST["categoryidis"];
                        $seasonumber = $_POST["seasonumber"];
                        $streamname = $_POST["streamname"];
                        $streamid = $_POST["streamid"];
                        $episodeid = $_POST["episodeid"];
                        $extLink = $_POST["extLink"];
                        $portalLink = $_POST["portallink"];
                        $bar = "/";
                        if (substr($portalLink, -1) == "/") {
                            $bar = "";
                        }
                        $portalLink = $portalLink . $bar;
                        $type = $_POST["type"];
                        if ($type == "series") {
                            $QueryData = ["request" => "Get", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "episode_id" => $episodeid, "streams_id" => $streamid, "season_no" => $seasonumber, "portallink" => $portalLink]];
                            $getexterdata = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            if (!empty($getexterdata)) {
                                $QueryData = ["request" => "Update", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "episode_id" => $episodeid, "streams_id" => $streamid, "season_no" => $seasonumber, "portallink" => $portalLink], "updatedata" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "episode_id" => $episodeid, "season_no" => $seasonumber, "externallink" => $extLink, "portallink" => $portalLink]];
                                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            } else {
                                $QueryData = ["request" => "Insert", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "episode_id" => $episodeid, "season_no" => $seasonumber, "externallink" => $extLink, "portallink" => $portalLink]];
                                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            }
                            if ($QueryExicute["result"] == "success") {
                                $return = "1";
                            }
                        }
                        if ($type == "movies") {
                            $QueryData = ["request" => "Get", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "portallink" => $portalLink]];
                            $getexterdata = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            if (!empty($getexterdata)) {
                                $QueryData = ["request" => "Update", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "portallink" => $portalLink], "updatedata" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "externallink" => $extLink, "portallink" => $portalLink]];
                                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            } else {
                                $QueryData = ["request" => "Insert", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $type, "streams_id" => $streamid, "category_id" => $categoryidis, "externallink" => $extLink, "portallink" => $portalLink]];
                                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                            }
                            if ($QueryExicute["result"] == "success") {
                                $return = "1";
                            }
                        }
                        echo $return;
                        exit;
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "deleteExternalink") {
                        $return = "0";
                        $DatabaseObj = new DBConnect();
                        $controlfunctions = new controlfunctions();
                        $conn = $DatabaseObj->makeconnection();
                        $delcategoryidis = $_POST["delcategoryidis"];
                        $delseasonum = $_POST["delseasonum"];
                        $delstreamid = $_POST["delstreamid"];
                        $deltype = $_POST["deltype"];
                        $delepisodeid = $_POST["delepisodeid"];
                        $portalLink = $_POST["portallink"];
                        $bar = "/";
                        if (substr($portalLink, -1) == "/") {
                            $bar = "";
                        }
                        $portalLink = $portalLink . $bar;
                        if ($deltype == "series") {
                            $QueryData = ["request" => "Delete", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $deltype, "streams_id" => $delstreamid, "category_id" => $delcategoryidis, "episode_id" => $delepisodeid, "portallink" => $portalLink]];
                        }
                        if ($deltype == "movies") {
                            $QueryData = ["request" => "Delete", "table" => "webtvtheme_external_streamlink", "data" => ["type" => $deltype, "streams_id" => $delstreamid, "category_id" => $delcategoryidis, "portallink" => $portalLink]];
                        }
                        $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                        if (is_array($QueryExicute)) {
                            $return = "1";
                        }
                        echo $return;
                        exit;
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "checkandsaveactivationcode") {
                        $returnData = ["result" => "error", "message" => "Invalid Details"];
                        $dispatcher = new AdminContoller();
                        $controlfunctions = new controlfunctions();
                        $DatabaseObj = new DBConnect();
                        $CommonController = new CommonController();
                        $conn = $DatabaseObj->makeconnection();
                        if (array_key_exists("dberror", $conn)) {
                            $returnData = ["result" => "error", "message" => "Your are not connected to database"];
                        } else {
                            $theme = $SelectedThemeIS = $_POST["selectedthemeis"];
                            $code = $_POST["code"];
                            $CheckThemeActivationCode = $CommonController->CheckThemeActivationCode($conn, $code, $SelectedThemeIS);
                            if ($CheckThemeActivationCode == "Active") {
                                $QueryData = ["request" => "Get", "table" => "webtvtheme_theme_activation", "data" => ["theme" => $theme]];
                                $getexterdata = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                if (!empty($getexterdata)) {
                                    $QueryData = ["request" => "Update", "table" => "webtvtheme_theme_activation", "data" => ["theme" => $theme], "updatedata" => ["theme" => $theme, "code" => $code]];
                                    $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                } else {
                                    $QueryData = ["request" => "Insert", "table" => "webtvtheme_theme_activation", "data" => ["theme" => $theme, "code" => $code]];
                                    $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                }
                                $returnData = ["result" => "success", "message" => "Active"];
                            }
                        }
                        echo json_encode($returnData);
                        exit;
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "checkandsavesmtp") {
                        $detailsfrompost = $_POST["datatosend"];
                        $dispatcher = new AdminContoller();
                        $controlfunctions = new controlfunctions();
                        $DatabaseObj = new DBConnect();
                        $CommonController = new CommonController();
                        $conn = $DatabaseObj->makeconnection();
                        $checksmtp = $CommonController->checkSMTPDetails($detailsfrompost);
                        if ($checksmtp == "Connected") {
                            $encpassword = $controlfunctions->webtvtheme_encrypt($detailsfrompost["smtppassword"]);
                            $detailsfrompost["smtppassword"] = $encpassword;
                            $SuccessCounter = 0;
                            foreach ($detailsfrompost as $Kdata => $Vdata) {
                                $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata]];
                                $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
                                $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => $Kdata, "value" => $Vdata]];
                                $QueryExicute = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
                                if ($QueryExicute["result"] == "success") {
                                    $SuccessCounter++;
                                }
                            }
                            if ($SuccessCounter == 5) {
                                echo "connected";
                                exit;
                            }
                            echo "Unable to save the details";
                            exit;
                        } else {
                            echo $checksmtp;
                            exit;
                        }
                    }
                }
            }
        }
    }
}

?>