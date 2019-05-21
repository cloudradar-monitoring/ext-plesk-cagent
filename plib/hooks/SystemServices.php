<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_CloudRadar_SystemServices extends pm_Hook_SystemServices
{
    public function getServices()
    {
        return [new Modules_Cloudradar_Service()];
    }
}