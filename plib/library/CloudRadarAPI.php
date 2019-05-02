<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 01.04.2019
 * Time: 17:46
 */

class Modules_Cagent_CloudRadarAPI
{
    /**
     * @var \GuzzleHttp\Client\
     */
    protected $client;

    /**
     * CloudRadarAPI constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'https://my.cloudradar.io/engine/',
            'timeout'  => 20
        ]);
    }

    protected function getPartnerData()
    {
        $data = [];
        $license = new pm_License();
        $data['key-number'] = $license->getProperty('plesk_key_id');
        $data['plesk-release'] = trim(file_get_contents('/etc/plesk-release'));
        $configReader = new Modules_Cagent_PleskConfigReader();
        if ($config = $configReader->getData()) {
            $data += $config;
        }
        //cat /etc/plesk-release contains 17.9.12 1709190308.17
        return $data;
    }

    public function register($email, $password)
    {
        try {
            $response = $this->client->post('register/', [
                'json' => [
                    'email'            => $email,
                    'password'         => $password,
                    'termsAccepted'    => true,
                    'privacyAccepted'  => true,
                    'partner'          => 'plesk',
                    'partnerExtraData' => $this->getPartnerData()
                ]
            ]);

            return $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getResponse()->getBody()->getContents();
        }
    }
}