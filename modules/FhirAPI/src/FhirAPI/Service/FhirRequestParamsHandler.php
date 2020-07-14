<?php
/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * This class creates acl filtered api calls array
 */

namespace FhirAPI\Service;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use Interop\Container\ContainerInterface;

class FhirRequestParamsHandler
{
    private $container=null;
    private $requestParams=null;

    const PARAMETERS_FOR_ALL_RESOURCES=array('_id',
                                             '_lastUpdated',
                                             '_tag',
                                             '_profile',
                                             '_security',
                                             '_text',
                                             '_content',
                                             '_list',
                                             '_has_type'
                                            );

    const PARAMETERS_FOR_SEARCH_RESULT =array('_sort',
                                              '_count',
                                              '_include',
                                              '_revinclude',
                                              '_summary',
                                              '_total',
                                              '_elements',
                                              '_contained',
                                              '_containedType',
                                              'filter'
                                            );

    const PREFIXES =array('eq',
                          'ne',
                          'gt',
                          'lt',
                          'ge',
                          'le',
                          'sa',
                          'eb',
                          'ap'
    );


    static  $PREFIXESTOMYSQLDB = array('eq'=>"=",
        'ne'=>"!=",
        'gt'=>">",
        'lt'=>"<",
        'ge'=>">=",
        'le'=>"<=",
        'sa'=>"",
        'eb'=>"",
        'ap'=>""
    );

    const MODIFIERS =array('contains','exact','not');




    public function __construct()
    {
        $this->processRequestParams();
    }


    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * set GET|POST arguments
     *
     */
    private function processRequestParams(){
/*
 * Search Parameter Types	Parameters for all resources	Search result parameters
 *
 *
 *
 * */
        $requestParams=array(
            'REWRITE_COMMAND'=>array(),//this is the post get request type
            'ARGUMENTS'=>array(),//Fhir object type  arguments
            'PARAMETERS_FOR_SEARCH_RESULT'=>array(),// https://www.hl7.org/fhir/search.html
            'PARAMETERS_FOR_ALL_RESOURCES'=>array(),// https://www.hl7.org/fhir/search.html
            'POST_PARSED_JSON'=>array()// saves the json sent by request to server into this param
        );



        if( in_array($_SERVER['REQUEST_METHOD'],array('GET')) ){
            $query  = explode('&', $_SERVER['QUERY_STRING']);

            foreach( $query as $param )
            {
                // prevent notice on explode() if $param has no '='
                if (strpos($param, '=') === false) $param .= '=';
                list($name, $value) = explode('=', $param, 2);
                $name=urldecode($name);
                if(!preg_match("/[a-z]/i", $name)){
                    continue; //ignore empty name
                }
                $value=urldecode($value);

                if(in_array($name,self::PARAMETERS_FOR_SEARCH_RESULT)){

                    switch ($name) {
                        case "_sort":
                            $sortParams=explode(',', $value);
                            foreach ($sortParams as $index => $key){
                                if($key[0]==='-'){
                                    $tempValue=array('value'=> substr($key, 1),'operator'=>"DESC");
                                }elseif($key[0]==='+'){
                                    $tempValue=array('value'=>substr($key, 1),'operator'=>"ASC");
                                }
                                else{
                                    $tempValue=array('value'=>$key,'operator'=>"ASC");
                                }
                                $requestParams['PARAMETERS_FOR_SEARCH_RESULT'][$name][] =$tempValue;
                            }
                            break;
                        case "filter":
                            $cleanValue = preg_replace("/[\W]/", "", $value);
                            $operators=  preg_replace("/[\w]/", "", $value);
                            if(strpos($operators, '%') !== false){
                                $operator="LIKE";
                            }else{
                                $operator="=";
                            }
                            $tempValue=array('value'=>$cleanValue,'operator'=>$operator);
                            $requestParams['PARAMETERS_FOR_SEARCH_RESULT'][$name][] =$tempValue;
                        break;
                        default:
                            $requestParams['PARAMETERS_FOR_SEARCH_RESULT'][$name][] =$value;
                    }
                    continue;
                }

                if(in_array($name,self::PARAMETERS_FOR_ALL_RESOURCES)){
                    $requestParams['PARAMETERS_FOR_ALL_RESOURCES'][$name][] =$value;
                    continue;
                }

                if($name==="_REWRITE_COMMAND"){
                    $requestParams['REWRITE_COMMAND'] = $value;
                    continue;
                }


                $tempValue=array();

                foreach (self::PREFIXES as $prefix){

                    $found = preg_match_all('!\d+!', $value, $matches);
                    if($found>0) {
                        $prefixPos = strpos($value, $prefix);
                        if ($prefixPos !== false) {
                            $value = substr($value, strlen($prefix));
                            $tempValue = array('value' => $value, 'operator' => $prefix);
                        }
                    }

                }

                if(empty($tempValue)){
                    $tempValue=array('value'=>$value,'operator'=>null);
                }

                $modirierPos=strpos($name,":");
                if($modirierPos>0){
                    $oname=$name;
                    $name=substr($oname,0,$modirierPos);
                    $modirier=substr($oname,$modirierPos+1);
                    if (in_array($modirier,self::MODIFIERS)){
                        $tempValue['modifier']=$modirier;
                    }else{
                        $requestParams['ARGUMENTS'][$name.":".$modirier][] = $tempValue;
                        continue;
                    }
                }else{
                    $tempValue['modifier']="exact";
                }

                $requestParams['ARGUMENTS'][$name][] = $tempValue;

            }

            $this->requestParams=$requestParams;
        } elseif(in_array($_SERVER['REQUEST_METHOD'],array('POST','PUT'))){
            $request = file_get_contents('php://input');
            $requestParams['POST_PARSED_JSON']=json_decode($request,true);

            if(gettype($requestParams['POST_PARSED_JSON'])!=='array'){ //handle double encoding
                ErrorCodes::http_response_code('400','Expected json');
            }else{
                $this->requestParams=$requestParams;
            }

        }elseif( in_array($_SERVER['REQUEST_METHOD'],array('PATCH'))){
            $request = file_get_contents('php://input');
            $data=json_decode($request,TRUE);
            foreach($data as $index=>$argumentArr){
                $value=$argumentArr['value'];
                $name=ltrim($argumentArr['path'], '/');
                $name=preg_replace('/\\//', '.', $name);
                $operator=$argumentArr['op'];
                $tempValue=array('value'=>$value,'operator'=>$operator);
                $requestParams['ARGUMENTS'][$name][] = $tempValue;
            }
        }

        $this->requestParams=$requestParams;

    }


}

