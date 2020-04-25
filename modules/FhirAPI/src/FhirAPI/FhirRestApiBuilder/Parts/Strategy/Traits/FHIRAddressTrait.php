<?php
/**
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * FHIR ADDRESS trait
 */
namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits;

use OpenEMR\FHIR\R4\FHIRElement\FHIRAddress;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAddressType;
use OpenEMR\FHIR\R4\FHIRElement\FHIRString;

trait FHIRAddressTrait
{

    /**
     * set FHIRAddress element
     *
     * @param array
     * @param array
     *
     * @return bool;
     */
    public function createFHIRAddress($dataAddress)
    {
        $addressArr = array();

        $addressArr['city'] = ( !empty($dataAddress['city']) ) ? $dataAddress['city'] : null;
        $addressArr['state'] =( !empty($dataAddress['state']) ) ? $dataAddress['state'] : null;
        $addressArr['postalCode'] =( !empty($dataAddress['postal_code']) ) ? $dataAddress['postal_code'] : null;
        $addressArr['country'] =( !empty($dataAddress['country_code']) ) ? $dataAddress['country_code'] : null;
        $addressArr['street'] =( !empty($dataAddress['street']) ) ? $dataAddress['street'] : "  ";
        $addressArr['streetNumber']  =( !empty($dataAddress['mh_house_no']) ) ?$dataAddress['mh_house_no'] : null;
        $addressArr['PoBox'] =( !empty($dataAddress['mh_pobox']) ) ? $dataAddress['mh_pobox'] : null;

        $fhirTemp = self::buildFHIRAddress($addressArr);
        if(!is_null($fhirTemp))
        {
          $FHIRAddress[] =  $fhirTemp  ;
          return $FHIRAddress;
        }

        return null;



    }


    /**
     * create FHIRAddress element
     *
     * @param array
     *
     * @return FHIRAddress | null
     */

    public function buildFHIRAddress($addressArr)
    {
        $FHIRAddress = new FHIRAddress;
        $flag = false;
        $addressType = 0; // 0-undefined 1-postal 2-physical 3-both


        if (key_exists('type', $addressArr)) {
            $type = new FHIRAddressType;
            $type->setValue($addressArr['type']);
            $FHIRAddress->setType($type);
            $flag = true;
        }

        if (key_exists('text', $addressArr)) {
            $text = new FHIRString();
            $text->setValue($addressArr['text']);
            $FHIRAddress->setText($text);
            $flag = true;
        }
        if (key_exists('line', $addressArr)) {
            $line = new FHIRString();
            $line->setValue($addressArr['line']);
            $FHIRAddress->addLine($line);
            $flag = true;
        } else {

            if (key_exists('street', $addressArr)) {
                $street = new FHIRString();
                $street->setValue($addressArr['street']);
                $FHIRAddress->addLine($street);
                $flag = true;
            }
            if (key_exists('streetNumber', $addressArr)) {
                $streetNumber = new FHIRString();
                $streetNumber->setValue($addressArr['streetNumber']);
                $FHIRAddress->addLine($streetNumber);
                $addressType = 1;
                $flag = true;
            }
            if (key_exists('PoBox', $addressArr)) {
                $PoBox = new FHIRString();
                $PoBox->setValue($addressArr['PoBox']);
                $FHIRAddress->addLine($PoBox);
                $addressType = ($addressType === 0) ? 2 : 3;
                $flag = true;
            }

            if ($addressType !== 0 && !(key_exists('type', $addressArr))) {
                $type = new FHIRAddressType;

                switch ($addressType) {
                    case 1:
                        $type->setValue('postal');
                        $FHIRAddress->setType($type);
                        break;
                    case 2:
                        $type->setValue('physical');
                        $FHIRAddress->setType($type);
                        break;
                    case 3:
                        $type->setValue('both');
                        $FHIRAddress->setType($type);
                        break;
                }
            }
        }

        if (key_exists('city', $addressArr)) {
            $city = new FHIRString();
            $city->setValue($addressArr['city']);
            $FHIRAddress->setCity($city);
            $flag = true;
        }
        if (key_exists('district', $addressArr)) {
            $district = new FHIRString();
            $district->setValue($addressArr['district']);
            $FHIRAddress->setDistrict($district);
            $flag = true;
        }
        if (key_exists('state', $addressArr)) {
            $state = new FHIRString();
            $state->setValue($addressArr['state']);
            $FHIRAddress->setState($state);
            $flag = true;
        }
        if (key_exists('postalCode', $addressArr)) {
            $postalCode = new FHIRString();
            $postalCode->setValue($addressArr['postalCode']);
            $FHIRAddress->setPostalCode($postalCode);
            $flag = true;
        }
        if (key_exists('country', $addressArr)) {
            $country = new FHIRString();
            $country->setValue($addressArr['country']);
            $FHIRAddress->setCountry($country);
            $flag = true;
        }

        if ($flag) {
            return $FHIRAddress;
        } else {
            return null;
        }
    }


}
