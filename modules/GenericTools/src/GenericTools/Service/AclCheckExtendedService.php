<?php


namespace GenericTools\Service;

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Crypto\CryptoGen;

class AclCheckExtendedService {

    private $container;

    public function __construct($container)
    {
        $this->container=$container;
    }

    public function authorizationCheck($section, $value, $user = '', $return_value = '')
    {
        if ($user === '' || $user === null || $user === false) {
            $user = $this->getAuthUser();
        }
        $result = AclMain::aclCheckCore($section, $value, $user, $return_value);
        return $result;
    }

    public function getAuthUser()
    {
        return $_SESSION['authUser'];
    }


    private function decryptValidateToken($token)
    {
        // decrypt/validate the encrypted/signed token (also ensure the decrypted token is 64 characters)
        if (empty($token)) {
            return false;
        }
        $cryptoGen = new CryptoGen();
        if (!$cryptoGen->cryptCheckStandard($token)) {
            return false;
        }
        $decrypt_token = $cryptoGen->decryptStandard($token, null, 'drive', 6);
        if (empty($decrypt_token)) {
            return false;
        }
        if (strlen($decrypt_token) != 64) {
            return false;
        }
        return $decrypt_token;
    }


    private function get_bearer_token()
    {
        $parse = preg_split("/[\s,]+/", $_SERVER["HTTP_AUTHORIZATION"]);
        if (strtoupper(trim($parse[0])) !== 'BEARER') {
            return false;
        }

        return trim($parse[1]);
    }


    public function getSiteId()
    {
        $siteId=null;

        $token =$this->get_bearer_token();
        $token_decoded = base64_decode($token, true);

        if (!empty($token_decoded)) {
            $tokenParts = json_decode($token_decoded, true);
        } else {
            $tokenParts = array();
        }

        if( !empty($tokenParts['site_id']) ){
            $siteId=$tokenParts['site_id'];
        }

        return $siteId;
    }

}
