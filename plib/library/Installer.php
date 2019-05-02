<?php

use Symfony\Component\Process\Process;

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/
class Modules_Cagent_Installer
{
    protected $archType;
    protected $packageType;

    /**
     * Modules_Cagent_Installer constructor.
     */
    public function __construct()
    {
        if ('x86_64' == php_uname('m')) {
            $this->archType = 'amd64';
        } else {
            $this->archType = 'i386';
        }

        $process = new Process(['which', 'yum']);
        $process->run();
        if (!empty($process->getOutput())) {
            $this->packageType = 'rpm';
        }

        $process = new Process(['which', 'apt-get']);
        $process->run();
        if (!empty($process->getOutput())) {
            $this->packageType = 'deb';
        }
    }

    public function isInstalled()
    {
        if ('rpm' == $this->packageType) {
            $args = ['rpm', '-q', '--qf', '"%{VERSION}\n"', 'cagent'];
        } elseif ('deb' == $this->packageType) {
            $args = ['dpkg-query', '--showformat=\'${Version}\'', '--show','cagent'];
        } else {
            return new Modules_Cagent_Status(false, 'Package manager not found');
        }
        $process = new Process($args);
        $process->run();
        if (!$process->isSuccessful()) {
            return new Modules_Cagent_Status(false, $process->getErrorOutput());

        }

        return new Modules_Cagent_Status(true, $process->getOutput());
    }

    public function latest()
    {
        return Modules_Cagent_Util::getLatestRelease($this->archType, $this->packageType);
    }


    public function install($params)
    {
        if (!$downloadUrl = $this->latest()) {
            throw new Exception("Unable to get cagent download url");
        }
        if ('rpm' == $this->packageType) {
            $output = pm_ApiCli::callSbin('installer.php', [$this->packageType,$downloadUrl], pm_ApiCli::RESULT_FULL, [
                'CAGENT_HUB_URL'      => $params['url'],
                'CAGENT_HUB_USER'     => $params['user'],
                'CAGENT_HUB_PASSWORD' => $params['password']
            ]);

            if ($output['code'] != 0) {
                throw new Exception($output['stderr']);
            }

        } else {
            $temp_file = tempnam('', 'cagent.deb.');
            $process = new Process(['wget', '-O', $temp_file, $downloadUrl]);
            $process->run();
            if (!$process->isSuccessful()) {
                unlink($temp_file);
                throw new Exception($process->getErrorOutput());
            }
            $output = pm_ApiCli::callSbin('installer.php', [$this->packageType,$temp_file], pm_ApiCli::RESULT_FULL, [
                'CAGENT_HUB_URL'      => $params['url'],
                'CAGENT_HUB_USER'     => $params['user'],
                'CAGENT_HUB_PASSWORD' => $params['password']
            ]);

            if ($output['code'] != 0) {
                throw new Exception($output['stderr']);
            }
            unlink($temp_file);
            touch('/etc/init/cagent.conf');
        }

        return true;
    }

    public function isRunning()
    {

    }

    public function uninstall()
    {
        $result = pm_ApiCli::callSbin('installer', ['uninstall',$this->packageType], pm_ApiCli::RESULT_FULL);
        $result['output'] = $result['stdout'];
        return $result;
    }
}