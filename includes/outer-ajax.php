<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("OUTERAJAXFILEDIRPATH", dirname(dirname(__FILE__)) . "/");
if (isset($_POST["action"]) && $_POST["action"] == "testandsavedetails") {
    $returnData = [];
    if (file_exists(OUTERAJAXFILEDIRPATH . "connection.php")) {
        include_once OUTERAJAXFILEDIRPATH . "connection.php";
    }
    $host = $_POST["hostname"];
    if ($_POST["port"] != "") {
        $host = $host . ":" . $_POST["port"];
    }
    $dbname = $_POST["dbname"];
    $dbusername = $_POST["dbusername"];
    $dbpassword = $_POST["dbpassword"];
    $DatabaseObj = new DBConnect();
    $conn = $DatabaseObj->outerconnction($dbname, $dbusername, $dbpassword, $host);
    if (array_key_exists("dberror", $conn)) {
        $returnData["result"] = "error";
        $returnData["message"] = "Database details are not correct!!";
        echo json_encode($returnData);
        exit;
    }
    if (file_exists(OUTERAJAXFILEDIRPATH . "admin/includes/functions.php")) {
        include_once OUTERAJAXFILEDIRPATH . "admin/includes/functions.php";
    }
    $controlfunctions = new controlfunctions();
    $controlfunctions->createallrecommendedtablesfunction($conn);
    $randpass = rand(0, 999999999);
    $EncPasswordToCheck = $controlfunctions->webtvtheme_encrypt($randpass);
    $deleteQueryData = ["request" => "Delete", "table" => "webtvtheme_admin", "data" => []];
    $controlfunctions->webtvtheme_ExecuteQuery($deleteQueryData, $conn);
    $QueryData = ["request" => "Insert", "table" => "webtvtheme_admin", "data" => ["username" => "admin", "password" => $EncPasswordToCheck, "role" => "1"]];
    $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
    $QueryData = ["request" => "Insert", "table" => "webtvtheme_settings", "data" => ["settings" => "theme", "value" => "default"]];
    $ExecuteQuery = $controlfunctions->webtvtheme_ExecuteQuery($QueryData, $conn);
    $content = "<?php \n";
    $content .= "\$dbhost = \"" . $host . "\";" . "\n";
    $content .= "\$dbusername = \"" . $dbusername . "\";" . "\n";
    $content .= "\$dbpassword = \"" . $dbpassword . "\";" . "\n";
    $content .= "\$dbname = \"" . $dbname . "\";" . "\n";
    $content .= "?>";
    if (file_exists(OUTERAJAXFILEDIRPATH . "dbinfo.php")) {
        unlink(OUTERAJAXFILEDIRPATH . "dbinfo.php");
    }
    $fp = fopen(OUTERAJAXFILEDIRPATH . "dbinfo.php", "w");
    fwrite($fp, $content);
    fclose($fp);
    chmod(OUTERAJAXFILEDIRPATH . "dbinfo.php", 420);
    if (file_exists(OUTERAJAXFILEDIRPATH . "dbinfo.php")) {
        $returnData["result"] = "success";
        $returnData["message"] = "Successfully configured";
        $returnData["data"] = $randpass;
        echo json_encode($returnData);
        exit;
    }
    exit;
}

?>