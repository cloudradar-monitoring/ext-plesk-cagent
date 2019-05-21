<?php

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/
use Symfony\Component\Process\Process;

/**
 * Class Modules_Cloudradar_Service
 * @see https://docs.plesk.com/en-US/onyx/extensions-guide/plesk-features-available-for-extensions/integrate-with-system-services/manage-services.77294/
 */
class Modules_Cloudradar_Service extends pm_SystemService_Service
{
    public function getName()
    {
        return 'Cloudradar';
    }

    public function getId ()
    {
        return 'cloudradar';
    }

    public function onStart()
    {
        $result = pm_ApiCli::callSbin('runner.php', ['-service_start'], pm_ApiCli::RESULT_FULL);
        if(0 !== $result['code']){
            throw new pm_Exception($result['stderr']);
        }

        return $result;
    }

    public function onStop()
    {
        $result = pm_ApiCli::callSbin('runner.php', ['-service_stop'], pm_ApiCli::RESULT_FULL);
        if(0 !== $result['code']){
            throw new pm_Exception($result['stderr']);
        }
        return $result;
    }

    public function onRestart()
    {
        $result = pm_ApiCli::callSbin('runner.php', ['-service_restart'], pm_ApiCli::RESULT_FULL);
        if(0 !== $result['code']){
            throw new pm_Exception($result['stderr']);
        }
        return $result;
    }

    /**
     * @return bool|Modules_Cloudradar_Status
     */
    public function isServiceRunning()
    {
        $process = new Process(['/usr/bin/cagent','--service_status']);
        $process->run();

        if(!$process->isSuccessful()){
            return new Modules_Cloudradar_Status(false,$process->getErrorOutput());
        }
        if(trim($process->getOutput()) == 'running'){
            return new Modules_Cloudradar_Status(true,$process->getOutput());
        }else{
            return new Modules_Cloudradar_Status(false,$process->getOutput());
        }
    }
    /**
     * @return bool|Modules_Cloudradar_Status
     */
    public function isRunning()
    {
        $status = $this->isServiceRunning();

        return $status->success();
    }

    /**
     * @return bool|Modules_Cloudradar_Status
     */
    public function isServiceConfigured()
    {
        $process = new Process(['/usr/bin/cagent','-t']);
        $process->run();

        if(!$process->isSuccessful()){
            return new Modules_Cloudradar_Status(false,$process->getErrorOutput());
        }

        return new Modules_Cloudradar_Status(true,$process->getOutput());
    }

    public function isConfigured()
    {
        $status = $this->isServiceConfigured();

        return $status->success();
    }
}