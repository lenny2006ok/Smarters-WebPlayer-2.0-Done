<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("EPGAJAXCONROLERDIRPATH", dirname(dirname(__FILE__)) . "/");
if (file_exists(EPGAJAXCONROLERDIRPATH . "connection.php")) {
    include_once EPGAJAXCONROLERDIRPATH . "connection.php";
}
if (file_exists(EPGAJAXCONROLERDIRPATH . "lib/Common/CommonController.php")) {
    include_once EPGAJAXCONROLERDIRPATH . "lib/Common/CommonController.php";
}
if (file_exists(EPGAJAXCONROLERDIRPATH . "admin/includes/functions.php")) {
    include_once EPGAJAXCONROLERDIRPATH . "admin/includes/functions.php";
}
if (file_exists(EPGAJAXCONROLERDIRPATH . "includes/functions.php")) {
    include_once EPGAJAXCONROLERDIRPATH . "includes/functions.php";
}
if (isset($_POST["action"]) && $_POST["action"] == "GetEpgDataByStreamid") {
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->makeconnection();
    $StreamId = $_POST["StreamId"];
    $CurrentPcDateTime = new DateTime($_POST["currentTime"]);
    $CurrentTime = $CurrentPcDateTime->getTimestamp();
    $CommonController = new CommonController();
    $clientcontrolfunctions = new clientcontrolfunctions();
    $RequestForEpg = $CommonController->getEpgDataByCateGoryID($StreamId);
    $GlobalTimeFormat = $clientcontrolfunctions->webtvpanel_gettimeformart($conn);
    $epgtimeshift = $clientcontrolfunctions->webtvpanel_getepgtimeshifting($conn);
    $epgtimeshift = $epgtimeshift != "" ? $epgtimeshift : "0";
    $Formatis = "h:i A";
    if ($GlobalTimeFormat == "24") {
        $Formatis = "H:i";
    }
    if ($epgtimeshift != "0") {
        $CurrentTime = strtotime($epgtimeshift . " hours", $CurrentTime);
    }
    if (!empty($RequestForEpg) && $RequestForEpg["result"] == "success") {
        $CurrentDate = date("Y:m:d", $CurrentTime);
        if (!empty($RequestForEpg["data"]->epg_listings)) {
            $OnlyDates = [];
            foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                $OnlyDateVar = date("Y:m:d", strtotime($ResVal->start));
                $ValDate = date("d/m/Y", strtotime($ResVal->start));
                if ($CurrentDate <= $OnlyDateVar) {
                    $OnlyDates[$OnlyDateVar] = $ValDate;
                }
            }
            if (!empty($OnlyDates)) {
                $TotalDates = count($OnlyDates);
                $Counter = 1;
                echo "           \n              <div class=\"card-header\"> \n                  <ul class=\"nav nav-tabs text-light\">\n                      ";
                foreach ($OnlyDates as $OnlyDate => $Val) {
                    if ($Counter <= 4) {
                        echo "                          <li class=\"nav-item \">\n                              <a href=\"#TabNo";
                        echo $Counter;
                        echo "\" class=\"nav-link ";
                        echo $Counter == 1 ? "active" : "";
                        echo "\" data-toggle=\"tab\">\n                               ";
                        echo $Val;
                        echo "                       \n                              </a>\n                          </li>\n                          ";
                    }
                    $Counter++;
                }
                if (4 < $TotalDates) {
                    echo "                        <li class=\"nav-item dropdown\">\n                          <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">More</a>\n                          <div class=\"dropdown-menu\">\n                              ";
                    $Counter1 = 1;
                    foreach ($OnlyDates as $OnlyDate => $Val) {
                        if (4 < $Counter1) {
                            echo "                                    <a class=\"dropdown-item\" data-toggle=\"tab\" href=\"#TabNo";
                            echo $Counter1;
                            echo "\">\n                                      ";
                            echo $Val;
                            echo "                                    </a>\n                                ";
                        }
                        $Counter1++;
                    }
                    echo "                          </div>\n                        </li>\n                        ";
                }
                echo "                  </ul>   \n              </div>\n              <div class=\"card-body\">\n                  <div class=\"tab-content\">\n                      ";
                $TabCounter = 1;
                foreach ($OnlyDates as $OnlyDate => $Val) {
                    echo "                        <div class=\"tab-pane fade customTab in ";
                    echo $TabCounter == 1 ? "in active show" : "";
                    echo "\" id=\"TabNo";
                    echo $TabCounter;
                    echo "\">  \n                          ";
                    foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                        $OnlyDateVal = date("Y:m:d", strtotime($ResVal->start));
                        if ($OnlyDateVal == $OnlyDate) {
                            $ACtiveClass = "";
                            $NowPLaying = "";
                            $StartTimming = strtotime($ResVal->start);
                            $EndTimming = strtotime($ResVal->end);
                            if ($StartTimming <= $CurrentTime && $CurrentTime <= $EndTimming) {
                                $ACtiveClass = "NowPlayingActive";
                                $NowPLaying = "(Now Playing)";
                            }
                            echo "                   \n                                   <div class=\"epginfo ";
                            echo $ACtiveClass;
                            echo "\">\n                                     ";
                            echo date($Formatis, $StartTimming);
                            echo "                                      -\n                                      ";
                            echo date($Formatis, $EndTimming);
                            echo "                                      &nbsp; \n                                      ";
                            echo base64_decode($ResVal->title);
                            echo " \n                                      &nbsp;\n                                      ";
                            echo $NowPLaying;
                            echo "                                  </div>  \n                                  ";
                        }
                    }
                    echo " \n                        </div>\n                        ";
                    $TabCounter++;
                }
                echo "  \n                  </div>\n              </div> \n            ";
            } else {
                echo "";
                exit;
            }
        } else {
            echo "";
            exit;
        }
    } else {
        echo "";
        exit;
    }
}
if (isset($_POST["action"]) && $_POST["action"] == "GetCaptchaEPGByStreamid") {
    $CurrentPcDateTime = new DateTime($_POST["currentTime"]);
    $CurrentTime = $CurrentPcDateTime->getTimestamp();
    $GlobalTimeFormat = "24";
    $Formatis = "h:i A";
    if ($GlobalTimeFormat == "24") {
        $Formatis = "H:i";
    }
    $StreamId = $_POST["StreamId"];
    $CommonController = new CommonController();
    $clientcontrolfunctions = new clientcontrolfunctions();
    $RequestForEpg = $CommonController->getEpgDataByCateGoryID($StreamId);
    if (!empty($RequestForEpg) && $RequestForEpg["result"] == "success") {
        $CurrentDate = date("Y:m:d", $CurrentTime);
        if (!empty($RequestForEpg["data"]->epg_listings)) {
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
                echo "           \n              <div class=\"card-header\"> \n                  <ul class=\"nav nav-tabs text-light\">\n                      ";
                foreach ($OnlyDates as $OnlyDate => $Val) {
                    if ($Counter <= 3) {
                        echo "                          <li class=\"nav-item \">\n                              <a href=\"#TabNo";
                        echo $Counter;
                        echo "\" class=\"nav-link ";
                        echo $Counter == 1 ? "active" : "";
                        echo "\" data-toggle=\"tab\">\n                               ";
                        echo $Val;
                        echo "                       \n                              </a>\n                          </li>\n                          ";
                    }
                    $Counter++;
                }
                if (3 < $TotalDates) {
                    echo "                        <li class=\"nav-item dropdown\">\n                          <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">More</a>\n                          <div class=\"dropdown-menu\">\n                              ";
                    $Counter1 = 1;
                    foreach ($OnlyDates as $OnlyDate => $Val) {
                        if (3 < $Counter1) {
                            echo "                                    <a class=\"dropdown-item\" data-toggle=\"tab\" href=\"#TabNo";
                            echo $Counter1;
                            echo "\">\n                                      ";
                            echo $Val;
                            echo "                                    </a>\n                                ";
                        }
                        $Counter1++;
                    }
                    echo "                          </div>\n                        </li>\n                        ";
                }
                echo "                  </ul>   \n              </div>\n              <div class=\"card-body\">\n                  <div class=\"tab-content\">\n                      ";
                $TabCounter = 1;
                $CaptchaCounter = 1;
                foreach ($OnlyDates as $OnlyDate => $Val) {
                    echo "                        <div class=\"tab-pane fade customTab in ";
                    echo $TabCounter == 1 ? "in active show" : "";
                    echo "\" id=\"TabNo";
                    echo $TabCounter;
                    echo "\">  \n                          ";
                    foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                        if ($ResVal->has_archive == 1) {
                            $OnlyDateVal = date("Y:m:d", strtotime($ResVal->start));
                            if ($OnlyDateVal == $OnlyDate) {
                                $ACtiveClass = "";
                                $NowPLaying = "";
                                $StartTimming = strtotime($ResVal->start);
                                $EndTimming = strtotime($ResVal->end);
                                $interval = abs($EndTimming - $StartTimming);
                                $minutes = round($interval / 60);
                                echo "                   \n                                       <div class=\"epginfo catchupclick ";
                                echo $ACtiveClass;
                                echo " cp-";
                                echo $CaptchaCounter;
                                echo "\" data-timediff=\"";
                                echo $minutes;
                                echo "\" data-starttime=\"";
                                echo date("Y-m-d:H-i", $StartTimming);
                                echo "\" data-streamid=\"";
                                echo $StreamId;
                                echo "\">\n                                         ";
                                echo date($Formatis, $StartTimming);
                                echo "                                          -\n                                          ";
                                echo date($Formatis, $EndTimming);
                                echo "                                          &nbsp; \n                                          ";
                                echo base64_decode($ResVal->title);
                                echo " \n                                          &nbsp;\n                                          ";
                                echo $NowPLaying;
                                echo "                                      </div>  \n                                      ";
                                $CaptchaCounter++;
                            }
                        }
                    }
                    echo " \n                        </div>\n                        ";
                    $TabCounter++;
                }
                echo "  \n                  </div>\n              </div> \n            ";
                exit;
            } else {
                echo "";
                exit;
            }
        } else {
            echo "";
            exit;
        }
    } else {
        echo "";
        exit;
    }
} else {
    if (isset($_POST["action"]) && $_POST["action"] == "getchannelepgdata") {
        $DatabaseObj = new DBConnect();
        $conn = $DatabaseObj->makeconnection();
        $StreamId = $_POST["StreamId"];
        $runtime = $_POST["runtime"];
        $CurrentPcDateTime = new DateTime($_POST["currentTime"]);
        $CommonController = new CommonController();
        $clientcontrolfunctions = new clientcontrolfunctions();
        $RequestForEpg = $CommonController->getEpgDataByCateGoryID($StreamId);
        $GlobalTimeFormat = $clientcontrolfunctions->webtvpanel_gettimeformart($conn);
        $CurrentTime = $CurrentPcDateTime->getTimestamp();
        $night12AM = strtotime(date("Ymd", $CurrentTime));
        if (!empty($RequestForEpg) && $RequestForEpg["result"] == "success") {
            if (!empty($RequestForEpg["data"]->epg_listings)) {
                $OnlyDate = date("Y:m:d");
                $totalvaidEPG = 1;
                foreach ($RequestForEpg["data"]->epg_listings as $ResVal) {
                    $OnlyDateVal = date("Y:m:d", strtotime($ResVal->start));
                    if ($OnlyDateVal == $OnlyDate) {
                        $StartTimming = strtotime($ResVal->start);
                        $EndTimming = strtotime($ResVal->end);
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
                        if ($totalvaidEPG == 1) {
                            $starttimedifference = ($StartTimming - $night12AM) / 60;
                            if (0 < $starttimedifference) {
                                $startwidthbymins = $starttimedifference * 8;
                                echo "                            <div class=\"programme noneresult ";
                                echo $ACtiveClass;
                                echo "\" style=\"width: ";
                                echo $startwidthbymins;
                                echo "px;\" >\n                               <input type=\"hidden\" value=\"";
                                echo $Checking;
                                echo "\" >\n                               <a href=\"#\" class=\"inner-excepta\" data-epgdescription=\"";
                                echo base64_decode($ResVal->description);
                                echo "\" data-streamselector=\"";
                                echo $StreamId;
                                echo "\" data-epgtitle=\"";
                                echo base64_decode($ResVal->title);
                                echo "\">\n                                  <h6 class=\"title\">";
                                echo base64_decode($ResVal->title);
                                echo "</h6>\n                               </a>\n                              </div>\n\n                        ";
                            }
                        }
                        echo "                  <div class=\"programme successfound ";
                        echo $ACtiveClass;
                        echo " \" style=\"width: ";
                        echo $widthbymins;
                        echo "px;\">\n                  <input type=\"hidden\" value=\"";
                        echo $Checking;
                        echo "\" >\n                    <a href=\"#\" class=\"inner-excepta ";
                        echo $NowPLayingselector;
                        echo "\" data-epgdescription=\"";
                        echo base64_decode($ResVal->description);
                        echo "\" data-streamselector=\"";
                        echo $StreamId;
                        echo "\" data-epgtitle=\"";
                        echo base64_decode($ResVal->title);
                        echo "\">\n                      <h6 class=\"title\">\n                        ";
                        echo base64_decode($ResVal->title);
                        echo "                      </h6>\n                    </a>\n                  </div>\n              ";
                        $totalvaidEPG++;
                    }
                }
                exit;
            } else {
                echo $clientcontrolfunctions->webtvpanel_noEPGresultfoundprogram($StreamId);
                exit;
            }
        } else {
            echo $clientcontrolfunctions->webtvpanel_noEPGresultfoundprogram($StreamId);
            exit;
        }
    }
}

?>