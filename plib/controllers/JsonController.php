<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 01.04.2019
 * Time: 13:23
 */

class JsonController extends pm_Controller_Action
{

    public function registerAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = Modules_Cagent_Util::getRegistrationForm();
            if ($form->isValid($this->getRequest()->getPost())) {
                $cloudRadar = new Modules_Cagent_CloudRadarAPI();

                $response = $cloudRadar->register($this->getRequest()->getPost('email'), $this->getRequest()->getPost('password'));

                $responseData = json_decode($response, true);
                if ($responseData['success']) {
                    //@TODO store in file?
                    pm_Settings::set('userUuid', $responseData['newUser']);
                    pm_Settings::set('userEmail', $responseData['email']);

                    $this->_helper->json([
                        'success' => true,
                        'data'    => $responseData
                    ]);
                } else {
                    if (isset($responseData['errors'])) {
                        $errors = [];
                        foreach($responseData['errors'] as $error){
                            $errors[str_replace("/","",$error['field'])] = $error['message'];
                        }

                    }elseif(isset($responseData['error'])){
                        $errors = ['email' => $responseData['error']];
                    }
                    $this->_helper->json([
                        'success' => false,
                        'errors'   => $errors,
                        'response' => $responseData
                    ]);
                }
            } else {
                $errors = [];
                foreach($form->getMessages() as $field => $error){
                    $errors[$field] = join(PHP_EOL,$error);
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
            $form = Modules_Cagent_Util::getHubSettingsForm();
            if ($form->isValid($this->getRequest()->getPost())) {
                $installer = new Modules_Cagent_Installer();
                try {
                    $installer->install($this->getRequest()->getPost());
                    $this->_helper->json([
                        'success' => true,
                        'message' => 'Cagent successfully installed'
                    ]);
                }catch(\Exception $e){
                    $this->_helper->json([
                        'success' => false,
                        'errors'  => ['installation'=>$e->getMessage()]
                    ]);
                }
            } else {
                $errors = [];
                foreach($form->getMessages() as $field => $error){
                    $errors[$field] = join(PHP_EOL,$error);
                }
                $this->_helper->json([
                    'success' => false,
                    'errors'  => $errors
                ]);
            }
        }
    }
}