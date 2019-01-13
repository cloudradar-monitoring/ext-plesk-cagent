<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class IndexController extends pm_Controller_Action {
    private $util;
    private $service;

    public  $view;

    public function init() {
        parent::init();

        define('MODULE_VERSION', '1.2.5');
        define('CLOUDRADAR_URL', 'https://cloudradar.io');
        pm_Settings::set('CLOUDRADAR_URL', CLOUDRADAR_URL);

        $this->view->dispatcher = new Modules_Cagent_View($this->view);
        $this->util = new Modules_Cagent_Util();
        $this->service = new Modules_Cagent_Service();
        $this->util->Init();
    }

    public function indexAction() {
        $support_actions = array('define_home', 'configure', 'status', 'start', 'stop', 'restart');

        $do = $this->util->get_request_var('do');
        $step = $this->util->get_request_var('step');
        $steps = array(0, 1, 2);

//        if (!in_array($do, $support_actions) || !in_array($step, $steps)) {
//            echo '<h1>Invalid Entrance</h1>';
//            return;
//        }

        $this->view->dispatcher->PageHeader($do);

        switch ($do) {
            default:
                // Show the main page
                $this->main_menu();
        }

        $this->view->dispatcher->PageFooter();
    }

    private function main_menu()
    {
        $info['cagent_running']    = $this->service->isRunning();
        $info['cagent_configured'] = $this->service->isConfigured();

        $this->view->dispatcher->MainMenu($info);
    }
}