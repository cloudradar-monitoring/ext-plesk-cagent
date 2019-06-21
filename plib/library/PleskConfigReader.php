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
    public function getData()
    {
        $xml = base64_decode(pm_ApiRpc::getService()->call('<server><get><key/></get></server>')->server->get->result->key->content);

        $dom = new DOMDocument("1.0", "utf-8");
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('plesk-unified', 'http://parallels.com/schemas/keys/products/ples/unified/multi');

        $domains = $xpath->evaluate("//plesk-unified:domains/text()");
        $subdomains = $xpath->evaluate("//plesk-unified:subdomains-support/text()");

        return [
            'plesk-unified-domains'            => $domains->length ? $domains->item(0)->nodeValue : "",
            'plesk-unified-subdomains-support' => $subdomains->length ? $subdomains->item(0)->nodeValue : ""
        ];
    }
}