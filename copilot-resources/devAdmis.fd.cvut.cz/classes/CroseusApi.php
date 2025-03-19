<?php

class CroseusApi extends SoapClient
{

    public function __construct($wsdl, array $options = null, $username, $password)
    {
        parent::__construct($wsdl, $options);
        $strXML = <<<XML
                <Security xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <UsernameToken>
                        <Username>$username</Username>
                        <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">$password</Password>
                    </UsernameToken>
                </Security>
XML;
        $objAuthVar = new \SoapVar($strXML, XSD_ANYXML);
        $objAuthHeader = new \SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", 'Security', $objAuthVar, false);
        $this->__setSoapHeaders(array($objAuthHeader));
    }

}