<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

class ClientContoller
{
    public function dispatch($action, $parameters = [], $variables = [])
    {
        if (!$action) {
            $action = "index";
        }
        $controller = new Controller();
        if (is_callable([$controller, $action])) {
            return $controller->{$action}($parameters, $variables);
        }
    }
}

?>