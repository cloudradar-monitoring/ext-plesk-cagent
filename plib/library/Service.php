<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_Service extends pm_SystemService_Service
{
    public function getName()
    {
        return 'cagent';
    }

    public function getId ()
    {
        return 'cagent';
    }

    public function onStart()
    {
        $result = pm_ApiCli::callSbin('runner', ['--service_start'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function onStop()
    {
        $result = pm_ApiCli::callSbin('runner', ['--service_stop'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function onRestart()
    {
        $result = pm_ApiCli::callSbin('runner', ['--service_restart'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function isRunning()
    {
        $result = pm_ApiCli::callSbin('runner', ['--service_status'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function isConfigured()
    {
        $result = pm_ApiCli::callSbin('runner', ['-t'], pm_ApiCli::RESULT_FULL);

        return $result;
    }
}