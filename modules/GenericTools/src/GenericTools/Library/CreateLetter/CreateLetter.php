<?php


namespace GenericTools\Library\CreateLetter;


class CreateLetter
{
    private $letter = NULL;

    /**
     * CreateLetter constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->letter_class = $params['letter_class'];
        $this->controller = $params["controller"];
        $this->params = $params;
        $this->storage = $params['storage'];
        $this->filename = $params['filename'];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function renderLetter()
    {
        $this->letter = new CreateLetterContext( new $this->letter_class );

        if( isset($this->storage) && strlen($this->storage) > 0 ){
            //Stage 2 : Save binary string to CouchDB(....etc)
            $this->letter_binary = $this->letter->doRender($this->params);
            return $this->saveLetter();
        } else {
            //Stage 2: Render pdf to browser
            return $this->letter->doRender($this->params);
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function saveLetter()
    {
        switch ($this->storage):
            case "CouchDB":
                $couchService = $this->controller->container->get("GenericTools\Service\CouchdbService");
                $couchService->connect();
                $data =  $couchService->saveDoc($this->letter_binary);
                $data['io'] = $this->letter_binary;
                return $data;
                break;
            case null :
                throw new \Exception("Type of storage was is not configured");
                break;
        endswitch;
    }


}
