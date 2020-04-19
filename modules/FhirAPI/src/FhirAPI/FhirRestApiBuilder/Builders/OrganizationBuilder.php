<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir Organization BUILDER
 */

namespace FhirAPI\FhirRestApiBuilder\Builders;



use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Organization;


class OrganizationBuilder extends Builder
{
    const PART = "PART";
    const EXACT = "EXACT";
    const SPECIFIC = "SPECIFIC";
    private $organization = null;
    private const TYPE = "Organization";
    /**
     * @var array
     */
    private $searchArrayBuilt = [];


    public function buildSearchStracture($name,$map){

        $this->searchArrayBuilt[$name]=[$map];

    }
    public function createMapping($arrayToMapping){
        $mappingTemplate =["obj_search_type", "fhir_name",  "active","fhir_type","fhir_place",  "openemr_table","openemr_column","description","search_type_method","specific"];
                  //        ["FHIROrganization",'address-city',1,      'string',   "address.city","facility",     "city",          'A city specified in an address"']

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
       // parent::setPart($this->Organization);

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
         * Name	Type	Description	Expression	In Common
            active	token	Is the Organization record active	Organization.active
            address	string	A server defined search that may match any of the string fields in the Address, including line, city, district, state, country, postalCode, and/or text	Organization.address
            address-city	string	A city specified in an address	Organization.address.city
            address-country	string	A country specified in an address	Organization.address.country
            address-postalcode	string	A postal code specified in an address	Organization.address.postalCode
            address-state	string	A state specified in an address	Organization.address.state
            address-use	token	A use code specified in an address	Organization.address.use
            endpoint	reference	Technical endpoints providing access to services operated for the organization	Organization.endpoint
                        (Endpoint)
            identifier	token	Any identifier for the organization (not the accreditation issuer's identifier)	Organization.identifier
            name	string	A portion of the organization's name or alias	Organization.name | Organization.alias
            partof	reference	An organization of which this organization forms a part	Organization.partOf
            (Organization)
            phonetic	string	A portion of the organization's name using some kind of phonetic matching algorithm	Organization.name
            type	token	A code for the type of organization	Organization.type
         *
         * */

/////////////                        FOR FUTURE USE
        /*
                $this->createMapping(["FHIROrganization",'address',0,'string','address',"facility",["city","street","state","postal_code","country_code"],'	A server defined search that may match any of the string fields in the Address, including line, city, district, state, country, postalCode, and/or text maybe need to be codded  =>  "3300 Washtenaw Avenue, Suite 227"'],self::PART);
                $this->createMapping(["FHIROrganization",'address-city',0,'string',"address.city","facility","city",'A city specified in an address"',self::PART]);
                $this->createMapping(["FHIROrganization",'address-country',0,'string',"address.country","facility","country",'A country specified in an address',self::PART]);
                $this->createMapping(["FHIROrganization",'address-postalcode',0,'string',"address.postalcode","facility","postal_code",'A postal code specified in an address',self::PART]);
                $this->createMapping(["FHIROrganization",'address-state',0,'string',"address.state","facility","state",'A state specified in an address',self::PART]);
                $this->createMapping(["FHIROrganization",'address-usee',0,'string',"address","facility",["city","street","state","postal_code","country_code"],'A use code specified in an address',self::PART]);
                $this->createMapping(["FHIROrganization",'endpoint',0,'reference',"address","facility",["city","street","state","postal_code","country_code"],'Technical endpoints providing access to services operated for the organization',self::PART]);
                $this->createMapping(["FHIROrganization",'identifier',0,'token',"identifier","facility","id","Any identifier for the organization (not the accreditation issuer's identifier",self::PART]);
                $this->createMapping(["FHIROrganization",'partof',0,'reference',"name","facility","",'An organization of which this organization forms a part',self::PART]);
                $this->createMapping(["FHIROrganization",'phonetic',0,'reference',"name","facility","name",'An organization of which this organization forms a part',self::PART]);
                $this->createMapping(["FHIROrganization",'type',0,'token',"identifier","facility","id",'A code for the type of organization',self::PART]);*/

        $this->createMapping(["FHIROrganization",'name',1,'string',"name","facility","name",'A portion of the organization\'s name or alias',self::PART,""]);
        $this->createMapping(["FHIROrganization",'_id',1,'token',"_id","facility","id",'A code for the type of organization',self::EXACT,""]);
        $this->createMapping(["FHIROrganization",'active',1,'token',"","facility","active","Is the Organization record active",self::EXACT,""]);
        $this->createMapping(["FHIROrganization",'type',1,'token',"","facility","pos_code","A Organization type",self::EXACT,""]);
    }


}
