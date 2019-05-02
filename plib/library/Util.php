<?php

/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_Util
{
    private $moduleCmd;

    public function __construct()
    {
        $this->Init();
    }

    public function Init()
    {
    }

    public static function get_request_var($tag)
    {
        if (isset($_REQUEST[$tag]))
            return trim($_REQUEST[$tag]);
        else
            return NULL;
    }

    public function Validate_InstallInput($input)
    {
        $errors = array();

        if ($input['hub_url'] == '') {
            $errors['hub_url'] = 'Missing Hub URL!';
        } elseif (!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $input['hub_url'])) {
            $errors['hub_url'] = 'Is not valid url';
        }

        if ($input['hub_user'] == '') {
            $errors['hub_user'] = 'Missing hub user';
        }

        if ($input['hub_password'] == '') {
            $errors['hub_password'] = 'Missing hub password';
        }

        return $errors;
    }

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
            new Modules_Cagent_CloudRadarCheckboxDecorator(['id' => 'agree'])
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
            'value'      => '',
            'required'   => true,
            'validators' => [
                ['NotEmpty', true]
            ]
        ]);
        $form->addElement('text', 'user', [
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
}