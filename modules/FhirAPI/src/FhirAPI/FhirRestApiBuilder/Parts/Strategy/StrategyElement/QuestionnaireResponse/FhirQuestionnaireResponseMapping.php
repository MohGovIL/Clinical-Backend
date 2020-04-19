<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR QuestionnaireResponse
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\QuestionnaireResponse;

use function DeepCopy\deep_copy;
use Exception;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Questionnaire\Questionnaire;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\RelatedPersonTable;
use Interop\Container\ContainerInterface;
use FhirAPI\FhirRestApiBuilder\Parts\Forms\GenericFormHandler;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaireResponse;
use OpenEMR\FHIR\R4\FHIRElement\FHIRCanonical;
use OpenEMR\FHIR\R4\FHIRElement\FHIRQuestionnaireResponseStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResource\FHIRQuestionnaireResponse\FHIRQuestionnaireResponseAnswer;
use OpenEMR\FHIR\R4\FHIRResource\FHIRQuestionnaireResponse\FHIRQuestionnaireResponseItem;


class FhirQuestionnaireResponseMapping extends FhirBaseMapping implements MappingData
{

    const FHIR_QUESTIONNAIRE  ='Questionnaire';
    const FHIR_ENCOUNTER  ='Encounter';
    const FHIR_PATIENT  ='Patient';
    const FHIR_PRACTITIONER  ='Practitioner';

    private $adapter = null;
    private $container = null;
    private $FHIRQuestionnaireResponse = null;
    private $questionnaireId = null;
    private $formName = null;
    private $formQuestionMapping;
    private $getAnswersMapping =array();

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Zend\Db\Adapter\Adapter');
        $this->FHIRQuestionnaireResponse = new FHIRQuestionnaireResponse;
    }


    public function setQuestionnaire($questionnaireId){
        $this->questionnaireId=intval($questionnaireId);
    }


    public function initFhirObject()
    {
        $FHIRQuestionnaireResponse = new FHIRQuestionnaireResponse;
        $FhirId = $this->createFHIRId(null);
        $FHIRQuestionnaireResponse->setId($FhirId);


        $FHIRCanonical =new FHIRCanonical;
        $FHIRCanonical->setValue(self::FHIR_QUESTIONNAIRE."/".$this->questionnaireId);
        $FHIRQuestionnaireResponse->setQuestionnaire($FHIRCanonical);

        //$FHIRIdentifier=$this->createFHIRIdentifier(null);
        //$FHIRQuestionnaireResponse->setIdentifier($FHIRIdentifier);

        $FHIRDateTime=$this->createFHIRDateTime(null);
        $FHIRQuestionnaireResponse->setAuthored($FHIRDateTime);

        $parms=array (
            'paramsFromUrl' => array($this->questionnaireId),
            'paramsFromBody' => array(),
            'container' => $this->container
        );

        $FHIRQuestionnaireResponseStatus= new FHIRQuestionnaireResponseStatus;
        $FHIRQuestionnaireResponseStatus->setValue(null);
        $FHIRQuestionnaireResponse->setStatus($FHIRQuestionnaireResponseStatus);

        $Questionnaire= new Questionnaire($parms);
        $FHIRQuestionnaire=$Questionnaire->read();

        $this->formQuestionMapping=$Questionnaire->getFormQuestionMapping();
        $items=$FHIRQuestionnaire->getItem();
        $this->formName=$FHIRQuestionnaire->getName()->getValue();

        //$FHIRQuestionnaireResponse->setQuestionnaire();

        $FHIRReference=  $this->createFHIRReference(["reference" => null]);
        $FHIRQuestionnaireResponse->setSubject(deep_copy($FHIRReference));
        $FHIRQuestionnaireResponse->setEncounter(deep_copy($FHIRReference));
        $FHIRQuestionnaireResponse->setAuthor(deep_copy($FHIRReference));
        $FHIRQuestionnaireResponse->setSource(deep_copy($FHIRReference));

        foreach($items as $index => $item){
            $FHIRQuestionnaireResponseItem=new FHIRQuestionnaireResponseItem;
            $FHIRQuestionnaireResponseItem->setLinkId($item->getLinkId());
            $FHIRQuestionnaireResponseItem->setText($item->getText()) ;
            $FHIRQuestionnaireResponseAnswer= new FHIRQuestionnaireResponseAnswer;
            $typeFunc='setValue'.ucfirst($item->getType()->getValue());

            $typeFuncGet=$typeFunc;
            $typeFuncGet[0]='g';
            $this->getAnswersMapping[$item->getLinkId()->getValue()]=$typeFuncGet;

            $FHIRQuestionnaireResponseAnswer->$typeFunc("");
            $FHIRQuestionnaireResponseItem->addAnswer($FHIRQuestionnaireResponseAnswer);
            $FHIRQuestionnaireResponse->addItem($FHIRQuestionnaireResponseItem);
        }

        $this->FHIRQuestionnaireResponse=$FHIRQuestionnaireResponse;
        return $FHIRQuestionnaireResponse;
    }


    /**
     * create FHIRQuestionnaireResponse
     *
     * @param array
     * @param bool
     *
     * @return FHIRQuestionnaireResponse | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $questionnaireResponseFromDb=$parmas[0];
        if (!is_array($questionnaireResponseFromDb) || count($questionnaireResponseFromDb) < 1) {
            return null;
        }
        $FHIRQuestionnaireResponse=$this->FHIRQuestionnaireResponse;
        $FHIRQuestionnaireResponse->setId($questionnaireResponseFromDb['id']);

        $FHIRDateTime=$this->createFHIRDateTime(null,null,$questionnaireResponseFromDb['update_date']);
        $FHIRQuestionnaireResponse->getAuthored()->setValue($FHIRDateTime->getValue());

        //$FHIRQuestionnaireResponse->getIdentifier()->setValue($questionnaireResponseFromDb['form_id']);

        $FHIRQuestionnaireResponse->getStatus()->setValue(trim($questionnaireResponseFromDb['status']));

        $subject=$questionnaireResponseFromDb['subject_type'].'/'.$questionnaireResponseFromDb['subject'];
        $FHIRQuestionnaireResponse->getSubject()->getReference()->setValue($subject);

        $author=self::FHIR_PRACTITIONER.'/'.$questionnaireResponseFromDb['update_by'];
        $FHIRQuestionnaireResponse->getAuthor()->getReference()->setValue($author);

        $source=$questionnaireResponseFromDb['source_type'].'/'.$questionnaireResponseFromDb['source'];
        $FHIRQuestionnaireResponse->getSource()->getReference()->setValue($source);

        $encounter=self::FHIR_ENCOUNTER.'/'.$questionnaireResponseFromDb['encounter'];
        $FHIRQuestionnaireResponse->getEncounter()->getReference()->setValue($encounter);


        $className = str_replace(' ', '',ucwords(str_replace("_"," ",$this->formName)));
        $parmas=array(
            "container"=>$this->container,
            "adapter"=>$this->adapter ,
            "formQuestionMapping" => $this->formQuestionMapping,
            "encounter"=> $questionnaireResponseFromDb['encounter'],
            "form_id" =>$questionnaireResponseFromDb['id'],
            "handlingClass"=>$className,
        );

        $handlerFinder=  new GenericFormHandler($parmas);
        $formHandler=  $handlerFinder->getFormHandler();
        $formAnswers=$formHandler->getFormAnswers();

        $items=$FHIRQuestionnaireResponse->getItem();

        $methods=get_class_methods($items[0]->getAnswer()[0]);
        $getMethods=array_filter($methods, function($method) {return strpos($method, 'getValue') !== false;});

        foreach($items as $index => $item){
            $answer=$item->getAnswer()[0];
            foreach($getMethods as $place => $methodName){
                if(!is_null($answer->$methodName())){
                   $methodName[0]='s';
                   $qid=$item->getLinkId()->getValue();
                   $answer->$methodName($formAnswers[$qid]);
                }
            }
        }

        return $FHIRQuestionnaireResponse;


    }



    public function fhirToDb($FhirObject)
    {
        $questionnaireResponse=array();
        $answers=array();

        $questionnaireResponse['id'] = (is_null($FhirObject->getId())) ? null : $FhirObject->getId()->getValue();

        $questionnaireResponse['form_name'] =$this->formName;

        $questionnaireResponse['status'] = (is_null($FhirObject->getStatus())) ? null : $FhirObject->getStatus()->getValue();

        $updateDate=(is_null($FhirObject->getAuthored())) ? null : $FhirObject->getAuthored()->getValue();
        $questionnaireResponse['update_date'] = (is_null($updateDate)) ? null :  $this->convertToDateTime($updateDate);

        $subjectLink=(is_null($FhirObject->getSubject())) ? null : $FhirObject->getSubject()->getReference()->getValue();
        $subject=substr($subjectLink,strpos($subjectLink,'/')+1,20);
        $questionnaireResponse['subject'] = $subject;

        $subject_type=substr($subjectLink,0,strpos($subjectLink,'/'));
        $questionnaireResponse['subject_type'] = $subject_type;

        $authorLink=(is_null($FhirObject->getAuthor())) ? null : $FhirObject->getAuthor()->getReference()->getValue();
        $author=substr($authorLink,strpos($authorLink,'/')+1,20);
        $questionnaireResponse['update_by'] =$author;

        $sourceLink=(is_null($FhirObject->getAuthor())) ? null : $FhirObject->getSource()->getReference()->getValue();
        $source=substr($sourceLink,strpos($sourceLink,'/')+1,20);
        $questionnaireResponse['source'] = $source;

        $source_type=substr($subjectLink,0,strpos($subjectLink,'/'));
        $questionnaireResponse['source_type'] = $source_type;

        $encounterLink=(is_null($FhirObject->getAuthor())) ? null : $FhirObject->getEncounter()->getReference()->getValue();
        $encounter=substr($encounterLink,strpos($encounterLink,'/')+1,20);
        $questionnaireResponse['encounter'] = $encounter;

        $items=$FhirObject->getItem();
        $answersMapping=$this->getAnswersMapping;

        foreach ($items as $index => $item){
            $qid=$item->getLinkId()->getValue();
            $answerObj=$item->getAnswer()[0];
            $getFunc=$answersMapping[$qid];
            $answer=$answerObj->$getFunc();
            $answers[$qid]=$answer;
        }

        return array(
                        "questionnaire_response"=>$questionnaireResponse,
                        "answers"=>$answers,
                        "questionsMap"=>$this->formQuestionMapping
                    );
    }


    /**
     * check if RelatedPerson data is valid
     *
     * @param array
     * @return bool
     */
    public function validateDb($data)
    {
        return true;
    }


    public function parsedJsonToDb($parsedData)
    {
        return $parsedData;

    }

    public function parsedJsonToFHIR($parsedData)
    {
        return null;

    }


    public function getDbDataFromRequest($data)
    {
        //extract Questionnaire id same one that is sent in Questionnaire read request
        $qid=substr($data['questionnaire'],strlen(self::FHIR_QUESTIONNAIRE)+1,20);
        $this->setQuestionnaire($qid);
        $this->initFhirObject();
        $this->arrayToFhirObject($this->FHIRQuestionnaireResponse,$data);
        $dBdata = $this->fhirToDb($this->FHIRQuestionnaireResponse);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $relatedPersonTable = $this->container->get(RelatedPersonTable::class);
        $primaryKey='id';
        $updated=$relatedPersonTable->safeUpdate($data['related_person'],array($primaryKey=>$id));
        $this->initFhirObject();
        return $this->DBToFhir($updated);
    }

    private function buildParams($QuestionnaireResponse)
    {
        $className = str_replace(' ', '',ucwords(str_replace("_"," ",$QuestionnaireResponse['form_name'])));
        $parmas=array(
            "container"=>$this->container,
            "adapter"=>$this->adapter ,
            "formQuestionMapping" => $this->formQuestionMapping,
            "encounter"=> $QuestionnaireResponse['encounter'],
            "form_id" =>$QuestionnaireResponse['id'],
            "handlingClass"=>$className,
        );

        return $parmas;
    }

    public function saveAnswers($data,$QuestionnaireResponse)
    {
        $parmas=$this->buildParams($QuestionnaireResponse);
        $handlerFinder=  new GenericFormHandler($parmas);
        $formHandler=  $handlerFinder->getFormHandler();
        $formAnswers=$formHandler->saveFormAnswers($data);

        return $QuestionnaireResponse['id'];
    }

}
