<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class JsonController extends pm_Controller_Action
{

    public function registerAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = Modules_Cloudradar_Util::getRegistrationForm();
            if ($form->isValid($this->getRequest()->getPost())) {
                $cloudRadar = new Modules_Cloudradar_CloudRadarAPI();

                $response = $cloudRadar->register($this->getRequest()->getPost('email'), $this->getRequest()->getPost('password'));

                $responseData = json_decode($response, true);
                if ($responseData['success']) {

                    pm_Settings::set('userUuid', $responseData['newUser']);
                    pm_Settings::set('userEmail', $responseData['email']);

                    $this->_helper->json([
                        'success' => true,
                        'data'    => $responseData
                    ]);
                } else {
                    $errors = [];
                    if (isset($responseData['errors'])) {
                        foreach ($responseData['errors'] as $error) {
                            if ('/password' == $error['field'] && strpos($error['message'], '[a-z]')) {
                                $error['message'] = 'Please use uppercase, lowercase letters, and digits.';
                            }
                            $errors[str_replace("/", "", $error['field'])] = $error['message'];
                        }

                    } elseif (isset($responseData['error'])) {
                        $errors = ['email' => $responseData['error']];
                    }
                    $this->_helper->json([
                        'success' => false,
                        'errors'  => $errors
                    ]);
                }
            } else {
                $errors = [];
                foreach ($form->getMessages() as $field => $error) {
                    $errors[$field] = join(PHP_EOL, $error);
                }
                $this->_helper->json([
                    'success' => false,
                    'errors'  => $errors
                ]);
            }
        }
    }

    public function installAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = Modules_Cloudradar_Util::getHubSettingsForm();
            if ($form->isValid($this->getRequest()->getPost())) {
                $installer = new Modules_Cloudradar_Installer();
                try {
                    $installer->install($this->getRequest()->getPost());
                    $this->_helper->json([
                        'success' => true,
                        'message' => 'Cagent successfully installed'
                    ]);
                } catch (\Exception $e) {
                    $this->_helper->json([
                        'success' => false,
                        'errors'  => ['installation' => $e->getMessage()]
                    ]);
                }
            } else {
                $errors = [];
                foreach ($form->getMessages() as $field => $error) {
                    $errors[$field] = join(PHP_EOL, $error);
                }
                $this->_helper->json([
                    'success' => false,
                    'errors'  => $errors
                ]);
            }
        }
    }

    public function registerHostAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = Modules_Cloudradar_Util::getHostRegisterForm();
            if ($form->isValid($this->getRequest()->getPost())) {
                $cloudRadar = new Modules_Cloudradar_CloudRadarAPI();
                try {
                    $response = $cloudRadar->createHost($this->getRequest()->getPost('hostname'),
                        $this->getRequest()->getPost('ip'),
                        $this->getRequest()->getPost('token'));
                    $json = json_decode($response, true);
                    if (!$json['success']) {
                        $details = "";
                        if (!empty($json['details'])) {
                            if (is_array($json['details'])) {
                                $details = ":<br>" . join("<br>", $json['details']);
                            } else {
                                $details = ":<br>" . $json['details'];
                            }
                        }
                        throw new Exception($json['error'] . $details);
                    }
                    pm_Settings::set('hostUuid', $json['host']['uuid']);
                    pm_Settings::set('token', $this->getRequest()->getPost('token'));
                    $installer = new Modules_Cloudradar_Installer();
                    $installer->install([
                        'url'      => $json['host']['hub_url'],
                        'hub_user' => $json['host']['uuid'],
                        'password' => $json['host']['hub_password']
                    ]);
                    $this->_helper->json([
                        'success' => true,
                        'message' => 'Host created. Monitoring enabled.'
                    ]);
                } catch (\Exception $e) {
                    $this->_helper->json([
                        'success' => false,
                        'errors'  => ['host-register' => $e->getMessage()]
                    ]);
                }
            } else {
                $errors = [];
                foreach ($form->getMessages() as $field => $error) {
                    $errors[$field] = join(PHP_EOL, $error);
                }
                $this->_helper->json([
                    'success' => false,
                    'errors'  => $errors
                ]);
            }
        }
    }
}