<?php


namespace GenericTools\Traits;


trait magicMethods
{
    //Setter and getter for any variables for class
    //The function returns an instance of the current class,
    //example: $this->setWhere(["is_deleted" => 0])
    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    function __call($name, $arguments)
    {
        $typeFunction = substr($name,0,3);
        $nameFunction = strtolower(substr($name, 3));
        if( strlen($nameFunction) > 0 && $typeFunction == "set"){
            if($nameFunction == "join"){
                if($arguments[1]==true) {
                    $tmpArr = [];
                    foreach ($arguments[0][2] as $key => $value) {
                        if (is_array($arguments[0][2])) {
                            $tmpArr[$arguments[0][0] . "_" . $value] = $value;
                        }

                    }
                    $arguments[0][2] = $tmpArr;
                }
                $this->$nameFunction[] = $arguments[0];
            }
            else {
                $this->$nameFunction = $arguments[0];
            }
            return $this;
        }

        if( $typeFunction == "get" && isset($this->$nameFunction) ){
            return $this->$nameFunction;
        }

        if( $typeFunction == "del" &&  strlen($nameFunction) > 0 ){
            if (isset($this->$nameFunction) ) {
                unset($this->$nameFunction);
            }
            return $this;
        }

    }

}
