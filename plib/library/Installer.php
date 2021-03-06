<?php

use Symfony\Component\Process\Process;

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_Installer
{
    protected $archType;
    protected $packageType;

    /**
     * Modules_Cagent_Installer constructor.
     */
    public function __construct()
    {
        if ('x86_64' == pm_ProductInfo::getOsArch()) {
            $this->archType = 'amd64';
        } else {
            $this->archType = 'i386';
        }

        $this->packageType = pm_ProductInfo::getOsPackageType();
    }

    public function isInstalled()
    {
        if ('rpm' == $this->packageType) {
            $args = ['rpm', '-q', '--qf', '"%{VERSION}\n"', 'cagent'];
        } elseif ('deb' == $this->packageType) {
            $args = ['dpkg-query', '--showformat=\'${Version}\'', '--show','cagent'];
        } else {
            return new Modules_Cloudradar_Status(false, 'Package manager not found');
        }
        $process = new Process($args);
        $process->run();
        if (!$process->isSuccessful()) {
            return new Modules_Cloudradar_Status(false, $process->getErrorOutput());

        }

        return new Modules_Cloudradar_Status(true, $process->getOutput());
    }

    public function latest()
    {
        if('deb' == $this->packageType){
            return 'https://repo.cloudradar.io/pool/utils/c/cloudradar-release/cloudradar-release.deb';
        }

        return Modules_Cloudradar_Util::getLatestRelease($this->archType, $this->packageType);
    }


    /**
     * @param $params
     * @return mixed
     * @throws pm_Exception
     */
    public function install($params)
    {
        if (!$downloadUrl = $this->latest()) {
            throw new Exception("Unable to get cagent download url");
        }
        $filename = basename($downloadUrl);
        $fileManager = new pm_ServerFileManager();

        if ('rpm' == $this->packageType) {
            $temp_file = pm_Context::getVarDir().DIRECTORY_SEPARATOR. $filename;
            $process = new Process(['wget','-O',$temp_file, $downloadUrl]);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new Exception($process->getErrorOutput());
            }
            $output = pm_ApiCli::callSbin('installer', [$this->packageType,$temp_file], pm_ApiCli::RESULT_FULL, [
                'CAGENT_HUB_URL'      => $params['url'],
                'CAGENT_HUB_USER'     => $params['hub_user'],
                'CAGENT_HUB_PASSWORD' => $params['hub_password']
            ]);
            if($fileManager->fileExists($temp_file)){
                $fileManager->removeFile($temp_file);
            }
            if ($output['code'] != 0) {
                Modules_Cloudradar_Util::log('Output:'.print_r($output,true));
                throw new Exception($output['stderr']);
            }

        } else {
            $temp_file = pm_Context::getVarDir().DIRECTORY_SEPARATOR. $filename;
            $process = new Process(['wget','-O',$temp_file, $downloadUrl]);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new Exception($process->getErrorOutput());
            }
            $output = pm_ApiCli::callSbin('installer', [$this->packageType,$temp_file], pm_ApiCli::RESULT_FULL, [
                'CAGENT_HUB_URL'      => $params['url'],
                'CAGENT_HUB_USER'     => $params['hub_user'],
                'CAGENT_HUB_PASSWORD' => $params['hub_password']
            ]);

            if($fileManager->fileExists($temp_file)){
                $fileManager->removeFile($temp_file);
            }
            if ($output['code'] != 0) {
                Modules_Cloudradar_Util::log('Output:'.print_r($output,true));
                throw new Exception($output['stderr']);
            }
        }

        return $output['stdout'];
    }

    public function configure($params){
        $output = pm_ApiCli::callSbin('config', [str_replace("/","\/",$params['url']),$params['hub_user'], $params['hub_password']], pm_ApiCli::RESULT_FULL);

        if ($output['code'] != 0) {
            Modules_Cloudradar_Util::log($output['stderr']);
            throw new Exception($output['stderr']);
        }

        Modules_Cloudradar_Util::log('Output:'.print_r($output,true));

        return $output['stdout'];
    }

    public function uninstall()
    {
        $result = pm_ApiCli::callSbin('uninstaller', [$this->packageType], pm_ApiCli::RESULT_FULL);
        $result['output'] = $result['stdout'];
        return $result;
    }
}