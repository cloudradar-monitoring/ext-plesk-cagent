<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_Extension extends pm_Extension
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'cloudradar';
    }

    public function enable()
    {
        $installer = new Modules_Cloudradar_Installer();
        $result = $installer->isInstalled();
        if (!$result->success()) {
            throw new Exception('cagent platform package not yet installed');
        }

        $result = pm_ApiCli::callSbin('runner.php', ['-service_start'], pm_ApiCli::RESULT_FULL);
        if (0 !== $result['code']) {
            Modules_Cloudradar_Util::log($result['stderr']);
            throw new pm_Exception($result['stderr']);
        }
        return $result;
    }

    public function disable()
    {
        $installer = new Modules_Cloudradar_Installer();
        $result = $installer->isInstalled();

        if (!$result->success()) {
            throw new Exception('cagent platform package not yet installed');
        }

        $result = pm_ApiCli::callSbin('runner.php', ['-service_stop'], pm_ApiCli::RESULT_FULL);
        if (0 !== $result['code']) {
            Modules_Cloudradar_Util::log($result['stderr']);
            throw new pm_Exception($result['stderr']);
        }
        return $result;
    }
}

return New Modules_Cloudradar_Extension();