<?php

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_Util
{
    private $moduleCmd;

    public function __construct()
    {
        $this->Init();
    }

    public function Init()
    {
    }

    public static function get_request_var($tag)
    {
        if (isset($_REQUEST[$tag]))
            return trim($_REQUEST[$tag]);
        else
            return NULL;
    }
}