<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir builder strategy interface
 */

namespace  FhirAPI\FhirRestApiBuilder\Parts\Strategy ;
interface Strategy
{
    public function doAlgorithm($arrParams);
}
