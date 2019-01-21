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
    private $installer;

    public  $view;

    public function init() {
        parent::init();

        define('MODULE_VERSION', '1.2.5');
        define('CLOUDRADAR_URL', 'https://cloudradar.io');
        pm_Settings::set('CLOUDRADAR_URL', CLOUDRADAR_URL);

        $this->view->dispatcher = new Modules_Cagent_View($this->view);
        $this->util = new Modules_Cagent_Util();
        $this->service = new Modules_Cagent_Service();
        $this->installer = new Modules_Cagent_Installer();
        $this->util->Init();
    }

    public function indexAction() {
        $support_actions = array('install', 'uninstall', 'configure', 'status', 'start', 'stop', 'restart', '');

        $do = $this->util->get_request_var('do');
        $step = $this->util->get_request_var('step');
        $steps = array(0, 1, 2);

        if (!in_array($do, $support_actions) || !in_array($step, $steps)) {
            echo '<h1>Invalid Entrance</h1>';
            return;
        }

        $this->view->dispatcher->PageHeader('');

        switch ($do) {
            case 'install':
                $this->install_cagent($step);
                break;
            case 'uninstall':
                $this->uninstall_cagent();
                break;
            default:
                // Show the main page
                $this->main_menu();
        }

        $this->view->dispatcher->PageFooter();
    }

    private function main_menu()
    {
        $info['cagent_installed']  = $this->installer->isInstalled();
        $info['cagent_latest']     = $this->installer->latest();
        $info['cagent_configured'] = $this->service->isConfigured();
        $info['cagent_running']    = $this->service->isRunning();

        $this->view->dispatcher->MainMenu($info);
    }

    private function install_cagent(&$step) {
        if ($step == 0) {
            $info['hub_url'] = '';
        } else {
            $info['hub_url'] = $this->util->get_request_var('hub_url');
            $info['hub_user'] = $this->util->get_request_var('hub_user');
            $info['hub_password'] = $this->util->get_request_var('hub_password');
            $info['version'] = $this->installer->latest()['version'];
            $info['error'] = $this->util->Validate_InstallInput($info);

            if ($info['error'] == NULL) {
                $res = $this->installer->install($info);
                $info['return'] = $res['code'];
                $info['output'] = $res['output'];
            }
        }

        if ($step == 0 || $info['error'] != NULL) {
            $this->view->dispatcher->InstallPrepare($info);
        } else {
            $info['cagent_running'] = $this->service->isRunning();
            $this->view->dispatcher->Install($info);
        }
    }

    private function uninstall_cagent() {

    }
}