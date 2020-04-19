<?php
/**
 * @author Dror Golan drorgo@matrix.co.il
 * FHIR ORGANIZATION trait
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits;


use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPoint;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPointSystem;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPointUse;
use OpenEMR\FHIR\R4\FHIRElement\FHIRString;

trait FHIROrganizationTelecomTrait
{
    public function buildFhirSystemType($createType,$type,$telecom,&$FHIRContactPoint,$alias){
        $createType->setValue($telecom[$type]);
        $FHIRContactPoint->setValue($createType);

        $FHIRContactPointSystem = new FHIRContactPointSystem();
        $FHIRContactPointSystem->setValue($alias);
        $FHIRContactPoint->setSystem($FHIRContactPointSystem);
    }

    public function buildFHIRTelecom(&$FHIRContactPoint,$telecom)
    {

        if (key_exists('phone_home', $telecom) || key_exists('phone', $telecom)) {
            $phone = new FHIRString();
            $this->buildFhirSystemType($phone,'phone',$telecom,$FHIRContactPoint,"phone");

            $FHIRContactPointUse = new FHIRContactPointUse();
            $FHIRContactPointUse->setValue('home');
            $FHIRContactPoint->setUse($FHIRContactPointUse);
        }

        if (key_exists('phone_work', $telecom)) {
            $phone = new FHIRString();
            $this->buildFhirSystemType($phone,'phone_work',$telecom,$FHIRContactPoint,"phone");

            $FHIRContactPointUse = new FHIRContactPointUse();
            $FHIRContactPointUse->setValue('work');
            $FHIRContactPoint->setUse($FHIRContactPointUse);
        }

        if (key_exists('phone_temp', $telecom)) {
            $phone = new FHIRString();
            $this->buildFhirSystemType($phone,'phone_temp',$telecom,$FHIRContactPoint,"phone");

            $FHIRContactPointUse = new FHIRContactPointUse();
            $FHIRContactPointUse->setValue('temp');
            $FHIRContactPoint->setUse($FHIRContactPointUse);
        }

        if (key_exists('phone_old', $telecom)) {
            $this->buildFhirSystemType($phone,'phone_old',$telecom,$FHIRContactPoint,'phone');

            $FHIRContactPointUse = new FHIRContactPointUse();
            $FHIRContactPointUse->setValue('old');
            $FHIRContactPoint->setUse($FHIRContactPointUse);
        }

        if (key_exists('mobile', $telecom) || key_exists('phone_mobile', $telecom)|| key_exists('phone_cell', $telecom)) {
            $mobile = new FHIRString();
            $this->buildFhirSystemType($mobile,'mobile',$telecom,$FHIRContactPoint,'phone');

            $FHIRContactPointUse = new FHIRContactPointUse();
            $FHIRContactPointUse->setValue('mobile');
            $FHIRContactPoint->setUse($FHIRContactPointUse);
        }

        if (key_exists('fax', $telecom)) {
            $fax = new FHIRString();
            $this->buildFhirSystemType($fax,'fax',$telecom,$FHIRContactPoint,'fax');

        }

        if (key_exists('pager', $telecom)) {
            $pager = new FHIRString();
            $this->buildFhirSystemType($pager,'pager',$telecom,$FHIRContactPoint,'pager');

        }
        if (key_exists('url', $telecom)) {
            $url = new FHIRString();
            $this->buildFhirSystemType($url,'url',$telecom,$FHIRContactPoint,'url');

        }

        if (key_exists('sms', $telecom)) {
            $sms = new FHIRString();
            $this->buildFhirSystemType($sms,'sms',$telecom,$FHIRContactPoint,'sms');

        }

        if (key_exists('other', $telecom)) {
            $other = new FHIRString();
            $this->buildFhirSystemType($other,'other',$telecom,$FHIRContactPoint,'other');

        }
        if (key_exists('email', $telecom)) {
            $email = new FHIRString();
            $this->buildFhirSystemType($email,'email',$telecom,$FHIRContactPoint,'email');
        }
        if (key_exists('other', $telecom)) {
            $other = new FHIRString();
            $this->buildFhirSystemType($other,'other',$telecom,$FHIRContactPoint,'other');
        }

        return $FHIRContactPoint;
    }
    public function createFHIRTelecom($telecomData)
    {

        $contactPoint = null;
        $systemFhir = ["phone","fax","email","pager","url","sms","other","phone_home","phone_mobile","phone_work","mobile"];

        foreach($systemFhir as $key=>$value){
            if($telecomData[$value] != null)
                $info[$value]=$telecomData[$value];
        }

        foreach ($info as $key => $value) {
            $FHIRContactPoint = new FHIRContactPoint();
            $FHIRArrayOfTelecoms[] = $this->buildFHIRTelecom($FHIRContactPoint,array($key => $value));
        }

        return $FHIRArrayOfTelecoms;
    }
}
