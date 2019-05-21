<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_Extension extends pm_Extension {
    public function __construct() {
    }

    public function getName() {
        return 'cloudradar';
    }

    public function enable() {
        $installer = new Modules_Cloudradar_Installer();
        $result = $installer->isInstalled();
        if (!$result->success()) {
            throw new Exception('cagent platform package not yet installed');
        }
        $result = pm_ApiCli::callSbin('runner.php', ['-s cagent'], pm_ApiCli::RESULT_FULL);

        return $result;
    }

    public function disable() {
        $installer = new Modules_Cloudradar_Installer();
        $result = $installer->isInstalled();

        if (!$result->success()) {
            throw new Exception('cagent platform package not yet installed');
        }

        $result = pm_ApiCli::callSbin('runner.php', ['-u'], pm_ApiCli::RESULT_FULL);

        return $result;
    }
}

return New Modules_Cloudradar_Extension();