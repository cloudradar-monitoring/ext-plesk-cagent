<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

$moduleId = basename(dirname(__FILE__));

$moduleId = 'cloudradar';
pm_Context::init($moduleId);
$app = new pm_Application();
$app->Run();