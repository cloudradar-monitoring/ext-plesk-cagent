<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

$hostUuid = pm_Settings::get('hostUuid');
$token = pm_Settings::get('token');
pm_Settings::clean();
$api = new Modules_Cloudradar_CloudRadarAPI();
try{
    if($hostUuid) {
        $api->removeHost($hostUuid, $token);
    }
}catch(\Exception $e){
    //do nothing, we are uninstalling
    //token can be revoked, for example
}
$installer = new Modules_Cloudradar_Installer();
$result = $installer->uninstall();
if ($result['code'] !== 0) {
    Modules_Cloudradar_Util::log('Uninstall:'.print_r($result,true));
    throw new pm_Exception ('Error occurred when unstalling cagent package.');
}