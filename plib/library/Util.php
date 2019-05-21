<?php

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_Util
{

    /**
     * @return pm_Form_Simple
     */
    public static function getRegistrationForm()
    {
        $form = new pm_Form_Simple();
        $form->addElement('text', 'email', [
            'label'      => 'E-mail',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['EmailAddress', true]
            ]
        ]);
        $form->addElement('password', 'password', [
            'label'      => 'Password',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['StringLength', true, [6, 30]]
            ]
        ]);
        $form->addElement('password', 'password2', [
            'label'      => 'Repeat Password',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['StringLength', true, [6, 30]],
                ['Identical', false, ['token' => 'password']]
            ]
        ]);
        //keep this order in decorator array to make label placement work
        $decorators = [
            'ViewHelper',
            ['label', [
                'escape'    => false,
                'placement' => 'append'
            ]],
            new Modules_Cloudradar_CloudRadarCheckboxDecorator(['id' => 'agree'])
        ];
        $form->addElement('checkbox', 'agree', [
            'label'          => 'I agree to the <a target="_blank" href="https://cloudradar.io/service-terms">Terms & Conditions</a> and the <a href="https://cloudradar.io/privacy-policy" target="_blank">Privacy Policy</a>',
            'required'       => true,
            'uncheckedValue' => null,
            'decorators'     => $decorators,
            'validators'     => [
                ['NotEmpty', true, ['messages' => 'You need to agree to Terms & Conditions and Privacy Policy']]
            ]
        ]);

        return $form;
    }

    public static function getHubSettingsForm()
    {
        $form = new pm_Form_Simple();
        $form->addElement('text', 'url', [
            'label'      => 'Hub URL',
            'value'      => pm_Config::get('hub_url'),
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);
        $form->addElement('text', 'hub_user', [
            'label'      => 'Hub User',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);
        $form->addElement('text', 'password', [
            'label'      => 'Hub Password',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);

        return $form;
    }

    public static function getHostRegisterForm()
    {
        $form = new pm_Form_Simple();
        $form->addElement('text', 'token', [
            'label'      => 'Token',
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);
        $form->addElement('text', 'hostname', [
            'label'      => 'Hostname',
            'value'      => self::getHostname(),
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);
        $form->addElement('text', 'ip', [
            'label'      => 'IP Address/FQDN',
            'value'      => self::getIp(),
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);

        return $form;
    }

    public static function getLatestRelease($archType = '386', $packageType = 'rpm')
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $response = file_get_contents('https://api.github.com/repos/cloudradar-monitoring/cagent/releases/latest', false,$ctx);
        if (false === $response) {
            return false;
        }

        $json = json_decode($response, true);

        if (false === $json) {
            return false;
        }
        $tag_name = $json['tag_name'];

        $url = sprintf('https://github.com/cloudradar-monitoring/cagent/releases/download/%s/cagent_%s_linux_%s.%s', $tag_name, $tag_name, $archType, $packageType);

        return $url;
    }

    public static function getIp()
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header'  => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $response = file_get_contents('https://api.ipify.org?format=json', false, $ctx);
        if (false === $response) {
            return '';
        }

        $json = json_decode($response, true);

        if (false === $json) {
            return '';

        }
        if(!isset($json['ip'])){
            return '';
        }
        return $json['ip'];
    }

    public static function getHostname(){
        return gethostname();
        $process = new Process(['/usr/bin/cagent','--service_status']);
        $process->run();

        if(!$process->isSuccessful()){
            return new Modules_Cloudradar_Status(false,$process->getErrorOutput());
        }
    }
}