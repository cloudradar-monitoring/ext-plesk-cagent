<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class IndexController extends pm_Controller_Action {
    private $util;
    /**
     * @var Modules_Cloudradar_Service
     */
    private $service;
    /**
     * @var Modules_Cloudradar_Installer
     */
    private $installer;

    public  $view;

    public function init() {
        parent::init();

        $this->util = new Modules_Cloudradar_Util();
        $this->service = new Modules_Cloudradar_Service();
        $this->installer = new Modules_Cloudradar_Installer();
    }

    public function indexAction() {

        $this->view->form = Modules_Cloudradar_Util::getRegistrationForm();
        $this->view->hubForm = Modules_Cloudradar_Util::getHubSettingsForm();
        $this->view->hostRegisterForm = Modules_Cloudradar_Util::getHostRegisterForm();


        $this->view->userUuid = pm_Settings::get('userUuid',false);
        $this->view->userEmail = pm_Settings::get('userEmail',false);

        $this->view->cagentInstalled = $this->installer->isInstalled();
        $this->view->cagentRunning = $this->installer->isInstalled();
        $this->view->cagentConfigured = $this->service->isServiceConfigured();
        $this->view->cagentRunning = $this->service->isServiceRunning();

        if (!$this->view->registration_url = pm_Config::get('registration_url')) {
            $this->view->registration_url = 'https://my.cloudradar.io';
        }
        $this->view->version = '0.0.2-beta';
    }
}