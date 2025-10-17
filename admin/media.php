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
$paranset = ["title" => "Media files", "activemenu" => "MEDIA FILES", "logovalue" => $LogoIs, "license" => $ValidLicense];
$dispatcher->dispatch("mainheader", $paranset);
$dispatcher->dispatch("mainsidebar", $paranset);
$PathUrl = url_part();
if (isset($_REQUEST["del_file"]) && $_REQUEST["del_file"] == "yes") {
    $imagelink = $_REQUEST["del_filename"];
    $permission = $_REQUEST["del_file"];
    if ($permission == "yes") {
        $dirHandle = glob("../mediafiles/*.*");
        foreach ($dirHandle as $imgPath) {
            if ($imgPath == $imagelink) {
                unlink($imgPath);
                $permission = "done";
            }
        }
        if ($permission == "done") {
            echo "          <script>\n            localStorage.setItem(\"del_file\", \"success\");\n            window.Location.href = 'media.php';\n          </script>\n          ";
        }
    }
}
echo "\n\n    <main class=\"app-content\">\n      <div class=\"app-title\">\n        <div>\n          <h1><i class=\"fa fa-picture-o\"></i> ";
echo $paranset["title"];
echo "</h1>\n          <p>";
echo $paranset["title"];
echo "</p>\n        </div>\n        <ul class=\"app-breadcrumb breadcrumb\">\n          <li class=\"breadcrumb-item\"><a href=\"dashboard.php\"><i class=\"fa fa-home fa-lg\"></i></a></li>\n          <li class=\"breadcrumb-item\"><a href=\"media.php\">";
echo $paranset["title"];
echo "</a></li>\n        </ul>\n      </div>\n      ";
if (isset($_POST["uploadImage"]) && $_POST["uploadImage"] == "uploadme") {
    $FinalLogoPath = "";
    $uploadstatus = 1;
    $imageTypeArray = ["png", "jpg", "jpeg", "gif"];
    $logoImg = $_FILES["fileToUpload"]["name"];
    $logoSize = $_FILES["fileToUpload"]["size"];
    $logotmpname = $_FILES["fileToUpload"]["tmp_name"];
    $logoType = pathinfo($logoImg, PATHINFO_EXTENSION);
    $logotype = strtolower($logoType);
    if (!empty($logoImg)) {
        $filescna = $_FILES["fileToUpload"]["name"];
        $logolink = time() . "mediafiles." . $logoType;
        $uploadpath = "../mediafiles/";
        if (!in_array($logotype, $imageTypeArray)) {
            echo "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\"><h4>Image type is not valid. Please try again.</h4><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div>";
            $uploadstatus = 0;
        }
        if (5000000 < $logoSize) {
            echo "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\"><h4>Image size is too large. Max. size allowed is 2Mb .</h4><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button></div>";
            $uploadstatus = 0;
        }
        if ($uploadstatus == 1) {
            $didUpload = move_uploaded_file($logotmpname, $uploadpath . $logolink);
            if ($didUpload) {
                $FinalLogoPath = $newlogopath = "images/" . $logolink;
                echo "                        <div class=\"alert alert-success alert-dismissible\" role=\"alert\">\n                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n                    <h4>Image Upload Successfully !</h4>\n                  </div>\n                  ";
            } else {
                echo "                        <div class=\"alert alert-danger alert-dismissible\" role=\"alert\">\n                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n                    <h4>Error - in Image upload!</h4>\n                  </div>\n                  ";
            }
        }
    }
}
echo "      <div class=\"row mb-3\">\n        <div class=\"col-md-12\">\n          <button class=\"btn btn-info showuploadform\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i> Add Media</button>\n        </div>\n        <div class=\"col-md-4 offset-md-4\">\n          <div class=\"card form-upload p-2 d-none\">\n            <form class=\"form\" method=\"POST\" enctype=\"multipart/form-data\">\n              <div class=\"form-group\">\n                <legend>Add Image</legend>\n                <input type=\"file\" id=\"file\" class=\"form-control\" name=\"fileToUpload\">\n              </div>\n              <button type=\"submit\" name=\"uploadImage\" value=\"uploadme\" class=\"btn btn-success\">Upload</button>\n            </form>\n          </div>\n        </div>\n      </div>\n\n      <div class=\"row\">\n    ";
$all_files = glob("../mediafiles/*.*");
foreach (array_reverse($all_files) as $imagepath) {
    $image_name = $imagepath;
    $supported_format = ["gif", "jpg", "jpeg", "png"];
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    if (in_array($ext, $supported_format)) {
        $image_nameforfullpath = str_replace("../", "", $image_name);
        $fullPath = $PathUrl . $image_nameforfullpath;
        echo "            <div class=\"col-md-3\" style=\"float: left;\">\n          <div class=\"card media-card\">    \n            <img src=\"";
        echo $image_name;
        echo "\" alt=\"";
        echo $image_name;
        echo "\" class=\"card-img-top img-crd\">\n            <div class=\"card-body\">\n              <span class=\"d-none fileImage\">\n                ";
        echo $fullPath;
        echo "</span>\n               <a  href=\"#\" data-file=\"my file 1\" data-path=\"";
        echo $fullPath;
        echo "\" class=\"btn btn-sm btn-success rename\">Show Path</a>\n               <form method=\"POST\" class=\"del-form\">\n                <input type=\"hidden\" name=\"del_filename\" value=\"";
        echo $image_name;
        echo "\">\n                 <button type=\"submit\" name=\"del_file\" value=\"yes\" class=\"close\" aria-label=\"Close\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button>\n               </form>              \n            </div>\n          </div>\n        </div>\n            ";
    }
}
echo "  </div>\n  <div class=\"modal fade\" id=\"basicModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"basicModal\" aria-hidden=\"true\">\n    <div class=\"modal-dialog\">\n      <div class=\"modal-content\">\n        <div class=\"modal-header\">\n          <h4 class=\"modal-title\" id=\"myModalLabel\">Image Path</h4>\n          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>\n        </div>\n        <div class=\"modal-body\">\n          <div id=\"imgPath\"></div>\n        </div>\n      </div>\n    </div>\n  </div>\n    </main>\n";
$dispatcher->dispatch("mainfooter");
echo "\n  <script type=\"text/javascript\">\n    \$(document).ready(function(){\n      \$('.showuploadform').click(function(){\n        \$('.form-upload').toggleClass('d-none');\n      });\n\n      \$(\".rename\").click(function(e){\n        \$('#imgPath').html('');\n        e.preventDefault();\n        var \$this = \$(this);\n        var fileName = \$(this).data(\"file\");\n          \$(\"#basicModal\").data(\"fileName\", fileName).modal(\"toggle\", \$this);\n         var ImagePath =  \$(this).data(\"path\");\n         \$('#imgPath').html('<input type=\"text\" value=\"'+ImagePath+'\" class=\"form-control\">');\n        \n      });\n      var file_status = localStorage.getItem(\"del_file\");\n    if(file_status == \"success\"){\n      Swal.fire({\n        position: 'center',\n        type: 'success',\n        title: 'Image Remove Successfully!',\n        showConfirmButton: false,\n        timer: 1500\n      })\n      localStorage.removeItem(\"del_file\");\n    }\n\n    });\n\n\n  </script>";
function url_part()
{
    $actual_link = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $actual_link = str_replace("media.php", "", $actual_link);
    $actual_link = str_replace("/admin", "", $actual_link);
    $bar = "/";
    if (substr($actual_link, -1) == "/") {
        $bar = "";
    }
    $actual_link = $actual_link . $bar;
    return $actual_link;
}

?>