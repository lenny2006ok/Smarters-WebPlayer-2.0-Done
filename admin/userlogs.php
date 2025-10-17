<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("ADMINFILESDIRECTORY", dirname(dirname(__FILE__)) . "/");
if (file_exists(ADMINFILESDIRECTORY . "connection.php")) {
    include_once ADMINFILESDIRECTORY . "connection.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Admin/AdminContoller.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Admin/AdminContoller.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Admin/Controller.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Admin/Controller.php";
}
if (file_exists(ADMINFILESDIRECTORY . "admin/includes/functions.php")) {
    include_once ADMINFILESDIRECTORY . "admin/includes/functions.php";
}
if (file_exists(ADMINFILESDIRECTORY . "lib/Common/CommonController.php")) {
    include_once ADMINFILESDIRECTORY . "lib/Common/CommonController.php";
}
$DatabaseObj = new DBConnect();
$controlfunctions = new controlfunctions();
$conn = $DatabaseObj->makeconnection();
$CommonController = new CommonController();
if (array_key_exists("dberror", $conn)) {
    exit("You are not connected to the database!!");
}
$ConfigDetails = $CommonController->getconfigurationoption($conn, "1");
$LicenseIS = isset($ConfigDetails["license"]) && $ConfigDetails["license"] != "" ? $ConfigDetails["license"] : "";
$LicenseLocalKey = isset($ConfigDetails["localKey"]) && $ConfigDetails["localKey"] != "" ? $ConfigDetails["localKey"] : "";
$portalslinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$FportalLinks = [];
if ($portalslinks != "") {
    $FportalLinks = unserialize($portalslinks);
}
$ValidLicense = "";
if ($LicenseIS != "") {
    $checkLicense = $CommonController->checklicense($LicenseIS, $LicenseLocalKey);
    if ($checkLicense["status"] == "Active") {
        $ValidLicense = $checkLicense["status"];
        if (isset($checkLicense["localkey"]) && !empty($checkLicense["localkey"])) {
            $updateLocalKey = $CommonController->updatelocalkey($checkLicense["localkey"], $conn);
        }
    }
}
if ($ValidLicense != "Active") {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
$titleis = "UNBLOCK IP's";
$dispatcher = new AdminContoller();
$dispatcher->dispatch("createallrecommendedtables", $conn);
$checkadminlogin = $dispatcher->dispatch("checkadminlogin");
if ($checkadminlogin != "1") {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$CommonController->addActivityOnload($conn);
$LogoIs = isset($ConfigDetails["logo"]) && $ConfigDetails["logo"] != "" ? $ConfigDetails["logo"] : "../images/blackdemo-Logo.jpg";
$SitetileIs = isset($ConfigDetails["sitetitle"]) && $ConfigDetails["sitetitle"] != "" ? $ConfigDetails["sitetitle"] : "";
$paranset = ["title" => "USERS LOGS", "activemenu" => "USERLOGS", "logovalue" => $LogoIs, "breadcrumblink" => "userlogs.php", "license" => $ValidLicense];
if (isset($_POST["blockedids"])) {
    $ExplodedIDS = explode(",", $_POST["blockedids"]);
    $DeleteBlockedIdsWithIdsArray = $CommonController->DeleteBlockedIdsWithIdsArray($ExplodedIDS, $conn);
    if ($DeleteBlockedIdsWithIdsArray == "1") {
        echo "      <script type=\"text/javascript\">\n        localStorage.setItem(\"deletesuccess\", \"yes\");\n        window.location.href = \"unblockip.php\";\n      </script>  \n      ";
        exit;
    }
}
if (isset($_POST["searchFilter"]) && $_POST["searchFilter"] == "Search") {
    $user = !empty($_POST["se_username"]) ? $_POST["se_username"] : "";
    $portal = !empty($_POST["se_portal"]) ? $_POST["se_portal"] : "";
    $status = !empty($_POST["se_status"]) ? $_POST["se_status"] : "";
    $ForTotalCount = $CommonController->getloggeduserslist($conn, 0, 0, $user, $portal, $status);
} else {
    $ForTotalCount = $CommonController->getloggeduserslist($conn, 0, 0);
}
$totalRecords = isset($ForTotalCount["total"]) && !empty($ForTotalCount["total"]) ? $ForTotalCount["total"] : "";
$limitnum = 50;
if (isset($_REQUEST["pageno"])) {
    $nextPage = $_REQUEST["pageno"] + 1;
    $currentPage = $_REQUEST["pageno"];
    $previousPage = $_REQUEST["pageno"] - 1;
    if ($_REQUEST["pageno"] != 1) {
        $limitstart = $_REQUEST["pageno"] * $limitnum - $limitnum;
    } else {
        $limitstart = 0;
    }
} else {
    $currentPage = 1;
    $nextPage = 2;
    $limitstart = 0;
}
$totalPage = ceil($totalRecords / $limitnum);
$Limit = $limitnum;
$Offset = $limitstart;
if (isset($_POST["searchFilter"]) && $_POST["searchFilter"] == "Search") {
    $user = !empty($_POST["se_username"]) ? $_POST["se_username"] : "";
    $portal = !empty($_POST["se_portal"]) ? $_POST["se_portal"] : "";
    $status = !empty($_POST["se_status"]) ? $_POST["se_status"] : "";
    $loggeduserslist = $CommonController->getloggeduserslist($conn, $Limit, $Offset, $user, $portal, $status);
} else {
    $loggeduserslist = $CommonController->getloggeduserslist($conn, $Limit, $Offset);
}
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
echo "\n<style type=\"text/css\">\n.eyecheck {\n    position: relative;\n    right: 12px;\n    float: right;\n    top: 40px;\n    cursor: pointer;\n}\n.eyecheck:hover {\n  color: #8c9bab;\n  }\n\n\n\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n.pagination-container a {\n    margin: 1px;\n    padding: 2px 5px;\n    border-radius: 1px;\n    border: 1px solid #c1c1ec;\n}\n\n.pagination-container a:hover {\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n.active-paging{\n    color: #fff;\n    border: 1px solid #5e5e73;\n    background-color: #8b8bf7;\n}\n\nbody {\n  background: #000;\n    font-family: sans-serif;\n}\n.ques {\n    color: darkslateblue;\n}\n\n/* toggle css start here*/\n.onoffswitch {\n    position: relative; width: 80px;\n    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;\n}\n.onoffswitch-checkbox {\n    display: none;\n}\n.onoffswitch-label {\n    display: block; overflow: hidden; cursor: pointer;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n}\n.onoffswitch-inner {\n    display: block; width: 200%; margin-left: -100%;\n    transition: margin 0.3s ease-in 0s;\n}\n.onoffswitch-inner:before, .onoffswitch-inner:after {\n    display: block; float: left; width: 50%; height: 36px; padding: 0; line-height: 36px;\n    font-size: 15px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;\n    box-sizing: border-box;\n}\n.onoffswitch-inner:before {\n    content: \"\";\n    padding-left: 10px;\n    background-color: #FFFFFF; color: #FFFFFF;\n}\n.onoffswitch-inner:after {\n    content: \"\";\n    padding-right: 10px;\n    background-color: #FFFFFF; color: #666666;\n    text-align: right;\n}\n.onoffswitch-switch {\n    display: block; width: 28px; margin: 4px;\n    background: #A1A1A1;\n    position: absolute; top: 0; bottom: 0;\n    right: 40px;\n    border: 2px solid #E3E3E3; border-radius: 36px;\n    transition: all 0.3s ease-in 0s; \n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-inner {\n    margin-left: 0;\n}\n.onoffswitch-checkboxchecked + .onoffswitch-label .onoffswitch-switch {\n    right: 0px; \n    background-color: #27A1CA; \n}\n.user-status-td{\n  position: relative;\n}\n.user-status{\n  position: absolute;\n  top: 25%;\n}\n\n\nspan.commonidentifire {\n    padding: 0px 6px;\n    border-radius: 45px;\n}\nspan.active-identifire {\n  background-color: #27a1ca;\n}\nspan.blocked-identifire {\n  background-color: #a1a1a1;\n}\n.searchfiltercontainer\n{\n    margin-bottom: 20px;\n}\n.btncontainersec\n{\n    text-align: right;\n}\n.searchfilterbtn {\n    color: #fff !important;\n}\n.eyeicon\n{\n  cursor :pointer;\n}\n</style>\n<script>\n  function passwordshow(passvalue){\n     \n          \$(\".toggle-password\"+passvalue).toggleClass(\"fa-eye-slash\");\n          if (\$(\"#secret\"+passvalue).is(\":hidden\"))\n          {\n          \n            \$(\"#secret\"+passvalue).removeClass(\"hidden\");\n          \$(\"#star\"+passvalue).addClass(\"hidden\");\n          }\n         else{\n          \$(\"#secret\"+passvalue).addClass(\"hidden\");\n          \$(\"#star\"+passvalue).removeClass(\"hidden\");\n         }\n          \n    }\n  </script>\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-id-card-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"userlogs.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row\">\n        <div class=\"col-md-12\">\n          <div class=\"tile\">\n            <div class=\"row\">\n              <div class=\"col-md-12\">\n                <h3 class=\"tile-title\">";
echo $paranset["title"];
echo " LIST</h3>\n              </div>\n            </div>\n            <div class=\"row\">\n              <div class=\"col-md-12 searchfiltercontainer\">\n                <div class=\"row\">\n                   <div class=\"col-md-12 btncontainersec\">\n                      <a class=\"btn btn-primary searchfilterbtn\" style=\"\">\n                        Search Filter\n                      </a>\n                   </div>\n                </div>\n              </div>\n            </div>\n            <div class=\"row\">\n             <div class=\"col-md-10 offset-md-1\">\n          <div class=\"card form-upload p-2 ";
echo !isset($_POST["searchFilter"]) ? "d-none" : "";
echo "\">\n            <form class=\"form\" id=\"search_form\" method=\"POST\" enctype=\"multipart/form-data\">\n              <div class=\"form-group\">\n                <legend>Search and Filter</legend>\n                <label>By Username</label>\n                <input type=\"text\" id=\"se_username\" class=\"form-control\" name=\"se_username\" value=\"";
echo isset($_POST["se_username"]) ? $_POST["se_username"] : "";
echo "\"><br>\n                <label>By Portal Link</label>\n                <!-- <input type=\"text\" id=\"se_portal\" class=\"form-control\" name=\"se_portal\"><br> -->\n                <select name=\"se_portal\" class=\"form-control\">\n                    <option  value=\"\">None</option>\n                    ";
if (!empty($FportalLinks)) {
    foreach ($FportalLinks as $SkVal) {
        $bar = "/";
        if (substr($SkVal, -1) == "/") {
            $bar = "";
        }
        $SkVal = $SkVal . $bar;
        echo "                            <option value=\"";
        echo $SkVal;
        echo "\" ";
        echo isset($_POST["se_portal"]) && $_POST["se_portal"] == $SkVal ? "selected='selected'" : "";
        echo ">";
        echo $SkVal;
        echo "</option>\n                            ";
    }
}
echo "                </select>\n                <label>By Status</label> <br><br>\n               <select name=\"se_status\" class=\"form-control\">\n                  <option value=\"\">None</option>\n                  <option value=\"Active\" ";
echo isset($_POST["se_status"]) && $_POST["se_status"] == "Active" ? "selected='selected'" : "";
echo ">Active</option>\n                  <option value=\"Blocked\" ";
echo isset($_POST["se_status"]) && $_POST["se_status"] == "Blocked" ? "selected='selected'" : "";
echo ">Blocked</option>  \n              </select>\n              </div>\n              <a href=\"userlogs.php\" class=\"btn btn-primary float-right ml-2 ";
echo empty($_POST["se_status"]) ? "disabled" : "";
echo "\">Clear</a>\n              <button type=\"submit\" name=\"searchFilter\" value=\"Search\" class=\"btn btn-success\" style=\"float:right\">Search</button>\n            </form>\n          </div>\n        </div>\n        </div><br>\n            ";
if (isset($loggeduserslist["data"]) && !empty($loggeduserslist["data"])) {
    echo "              <p style=\"text-align: right;\">Total Records: <b>";
    echo $totalRecords;
    echo "</b></p>\n              <table class=\"table\">\n                <thead>\n                  <tr>\n                    <th>#</th>\n                    <th>PORTAL LINK</th>\n                    <th>USERNAME</th>\n                    <th>PASSWORD</th>\n                    <th>\n                         \n                        <div class=\"identifiresection\">\n                          <span class=\"commonidentifire active-identifire\">&nbsp;</span> Acitve / \n                          <span class=\"commonidentifire blocked-identifire\">&nbsp;</span> Blocked\n                        </div>\n                    </th>\n                    <th>LOGS</th>\n                  </tr>\n                </thead>\n                <tbody>\n                  ";
    foreach ($loggeduserslist["data"] as $key) {
        echo "                    <tr>\n                      <td>";
        echo $key["id"];
        echo "</td>\n                      <td>";
        echo $key["portallink"];
        echo "</td>\n                      <td>";
        echo $key["username"];
        echo "</td>\n                      <td><span id=\"secret";
        echo $key["id"];
        echo "\" class=\"starsof-";
        echo $key["id"];
        echo " hidden\">";
        echo $controlfunctions->webtvtheme_decrypt($key["password"]);
        echo "</span>\n                        <span >\n                          <span id =\"star";
        echo $key["id"];
        echo "\">*********************</span>\n                       <span toggle=\"#secret";
        echo $key["id"];
        echo "\" class=\"fa fa-fw fa-eye field-icon fa-eye-slash toggle-password";
        echo $key["id"];
        echo " eyeicon\" onclick=\"passwordshow(";
        echo $key["id"];
        echo ");\"></span></td>\n\n                      <td class=\"user-status-td\">\n                        <div class=\"onoffswitch\">                          \n                            <input type=\"checkbox\" name=\"onoffswitch\" class=\"onoffswitch-checkbox ";
        echo $key["status"] == "Active" ? "onoffswitch-checkboxchecked" : "";
        echo "\" id=\"myonoffswitch-";
        echo $key["id"];
        echo "\" data-currentis=\"";
        echo $key["status"] == "Active" ? "1" : "0";
        echo "\" data-rowid=\"";
        echo $key["id"];
        echo "\">\n                            <label class=\"onoffswitch-label\" for=\"myonoffswitch-";
        echo $key["id"];
        echo "\">\n                                <span class=\"onoffswitch-inner\"></span>\n                                <span class=\"onoffswitch-switch\"></span>\n                            </label>\n                        </div>\n                        <!-- <span class=\"user-status\" id=\"textshown-";
        echo $key["id"];
        echo "\">Active</span> -->\n                      </td>\n                      <td>\n                          <a href=\"userexpandlogs.php?u=";
        echo base64_encode($key["id"]);
        echo "\">View</a>\n                      </td>\n                    </tr>\n                    ";
    }
    echo "                </tbody>\n              </table>\n              ";
    if (0 < $totalPage) {
        echo "                <div class=\"pagination-container\">\n                   ";
        for ($i = 1; $i <= $totalPage; $i++) {
            echo "                      <a href=\"userlogs.php?pageno=";
            echo $i;
            echo "\" class=\"";
            echo $i == $currentPage ? "active-paging" : "";
            echo "\">\n                        ";
            echo $i;
            echo "                      </a>\n                      ";
        }
        echo " \n                </div>\n                ";
    }
} else {
    echo "                <center><h4>No Result Found!!</h4></center>\n                ";
}
echo "          </div>\n        </div>\n        <div class=\"clearix\"></div>\n      </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n       \$('.searchfilterbtn').click(function(){\n        \$('.form-upload').toggleClass('d-none');\n      });\n      \$(\".onoffswitch-checkbox\").click(function(e){\n        e.preventDefault();\n        thisvar = \$(this);\n        currentis = thisvar.data(\"currentis\");\n        rowid = thisvar.data(\"rowid\");\n\n        TextData = \"Active\";\n        AjaxRetunTextData = \"Actived\";\n        if(currentis == 1)\n        {\n            TextData = \"block\";\n            AjaxRetunTextData = \"Blocked\";\n        }\n        Swal.fire({\n          title: 'Are you sure?',\n          text: \"You want to \"+TextData+ \" this user!!\",\n          type: 'warning',\n          showCancelButton: true,\n          confirmButtonColor: '#3085d6',\n          cancelButtonColor: '#d33',\n          confirmButtonText: 'Yes, go for it!'\n        }).then((result) => {\n          if (result.value) {\n            Swal.fire({\n              text: \"Processing.....\",\n              allowOutsideClick: false,\n              showCancelButton: false,\n              showConfirmButton: false\n            })\n            jQuery.ajax({\n              type:\"POST\",\n              url:\"includes/ajax-control.php\",\n              dataType:\"text\",\n              data:{\n              action:'userstatusaction',\n              rowid:rowid,\n              currentis:currentis\n              },  \n              success:function(response){\n                swal.close();\n                obj = JSON.parse(response);\n                if(obj.result == \"success\")\n                {\n                    if(currentis == 1)\n                    {\n                        thisvar.data(\"currentis\",0);            \n                        thisvar.removeClass(\"onoffswitch-checkboxchecked\");\n                    }\n                    else\n                    {\n                        thisvar.data(\"currentis\",1);\n                        thisvar.addClass(\"onoffswitch-checkboxchecked\");\n                    }  \n                   /* \$(\"#textshown-\"+rowid).text(AjaxRetunTextData);*/\n\n                      Swal.fire(\n                        'User '+AjaxRetunTextData+' Successfully..',\n                        '',\n                        'success'\n                      )\n                }\n                else\n                {\n                    Swal.fire(\n                        'Seems some technical error please concern with provider',\n                        '',\n                        'error'\n                      )\n                }\n              }\n            });\n          }\n        })\n      });\n       \n    });\n   \n   /*\$(\".toggle-password\").click(function() {\n\n  \$(this).toggleClass(\"fa-eye fa-eye-slash\");\n  var input = \$(\$(this).attr(\"toggle\"));\n  if (input.attr(\"type\") == \"password\") {\n    input.attr(\"type\", \"text\");\n  } else {\n    input.attr(\"type\", \"password\");\n  }\n});*/\n  </script>\n";

?>