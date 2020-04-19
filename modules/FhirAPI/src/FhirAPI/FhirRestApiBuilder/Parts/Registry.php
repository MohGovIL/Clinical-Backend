<?php


namespace FhirAPI\FhirRestApiBuilder\Parts;


class Registry
{
    protected static $data = [];
    protected static $function = "function";
    static function setPart( $builder,$type,$operation,$key, $value){
        if(is_null( self::$data[$builder])){
            self::$data[$builder]  = [];
        }
        if(is_null( self::$data[$builder][$operation])){
            self::$data[$builder][$operation]  = [];
        }
        if($type != "" && is_null( self::$data[$builder][$operation][$type])){
            self::$data[$builder][$operation][$type]  = [];
            self::$data[$builder][$operation][$type][$key]=$value;
        }
        else{
            self::$data[$builder][$operation][$key]=$value;
        }


    }


    public static function getAllRoutes(){
        $routingData=[];
        foreach(self::$data as $key=>$value){
            foreach($value as $key=>$value) {
                if ($key == "routes") {
                    foreach ($value as $key => $value)
                        foreach ($value as $key => $value) {
                            $routingData[$key]=$value;
                        }
                }
            }

        }
        return $routingData;
    }

    public static function getAllSearchParams(){
        $searchParams=[];
        foreach(self::$data as $key=>$value){
            foreach($value as $key=>$value) {
                if ($key == "search") {
                    foreach ($value as $key => $value)
                        foreach ($value as $value) {
                            $searchParams[]=$value;
                        }
                }
            }

        }
        return $searchParams;
    }

    public static function getAllFunctions(){
        $searchParams=[];
        foreach(self::$data as $key=>$value){
            foreach($value as $key=>$value) {
                if ($key == "function") {
                    foreach ($value as $key => $value)
                        foreach ($value as $value) {
                            $searchParams[]=$value;
                        }
                }
            }

        }
        return $searchParams;
    }

    public static function getSearchParamsAvailibleForThisStrategy($Element){
        $searchParams=[];
        foreach(self::$data as $key=>$value){
            if($key==$Element) {
                foreach ($value as $key => $value) {
                    if($key=='search') {
                        foreach ($value as $key => $value) {
                            foreach ($value as $key => $value) {
                                $searchParams['search'][$key] = $value[0];
                            }
                        }
                        }


                }
            }
        }
        return $searchParams;
    }


}
