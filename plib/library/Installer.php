<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_Installer {
    public function isInstalled() {
        $result = pm_ApiCli::callSbin('installer', ['check', 'installed'], pm_ApiCli::RESULT_FULL);
        $result['version'] = str_replace(PHP_EOL, '', $result['stdout']);
        return $result;
    }

    public function latest() {
        $result = pm_ApiCli::callSbin('installer', ['check', 'latest'], pm_ApiCli::RESULT_FULL);
        $result['version'] = str_replace(PHP_EOL, '', $result['stdout']);
        return $result;
    }


    public function install($params) {
        $result = pm_ApiCli::callSbin('installer', ['install', $params['hub_url'], $params['hub_user'], $params['hub_password']], pm_ApiCli::RESULT_FULL);
        if ($result['code'] !== 0) {
            $result['output'] = $result['stderr'];
        } else {
            $result['output'] = 'installation succeeded';
        }
        return $result;
    }

    public function uninstall() {
        $result = pm_ApiCli::callSbin('installer', ['uninstall'], pm_ApiCli::RESULT_FULL);
        $result['output'] = $result['stdout'];
        return $result;
    }
}