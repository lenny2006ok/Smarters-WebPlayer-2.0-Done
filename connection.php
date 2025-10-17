<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

session_start();
define("ABSPATH", dirname(dirname(dirname(__FILE__))) . "/");
define("NEWAOSBS", dirname(__FILE__) . "/");
class DBConnect
{
    protected $db_name = NULL;
    protected $db_user = NULL;
    protected $db_pass = NULL;
    protected $db_host = NULL;
    public function makeconnection()
    {
        if (file_exists(ABSPATH . "dbinfo.php")) {
            include_once ABSPATH . "dbinfo.php";
        } else {
            if (file_exists("../dbinfo.php")) {
                include_once "../dbinfo.php";
            } else {
                if (file_exists("dbinfo.php")) {
                    include_once "dbinfo.php";
                } else {
                    if (file_exists(NEWAOSBS . "dbinfo.php")) {
                        include_once "dbinfo.php";
                    }
                }
            }
        }
        $this->db_name = $dbname;
        $this->db_user = $dbusername;
        $this->db_pass = $dbpassword;
        $this->db_host = $dbhost;
        $rerutnData = [];
        if ($this->db_name != "" && $this->db_user != "" && $this->db_pass != "" && $this->db_host != "") {
            $connect_db = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            if (mysqli_connect_errno()) {
                $rerutnData = ["dberror" => "error", "message" => mysqli_connect_error()];
            } else {
                $rerutnData = $connect_db;
            }
        } else {
            $rerutnData = ["dberror" => "error", "message" => "Database confifueation is pending!!"];
        }
        return $rerutnData;
    }
    public function outerconnction($dbname = "", $dbusername = "", $dbpassword = "", $dbhost = "")
    {
        return $this->connectconnection($dbname, $dbusername, $dbpassword, $dbhost);
    }
    private function connectconnection($dbname = "", $dbusername = "", $dbpassword = "", $dbhost = "")
    {
        $rerutnData = [];
        $this->db_name = $dbname;
        $this->db_user = $dbusername;
        $this->db_pass = $dbpassword;
        $this->db_host = $dbhost;
        $rerutnData = [];
        if ($this->db_name != "" && $this->db_user != "" && $this->db_pass != "" && $this->db_host != "") {
            $connect_db = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            if (mysqli_connect_errno()) {
                $rerutnData = ["dberror" => "error", "message" => mysqli_connect_error()];
            } else {
                $rerutnData = $connect_db;
            }
        } else {
            $rerutnData = ["dberror" => "error", "message" => "Database confifueation is pending!!"];
        }
        return $rerutnData;
    }
}

?>