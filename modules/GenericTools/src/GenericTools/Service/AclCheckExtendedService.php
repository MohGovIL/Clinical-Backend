<?php


namespace GenericTools\Service;

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
        $result = acl_check($section, $value, $user, $return_value);
        return $result;
    }

    public function getAuthUser()
    {
        $authUser=null;
        // check for localCall and notRestCall
        if(!empty($_SERVER['HTTP_APICSRFTOKEN'])){   //todo:: check for notRestCall
            $authUser= $_SESSION['authUser'];
        }else{

            $tokenRaw=$_SERVER["HTTP_X_API_TOKEN"];
            $token = $this->decryptValidateToken($tokenRaw);
            if (!empty($token)) {
                // Only use first part of token since authentication of token not needed here
                $token = substr($token, 0, 32);

                // Collect username
                $sql = " SELECT";
                $sql .= " u.username";
                $sql .= " FROM api_token a";
                $sql .= " JOIN users_secure u ON u.id = a.user_id";
                $sql .= " WHERE BINARY a.token = ?";
                $userResult = sqlQueryNoLog($sql, array($token));
                if (!empty($userResult["username"])) {
                    $authUser=$userResult["username"];
                }
            }
        }

        return $authUser;
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
