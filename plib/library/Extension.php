<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_Extension extends pm_Extension {
    public function __construct() {
    }

    public function getName() {
        return 'cagent';
    }

    public function enable() {
        $result = Modules_Cagent_Installer::isInstalled();
        if ($result['code'] !== 0) {
            throw new Exception('cagent platform package not yet installed');
        }
        $result = pm_ApiCli::callSbin('runner.php', ['-s cagent'], pm_ApiCli::RESULT_FULL);

        return $result;
    }

    public function disable() {
        $result = Modules_Cagent_Installer::isInstalled();
        if ($result['code'] !== 0) {
            throw new Exception('cagent platform package not yet installed');
        }

        $result = pm_ApiCli::callSbin('runner.php', ['-u'], pm_ApiCli::RESULT_FULL);

        return $result;
    }
}

return New Modules_Cagent_Extension();