<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_PleskConfigReader
{

    protected function findConfig(){
        $files = glob("/root/PLSK.*.xml");
        if(isset($files[0])){
            return $files[0];
        }
         return false;
    }

    public function getData(){
        if(!$file = $this->findConfig()){
            return false;
        }

        $dom = new DOMDocument("1.0","utf-8");
        $dom->load($file);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('plesk-unified','http://parallels.com/schemas/keys/products/ples/unified/multi');

        $domains = $xpath->evaluate("//plesk-unified:domains/text()");
        $subdomains = $xpath->evaluate("//plesk-unified:subdomains-support/text()");

        return [
            'plesk-unified-domains' => $domains,
            'plesk-unified-subdomains-support' => $subdomains
        ];
    }
}