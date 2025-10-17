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
$ActivethemeIs = isset($ConfigDetails["theme"]) && $ConfigDetails["theme"] != "" ? $ConfigDetails["theme"] : "";
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
$CommonController->GenerateThemeActivationCodesForDefault($conn, $LicenseIS);
if ($ValidLicense != "Active") {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
$titleis = "Configuration Settings";
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
$portalslinks = isset($ConfigDetails["portallinks"]) && $ConfigDetails["portallinks"] != "" ? $ConfigDetails["portallinks"] : "";
$FportalLinks = [""];
if ($portalslinks != "") {
    $FportalLinks = unserialize($portalslinks);
}
$paranset = ["title" => "Theme list", "activemenu" => "THEME LIST", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
if ($_FILES["zip_file"]["name"]) {
    $filename = $_FILES["zip_file"]["name"];
    $source = $_FILES["zip_file"]["tmp_name"];
    $type = $_FILES["zip_file"]["type"];
    $extractto = ADMINFILESDIRECTORY . "themes/";
    $name = explode(".", $filename);
    $accepted_types = ["application/zip", "application/x-zip-compressed", "multipart/x-zip", "application/x-compressed"];
    foreach ($accepted_types as $mime_type) {
        if ($mime_type == $type) {
            $okay = true;
            $continue = strtolower($name[1]) == "zip" ? true : false;
            if (!$continue) {
                $message = "The file you are trying to upload is not a .zip file. Please try again.";
            }
            $target_path = "../themes/" . $filename;
            if (move_uploaded_file($source, $target_path)) {
                $zip = new ZipArchive();
                $x = $zip->open($target_path);
                if ($x === true) {
                    $zip->extractTo($extractto);
                    $zip->close();
                    unlink($target_path);
                }
                $message = "Your .zip file was uploaded and unpacked.";
            } else {
                $message = "There was a problem with the upload. Please try again.";
            }
        }
    }
}
echo "    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-picture-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"theme_list.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      <div class=\"row mb-3\">        \n        <div class=\"col-md-12 mb-2\">\n          ";
if ($message) {
    echo "          <div class=\"alert alert-info alert-dismissible\" role=\"alert\">\n            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n            <h4>";
    echo $message;
    echo "</h4>\n          </div>\n          ";
}
echo "          <button class=\"btn btn-info showuploadtheme\"><i class=\"fa fa-plus\"></i> Add New</button>\n          <a href=\"https://www.whmcssmarters.com/webtv-player-theme/\" class=\"float-right text-underline\" target=\"_blank\">Buy New Theme</a>\n        </div> \n        <div class=\"col-md-6 offset-md-3\">\n          <div class=\"card p-2 theme-upload d-none\">\n              <form enctype=\"multipart/form-data\" method=\"post\" action=\"\">\n                <div class=\"form-group\">\n                    <label>Choose a zip file to upload:</label>\n                    <input type=\"file\" name=\"zip_file\" class=\"form-control\" />               \n                    \n                </div>\n                <button type=\"submit\" name=\"submit\" class=\"btn btn-success\" value=\"Upload\">Submit</button>\n              </form>\n          </div>\n        </div>  \n      </div>\n      ";
$pathtogetimage = ADMINFILESDIRECTORY . "themes";
$imageArrays = [];
$imageArrays = readdirs($pathtogetimage);
$ThemeFileInfo = readthemefile($pathtogetimage);
$themeNameArray = [];
foreach ($ThemeFileInfo as $key => $value) {
    $OnlyThemeName = "";
    $ThemeFolderName = $dir_path = str_replace(ADMINFILESDIRECTORY . "themes/", "", $key);
    if (file_exists("../" . $value[0])) {
        include_once "../" . $value[0];
        $OnlyThemeName = isset($ThemeName) && !empty($ThemeName) ? $ThemeName : "";
    }
    $themeNameArray[$ThemeFolderName] = $OnlyThemeName;
}
echo "      <div class=\"row\">\n        ";
if (!empty($imageArrays)) {
    foreach ($imageArrays as $dir_path => $image) {
        $FullRootPath = $dir_path;
        $ThemeNameIS = $dir_path = str_replace(ADMINFILESDIRECTORY . "themes/", "", $dir_path);
        $GetThemeActivationCode = $CommonController->GetThemeActivationCode($conn, $ThemeNameIS);
        $CheckThemeActivationCode = $CommonController->CheckThemeActivationCode($conn, $GetThemeActivationCode, $ThemeNameIS, $LicenseIS);
        $show_path = str_replace("_", " ", $dir_path);
        $pathInfo = pathinfo($image[0], PATHINFO_DIRNAME);
        $dir_is = str_replace("themes/", "", $pathInfo);
        echo "                <div class=\"col-md-3 col-sm-4 col-12 p-0\">\n                    <div class=\"card m-2\" style=\"height: 300px;\">                      \n                      <input type=\"hidden\" value=\"";
        echo $dir_path;
        echo "\">\n                      <img src =\"";
        echo !empty($image) ? "../" . $image[0] : "images/no-image-icon.png";
        echo "\" alt=\"\" class=\"img-responsive theme-img-card\"  />\n                      <div class=\"card-body\">\n                        <p class=\"theme-title\"><b>Theme Name:</b> &nbsp;  ";
        echo isset($themeNameArray[$ThemeNameIS]) && !empty($themeNameArray[$ThemeNameIS]) ? $themeNameArray[$ThemeNameIS] : "UNKNOWN";
        echo "</p>\n                        ";
        if ($CheckThemeActivationCode == "Active") {
            $TxtShow = "Activate";
            $BtnDisable = "";
            if ($ActivethemeIs == $dir_path) {
                $TxtShow = "Activated";
                $BtnDisable = "disabled";
            }
            echo "\t\n\t                        <button type=\"button\" class=\"btn btn-activate\" data-theme=\"";
            echo $dir_path;
            echo "\" ";
            echo $BtnDisable;
            echo ">";
            echo $TxtShow;
            echo "</button>\n\t                        ";
        } else {
            echo "\t\n\t                        <button type=\"button\" class=\"btn btn-addcode\" data-theme=\"";
            echo $dir_path;
            echo "\">ADD ACTIVATION CODE</button>\n\t                        ";
        }
        echo "                      </div>\n                    </div>\n                </div>\n                ";
    }
}
echo "        \n      </div>  \n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "<!-- Modal -->\n<div id=\"AddCodeModal\" class=\"modal fade\" role=\"dialog\" data-keyboard=\"false\" data-backdrop=\"static\">\n  <div class=\"modal-dialog\" style=\"max-width: 700px;\">\n\n    <!-- Modal content-->\n    <div class=\"modal-content\">\n      <div class=\"modal-header\">\n        <h4 class=\"modal-title\">Add activation code of <span id=\"themenmaehere\" style=\"text-decoration: underline;\"></span></h4>\n      </div>\n      <div class=\"modal-body\">\n        <input type=\"text\" id=\"activationthemecode\" class=\"form-control\" placeholder=\"ENTER CODE HERE\">\n      </div>\n      <div class=\"modal-footer\">\n        <button type=\"button\" class=\"btn btn-primary\" id=\"checkandsaveactivationcode\" data-selectedthemeis=\"\">CHECK AND ACTIVATE THEME</button>\n        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>\n      </div>\n    </div>\n\n  </div>\n</div>\n\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n      \$('.showuploadtheme').click(function(){\n        \$('.theme-upload').toggleClass('d-none');\n      });\n\n      \$('.btn-activate').click(function(){\n        activate_theme = \$(this).data('theme');\n        jQuery.ajax({\n          type:\"POST\",\n          url:\"includes/ajax-control.php\",\n          dataType:\"text\",\n          data:{\n            action:'activatetheme',\n            activate_theme:activate_theme\n            },  \n          success:function(response2){\n            var responseObj = jQuery.parseJSON(response2);\n             if(responseObj.result == \"success\")\n             {                      \n                 Swal.fire({\n                    position: 'center',\n                    title: responseObj.message,\n                    type: 'success',\n                    showCancelButton: false,\n                    confirmButtonColor: '#3085d6',\n                    confirmButtonText: 'Okay'\n                  }).then((result) => {\n                    if (result.value) {\n                         window.location.href =  'theme_list.php';\n                    }\n                  });                      \n             }else{\n                Swal.fire({\n                  position: 'center',\n                  type: 'error',\n                  title: responseObj.message,\n                  showConfirmButton: false,\n                  timer: 1500\n                });\n             }\n          }\n        }); \n      });\n\n      \$('.btn-addcode').click(function(){\n      \tSelectedTheme = \$(this).data('theme');\n      \t\$(\"#themenmaehere\").text(SelectedTheme);\n      \t\$(\"#checkandsaveactivationcode\").data(\"selectedthemeis\",SelectedTheme);\n      \t\$(\"#AddCodeModal\").modal(\"show\");\n      \t //alert(SelectedTheme);\n      });\n\n\t\$('#checkandsaveactivationcode').click(function(){\n\t\tselectedthemeis = \$(this).data(\"selectedthemeis\");\n\t\tCode = \$(\"#activationthemecode\").val();\n\t\tif(Code != \"\")\n\t\t{\n\t\t\tjQuery.ajax({\n\t          type:\"POST\",\n\t          url:\"includes/ajax-control.php\",\n\t          dataType:\"text\",\n\t          data:{\n\t            action:'checkandsaveactivationcode',\n\t            selectedthemeis:selectedthemeis,\n\t            code:Code\n\t            },  \n\t          success:function(response2){\n\t          \tvar obj = jQuery.parseJSON(response2);\n\t          \tif(obj.result == \"error\")\n\t          \t{\n\t          \t\tSwal.fire({\n\t                    position: 'center',\n\t                    type: 'error',\n\t                    title: \"Invalid Code\",\n\t                    showConfirmButton: false,\n\t                    timer: 1500\n\t                  })\n\t          \t}\n\t          \telse\n\t          \t{\n\t          \t\t\$(\"#AddCodeModal\").modal(\"hide\");\n\t          \t\tSwal.fire({\n\t                    text: \"Theme Activation code is saved..\",\n\t                    type: 'success',\n\t                    allowOutsideClick: false,\n\t                    showCancelButton: false,\n\t                    showConfirmButton: false\n\t                  })\n\t                  setTimeout(function(){ \n\t                    window.location.href = 'theme_list.php'; \n\t                   }, 2000);\n\t          \t}\n\t          }\n\t        }); \n\t\t}\n\t\telse\n\t\t{\n\t\t\t\$(\"#activationthemecode\").addClass(\"is-valid\");\n\t\t}\n\t});\n\n\t\t\$(\".is-valid\").click(function(){\n\t\t\t\$(this).removeClass(\"is-valid\");\n\t\t});\n\n      \$('.btn-del-theme').click(function(){\n        del_theme = \$(this).data('theme');\n      });\n\n    });\n\n\n  </script>";
function readDirs($path)
{
    $imageArray = [];
    $infoArray = [];
    $dirHandle = opendir($path);
    while ($item = readdir($dirHandle)) {
        $newPath = $path . "/" . $item;
        if (is_dir($newPath) && $item != "." && $item != "..") {
            $directory = $newPath;
            $images = glob($directory . "/*.jpg");
            $imageArray[$newPath] = str_replace(ADMINFILESDIRECTORY, "", $images);
        }
    }
    return $imageArray;
}
function readThemeFile($path)
{
    $infoArray = [];
    $dirHandle = opendir($path);
    while ($item = readdir($dirHandle)) {
        $newPath = $path . "/" . $item;
        if (is_dir($newPath) && $item != "." && $item != "..") {
            $directory = $newPath;
            $infopath = glob($directory . "/info.php");
            $infoArray[$newPath] = str_replace(ADMINFILESDIRECTORY, "", $infopath);
        }
    }
    return $infoArray;
}

?>