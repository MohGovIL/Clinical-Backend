<?php


namespace FhirAPI\Service;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;

class FhirValidateTypes
{
    const BOOLEAN = "boolean";
    const INTEGER = "integer";
    const STRING = "string";
    const DECIMAL = "decimal";
    const URI = "uri";
    const URL = "url";
    const BASE64BINARY = "base64Binary";
    const INSTANT = "instant";
    const DATE = "date";
    const DATETIME = "dateTime";
    const TIME = "time";
    const CODE = "code";
    const OID = "oid";
    const ID = "id";
    const MARKDOWN = "markdown";
    const UNSIGNED_INT = "unsignedInt";
    const POSITIVE_INT = "positiveInt";
    const UUID = "uuid";
    const DAYS_OF_WEEK = "DaysOfWeek";


    const URI_RFC3986_V1 = "/[A-Za-z][A-Za-z0-9+.-]*:(\/\/(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|:)*@)?([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=])*(:[0-9]*)?(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*|\/(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])+(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*)?|([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])+(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*|())(\?(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])|[\/?])*)?(#(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])|[\/?])*)?|(\/\/(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|:)*@)?([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=])*(:[0-9]*)?(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*|\/(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])+(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*)?|([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|@)+(\/([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])*)*|())(\?(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])|[\/?])*)?(#(([A-Za-z0-9._~-]|%[0-9A-Fa-f]{2}|[!$&'()*+,;=]|[:@])|[\/?])*)?/";
    const ALLOW_NULL_BOOLEAN = "ALLOW_NULL_BOOLEAN";
    const NO_NULL_BOOLEAN = "NO_NULL_BOOLEAN";
    const ALLOW_NULL_ERROR = "ALLOW_NULL_ERROR";
    const NO_NULL_ERROR = "NO_NULL_ERROR";
    const FAIL_STRING = "FHIR validation fail";


    private static $types = [
        self::BOOLEAN => "/true|false/",
        self::INTEGER => "/[0]|[-+]?[1-9][0-9]*/",
        self::STRING => "/[\r\n\t\S]+/",
        self::URI => self::URI_RFC3986_V1,
        self::URL => self::URI_RFC3986_V1,
        self::DECIMAL => "/-?(0|[1-9][0-9]*)(\.[0-9]+)?([eE][+-]?[0-9]+)?/",
        self::BASE64BINARY => "/(\s*([0-9a-zA-Z\+\=]){4}\s*)+/",
        self::INSTANT => "/([0-9]([0-9]([0-9][1-9]|[1-9]0)|[1-9]00)|[1-9]000)-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T([01][0-9]|2[0-3]):[0-5][0-9]:([0-5][0-9]|60)(\.[0-9]+)?(Z|(\+|-)((0[0-9]|1[0-3]):[0-5][0-9]|14:00))/",
        self::DATE => "/([0-9]([0-9]([0-9][1-9]|[1-9]0)|[1-9]00)|[1-9]000)(-(0[1-9]|1[0-2])(-(0[1-9]|[1-2][0-9]|3[0-1]))?)?/",
        self::DATETIME => "/([0-9]([0-9]([0-9][1-9]|[1-9]0)|[1-9]00)|[1-9]000)(-(0[1-9]|1[0-2])(-(0[1-9]|[1-2][0-9]|3[0-1])(T([01][0-9]|2[0-3]):[0-5][0-9]:([0-5][0-9]|60)(\.[0-9]+)?(Z|(\+|-)((0[0-9]|1[0-3]):[0-5][0-9]|14:00)))?)?)?/",
        self::TIME => "/([01][0-9]|2[0-3]):[0-5][0-9]:([0-5][0-9]|60)(\.[0-9]+)?/",
        self::CODE => "/[^\s]+(\s[^\s]+)*/",
        self::OID => "/urn:oid:[0-2](\.(0|[1-9][0-9]*))+/",
        self::ID => "/[A-Za-z0-9\-\.]{1,64}/",
        self::MARKDOWN => "/\s*(\S|\s)*/",
        self::UNSIGNED_INT => "/[0]|([1-9][0-9]*)/",
        self::POSITIVE_INT => "/+?[1-9][0-9]*/",
        self::UUID => self::URI_RFC3986_V1,
        self::DAYS_OF_WEEK => "mon|tue|wed|thu|fri|sat|sun",


    ];

    /**
     * validate a value
     *
     * @param string
     * value to check
     * @param string
     * value type
     * @param string
     * behavioral flag
     * ALLOW_NULL_BOOLEAN - return boolean allow null value
     * NO_NULL_BOOLEAN  - return boolean does not allow null value
     * ALLOW_NULL_ERROR  - return true or exit allow null value
     * NO_NULL_ERROR - return true or exit does not allow null value
     *
     * The function return false or error on empty string
     *
     * @return boolean | ErrorCodes
     */

    static public function checkByPreg($string, $type, $flag = self::ALLOW_NULL_ERROR)
    {


        if(empty($GLOBALS['fhir_type_validation']) || !($GLOBALS['fhir_type_validation'])){
            return true;
        }


        $typeExistFlag = array_key_exists($type, self::$types);

        switch ($flag) {

            case self::ALLOW_NULL_ERROR:
                if (!$typeExistFlag || ((strlen($string) <= 0) && $string !== null) ) {
                    ErrorCodes::http_response_code('406', self::FAIL_STRING . ':' . $type);
                }

                break;
            case self::NO_NULL_ERROR:
                if (!$typeExistFlag || (strlen($string) <= 0)) {
                    ErrorCodes::http_response_code('406', self::FAIL_STRING . ':' . $type);
                }
                break;

            case self::ALLOW_NULL_BOOLEAN:
                if (!$typeExistFlag || ((strlen($string) <= 0) && $string !== null) ) {
                    return false;
                }
                break;

            case self::NO_NULL_BOOLEAN :
            default:
                if (!$typeExistFlag || (strlen($string) <= 0)) {
                    return false;
                }
        }

        if($string===null){
            return true;
        } else{
            preg_match_all(self::$types[$type], $string, $matches);
            if ($matches[0][0] === $string){
                return true;
            }else{
                ErrorCodes::http_response_code('406', self::FAIL_STRING . ':' . $type.' does not match fhir base validation');
            }

        }
    }

}
