<?php
/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class Fhir Encounter BUILDER
 */

namespace FhirAPI\FhirRestApiBuilder\Builders;



use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Encounter;


class EncounterBuilder extends Builder
{
    const PART = "PART";
    const EXACT = "EXACT";
    const SPECIFIC = "SPECIFIC";
    const EQUALITY = "EQUALITY";
    const COLLECT_FHIR_OBJECT = "collect_fhir_object";
    private $Encounter = null;
    private const TYPE = "Encounter";
    /**
     * @var array
     */
    private $searchArrayBuilt = [];


    public function buildSearchStracture($name,$map){

        $this->searchArrayBuilt[$name]=[$map];

    }
    public function createMapping($arrayToMapping){
        $mappingTemplate =["obj_search_type", "fhir_name",  "active","fhir_type","fhir_place",  "openemr_table","openemr_column","description","search_type_method","specific"];
                  //        ["FHIREncounter",'address-city',1,      'string',   "address.city","facility",     "city",          'A city specified in an address"']

        $mapThis = array_combine($mappingTemplate,$arrayToMapping);
        if($mapThis['active']) {
            $this->buildSearchStracture($mapThis['fhir_name'], $mapThis);
        }
    }

    public function __construct($apiVersion)
    {
        $this->buildSearchParams();

        parent::__construct($apiVersion);
        parent::setType(self::TYPE);
       // parent::setPart($this->Encounter);

         parent::setSearchParams($this->getSearchArrayBuilt());
     }

    /**
     * @return array
     */
    public function getSearchArrayBuilt(): array
    {
        if (is_null($this->searchArrayBuilt)){
            return [];
        }
        return $this->searchArrayBuilt;
    }

    /**
     * @param array $searchArrayBuilt
     */
    public function setSearchArrayBuilt(array $searchArrayBuilt): void
    {
        $this->searchArrayBuilt = $searchArrayBuilt;
    }


    private function buildSearchParams()
    {

        /*
         8.11.6 Search Parameters
            Search parameters for this resource. The common parameters also apply. See Searching for more information about searching in REST, messaging, and services.

            Name	        Type	        Description	                                                        Expression	                          In Common
            account     	reference	    The set of accounts that may be used for billing for this Encounter	Encounter.account
                                                                                                                (Account)
            appointment 	reference	    The appointment that scheduled this encounter	                    Encounter.appointment
                                                                                                                (Appointment)
            based-on	    reference	    The ServiceRequest that initiated this encounter	                Encounter.basedOn
                                                                                                                (ServiceRequest)
            class	        token	        Classification of patient encounter	Encounter.class
            date        	date	        A date within the period the Encounter lasted	                    Encounter.period            	       17 Resources
            diagnosis	    reference   	The diagnosis or procedure relevant to the encounter	            Encounter.diagnosis.condition
                                                                                                                (Condition, Procedure)
            episode-of-care	reference	    Episode(s) of care that this encounter should be recorded against	Encounter.episodeOfCare
                                                                                                                (EpisodeOfCare)
            identifier  	token	        Identifier(s) by which this encounter is known	Encounter.identifier                                       30 Resources
            length	        quantity    	Length of encounter in days	                                        Encounter.length
            location	    reference	    Location the encounter takes place	                                Encounter.location.location
                                                                                                                (Location)
            location-period	date	        Time period during which the patient was present at the location	Encounter.location.period
            part-of	reference	            Another Encounter this encounter is part of                     	Encounter.partOf
                                                                                                                (Encounter)
            participant	    reference	    Persons involved in the encounter other than the patient	        Encounter.participant.individual
                                                                                                                (Practitioner, PractitionerRole, RelatedPerson)
            participant-type token	        Role of participant in encounter	                                Encounter.participant.type
            patient     	reference	    The patient or group present at the encounter	                    Encounter.subject.where(resolve() is Patient)
                                                                                                                (Patient)	                            33 Resources
            practitioner	reference	    Persons involved in the encounter other than the patient	        Encounter.participant.individual.where(resolve() is Practitioner)
                                                                                                                (Practitioner)
            reason-code	token Coded         reason the encounter takes place	                                Encounter.reasonCode
            reason-reference  reference	    Reason the encounter takes place (reference)	                    Encounter.reasonReference
                                                                                                                (Condition, Observation, Procedure, ImmunizationRecommendation)
            service-provider  reference 	The organization (facility) responsible for this encounter	        Encounter.serviceProvider
                                                                                                                (Organization)
            special-arrangement	token   	Wheelchair, translator, stretcher, etc.	                            Encounter.hospitalization.specialArrangement
            status	            token	    planned | arrived | triaged | in-progress | onleave |               Encounter.status
                                            finished | cancelled +
            subject	            reference	The patient or group present at the encounter	                    Encounter.subject
                                                                                                                (Group, Patient)
            type	            token   	Specific type of encounter	Encounter.type	5 Resources
 */



        $this->createMapping(["FHIREncounter",'_id',1,'token',"_id","form_encounter","form_encounter.id",'A code for the type of Encounter',self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'date',1,'Period',"Encounter.period","form_encounter","form_encounter.date",' A date within the period the Encounter lasted',self::EXACT,"date"]);
        $this->createMapping(["FHIREncounter",'appointment',1,'reference',"Encounter.appointment","form_encounter","form_encounter.eid",' The appointment that scheduled this encounter',self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'patient',1,'reference',"Encounter.subject.where(resolve() is Patient)(Patient)","form_encounter","form_encounter.pid",'The patient or group present at the encounter',self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'status',1,'EncounterStatus',"","form_encounter","form_encounter.status","EncounterStatus (Required)",self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'service-type',1,'EncounterStatus',"","form_encounter","lr.option_id","EncounterStatus (Required)",self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'service-provider',1,'EncounterStatus',"","form_encounter","facility_id","EncounterStatus (Required)",self::EXACT,""]);
        $this->createMapping(["FHIREncounter",'arrival-way',1,'form_encounter',"","form_encounter","arrival_way"," ",self::PART,""]);
        $this->createMapping(["FHIREncounter",'reason-codes-details',1,'form_encounter',"","form_encounter","reason_codes_details"," ",self::PART,""]);

        $this->createMapping(["FHIREncounter",'status-extended',1,'form_encounter',"","form_encounter","all_statuses"," ",self::EXACT,""]);

        /////////////////////////////////////////////SORT BY id,date,appointment,patient,status    from mysql table form_encounter column id
        $this->createMapping(["FHIREncounter",'_sort',1,'token',"id,date,appointment,patient,status,priority,service-type,status_update_date","form_encounter","form_encounter.id,form_encounter.date,form_encounter.eid,form_encounter.pid,form_encounter.status,form_encounter.priority,service_type_seq,form_encounter.status_update_date",'A code for the type of Encounter',self::EXACT,""]);

        /////////////////////////////////////////////INCLUDE records from other table
        $this->createMapping(["FHIREncounter",'_include',1,'token',"Encounter:organization,Encounter:patient","Organization,Patient","",'Collect every patient fhir object  for this encounter',self::COLLECT_FHIR_OBJECT,"ServiceProvider,Subject"]);
      //  $this->createMapping(["FHIREncounter",'_include',1,'token',"Encounter:patient","Patient","",'Collect every patient fhir object  for this encounter',self::COLLECT_FHIR_OBJECT,"Subject"]);

        ///////////////////////////////////////////SUMMaRY
        ///
        ///    GET [base]/ValueSet?_summary=true
        //The _summary parameter requests the server to return a subset of the resource. It can contain one of the following values:
        //
        //true	Return a limited subset of elements from the resource. This subset SHOULD consist solely of all supported elements that are marked as "summary" in the base definition of the resource(s) (see ElementDefinition.isSummary)
        //text	Return only the "text" element, the 'id' element, the 'meta' element, and only top-level mandatory elements
        //data	Remove the text element
        //count	Search only: just return a count of the matching resources, without returning the actual matches
        //false	Return all parts of the resource(s)
        $this->createMapping(["FHIREncounter",'_summary',1,'',"count","","","","","count"]);


    }


}
