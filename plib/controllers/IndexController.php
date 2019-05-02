<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class IndexController extends pm_Controller_Action {
    private $util;
    /**
     * @var Modules_Cagent_Service
     */
    private $service;
    /**
     * @var Modules_Cagent_Installer
     */
    private $installer;

    public  $view;

    public function init() {
        parent::init();

        define('MODULE_VERSION', '1.2.5');
        define('CLOUDRADAR_URL', 'cloudradar.io');
        pm_Settings::set('CLOUDRADAR_URL', CLOUDRADAR_URL);

        $this->view->dispatcher = new Modules_Cagent_View($this->view);
        $this->util = new Modules_Cagent_Util();
        $this->service = new Modules_Cagent_Service();
        $this->installer = new Modules_Cagent_Installer();
        $this->util->Init();
    }

    public function indexAction() {

        $this->view->form = Modules_Cagent_Util::getRegistrationForm();
        $this->view->hubForm = Modules_Cagent_Util::getHubSettingsForm();

        $this->view->userUuid = pm_Settings::get('userUuid',false);
        $this->view->userEmail = pm_Settings::get('userEmail',false);

        $this->view->cagentInstalled = $this->installer->isInstalled();
        $this->view->cagentRunning = $this->installer->isInstalled();
        $this->view->cagentConfigured = $this->service->isConfigured();
        $this->view->cagentRunning = $this->service->isRunning();
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

    public function jsonAction(){
        $this->_helper->json([
            'success' => true
        ]);
    }
}