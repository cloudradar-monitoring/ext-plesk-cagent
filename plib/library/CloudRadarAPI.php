<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_CloudRadarAPI
{
    /**
     * @var \GuzzleHttp\Client\
     */
    protected $client;

    protected $api_url;
    protected $hub_url;
    protected $registration_url;

    /**
     * CloudRadarAPI constructor.
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'timeout' => 20
        ]);
        if (!$this->api_url = pm_Config::get('api_url')) {
            $this->api_url = 'https://api.cloudradar.io';
        }
        if (!$this->hub_url = pm_Config::get('hub_url')) {
            $this->hub_url = 'https://hub.cloudradar.io';
        }
        if (!$this->registration_url = pm_Config::get('registration_url')) {
            $this->registration_url = 'https://my.cloudradar.io';
        }
    }

    protected function getPartnerData()
    {
        $data = [];
        $data['plesk-release'] = sprintf('%s %s%s.', pm_ProductInfo::getVersion(), pm_ProductInfo::getOsName(), pm_ProductInfo::getOsVersion());

        return $data;
    }

    public function register($email, $password)
    {
        try {
            $response = $this->client->post($this->registration_url . '/engine/register/', [
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
        } catch (\GuzzleHttp\Exception\ServerException $exception) {
            return $exception->getResponse()->getBody()->getContents();
        }
    }

    public function createHost($name, $connect, $token)
    {
        try {
            $response = $this->client->post($this->api_url . '/v1/hosts/', [
                'json'    => [
                    'name'        => $name,
                    'connect'     => $connect,
                    'description' => '',
                    'tags'        => [],
                    'cagent'      => true,
                    'dashboard'   => true
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            return $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getResponse()->getBody()->getContents();
        }
    }

    public function removeHost($hostUuid, $token)
    {

        try {
            $response = $this->client->delete(sprintf($this->api_url . '/v1/hosts/%s', $hostUuid),
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token
                    ]
                ]);

            return $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getResponse()->getBody()->getContents();
        }
    }
}