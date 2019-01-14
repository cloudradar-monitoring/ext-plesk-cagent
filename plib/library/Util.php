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

    public function Validate_InstallInput($input) {
        $errors = array();

        if ($input['hub_url'] == '') {
            $errors['hub_url'] = 'Missing Hub URL!';
        } elseif (!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $input['hub_url']) ) {
            $errors['hub_url'] = 'Is not valid url';
        }

        if ($input['hub_user'] == '') {
            $errors['hub_user'] = 'Missing hub user';
        }

        if ($input['hub_password'] == '') {
            $errors['hub_password'] = 'Missing hub password';
        }

        return $errors;
    }
}