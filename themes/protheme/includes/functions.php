<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

function webtvpanel_CallApiRequest($ApiLinkIs = "")
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
function webtvpanel_encrypt($q, $salt = "WEBTVPLAYER")
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
function webtvpanel_decrypt($q, $salt = "WEBTVPLAYER")
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

?>