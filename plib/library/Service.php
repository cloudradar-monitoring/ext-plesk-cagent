<?php

use Symfony\Component\Process\Process;

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
        $result = pm_ApiCli::callSbin('runner.php', ['--service_start'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function onStop()
    {
        $result = pm_ApiCli::callSbin('runner.php', ['--service_stop'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    public function onRestart()
    {
        $result = pm_ApiCli::callSbin('runner.php', ['--service_restart'], pm_ApiCli::RESULT_FULL);
        return $result;
    }

    /**
     * @return bool|Modules_Cagent_Status
     */
    public function isRunning()
    {
        $process = new Process(['/usr/bin/cagent','--service_status']);
        $process->run();

        if(!$process->isSuccessful()){
            return new Modules_Cagent_Status(false,$process->getErrorOutput());
        }
        if(trim($process->getOutput()) == 'running'){
            return new Modules_Cagent_Status(true,$process->getOutput());
        }else{
            return new Modules_Cagent_Status(false,$process->getOutput());
        }
    }

    /**
     * @return bool|Modules_Cagent_Status
     */
    public function isConfigured()
    {
        $process = new Process(['/usr/bin/cagent','-t']);
        $process->run();

        if(!$process->isSuccessful()){
            return new Modules_Cagent_Status(false,$process->getErrorOutput());
        }

        return new Modules_Cagent_Status(true,$process->getOutput());
    }
}