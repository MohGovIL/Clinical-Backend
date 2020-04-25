<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Questionnaire
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Questionnaire;

use function DeepCopy\deep_copy;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;



/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPublicationStatus;
use OpenEMR\FHIR\R4\FHIRElement\FHIRQuestionnaireItemType;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResource\FHIRQuestionnaire\FHIRQuestionnaireItem;


class FhirQuestionnaireMapping extends FhirBaseMapping implements MappingData
{

    private $adapter = null;
    private $container = null;
    private $FHIRQuestionnaire = null;
    private $formQuestionMapping;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRQuestionnaire = new FHIRQuestionnaire;
    }

    public function fhirToDb($FhirObject)
    {
        return array();
    }

    public function getFormQuestionMapping()
    {
        return $this->formQuestionMapping;
    }


    public function initFhirObject()
    {
        $FHIRQuestionnaire = new FHIRQuestionnaire;

        $FhirId = $this->createFHIRId(null);
        $FHIRQuestionnaire->setId($FhirId);
        $FHIRPublicationStatus=new FHIRPublicationStatus;
        $FHIRPublicationStatus->setValue(null);
        $FHIRQuestionnaire->setStatus($FHIRPublicationStatus);
        $FHIRString=$this->createFHIRString(null);
        $FHIRQuestionnaire->setTitle(deep_copy($FHIRString));
        $FHIRQuestionnaire->setName(deep_copy($FHIRString));

        $FHIRQuestionnaireItem= new FHIRQuestionnaireItem;
        $FHIRQuestionnaireItem->setText(deep_copy($FHIRString));
        $FHIRQuestionnaireItemType= new FHIRQuestionnaireItemType;
        $FHIRQuestionnaireItemType->setValue(null);
        $FHIRQuestionnaireItem->setType($FHIRQuestionnaireItemType);
        $FHIRQuestionnaireItem->setLinkId(deep_copy($FHIRString));

        $FHIRQuestionnaire->addItem($FHIRQuestionnaireItem);

        $this->FHIRQuestionnaire=$FHIRQuestionnaire;
        return $FHIRQuestionnaire;
    }

    /**
     * create FHIRQuestionnaire
     *
     * @param array
     * @param bool
     *
     * @return FHIRQuestionnaire | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $questionnaireFromDb=$parmas[0];

        if (!is_array($questionnaireFromDb) || count($questionnaireFromDb) < 1) {
            return null;
        }
        $FHIRQuestionnaire=$this->FHIRQuestionnaire;

        $FHIRQuestionnaire->setId($questionnaireFromDb['id']);
        $status=($questionnaireFromDb['state']) ? "active" : "retired";
        $FHIRQuestionnaire->getStatus()->setValue($status);
        $FHIRQuestionnaire->getTitle()->setValue($questionnaireFromDb['name']);
        $FHIRQuestionnaire->getName()->setValue($questionnaireFromDb['directory']);

        $this->formQuestionMapping=array();

        $linkIds=explode(",", $questionnaireFromDb['linkIds']);
        $formTables=explode(",", $questionnaireFromDb['form_tables']);
        $columnTypes=explode(",", $questionnaireFromDb['column_types']);
        $questions=explode(",", $questionnaireFromDb['questions']);

        if(!empty($questions)){
            $FHIRQuestionnaire->getItem()[0]->getText()->setValue($questions[0]);
            $FHIRQuestionnaire->getItem()[0]->getType()->setValue($columnTypes[0]);
            $FHIRQuestionnaire->getItem()[0]->getLinkId()->setValue($linkIds[0]);

            // map the questions id and the table where the answers are
            $this->formQuestionMapping[$formTables[0]][]=$linkIds[0];

            unset($questions[0]);
            unset($columnTypes[0]);
            unset($formTables[0]);
            unset($linkIds[0]);

        }


        foreach($questions as $index => $question){

            $tempQuestionnaireItem= new FHIRQuestionnaireItem;
            $FHIRTempTextString=$this->createFHIRString($question);
            $FHIRTempTypeString= new FHIRQuestionnaireItemType;
            $type=$columnTypes[$index];
            $FHIRTempTypeString->setValue($type);
            $tempQuestionnaireItem->setText($FHIRTempTextString);
            $tempQuestionnaireItem->setType($FHIRTempTypeString);
            $FHIRLinkIdString=$this->createFHIRString($linkIds[$index]);
            $tempQuestionnaireItem->setLinkId($FHIRLinkIdString);
            $FHIRQuestionnaire->addItem($tempQuestionnaireItem);

            // map the questions id and the table where the answers are
            $this->formQuestionMapping[$formTables[$index]][]=$linkIds[$index];
        }

        return $FHIRQuestionnaire;


    }


    public function validateDb($data)
    {
        return true;
    }


    public function parsedJsonToDb($parsedData)
    {
        return array();

    }

    public function parsedJsonToFHIR($parsedData)
    {

        return array();

    }

    public function getDbDataFromRequest($data)
    {
        return array();
    }

    public function updateDbData($data,$id)
    {
        return array();
    }


}
