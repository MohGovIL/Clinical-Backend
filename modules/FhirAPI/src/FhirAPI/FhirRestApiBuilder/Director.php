<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir DIRECTOR OF BUILDING THE RESTFUL API
 */

namespace FhirAPI\FhirRestApiBuilder;



use FhirAPI\FhirRestApiBuilder\Builders\Builder;

class Director
{
    public function build(Builder $builder) {
        $builder->addRoutes();
        $builder->addFunctionalityToRoutingMapping();
        $builder->addErrorCodes();
        $builder->addSearchParams();
        return "getRestfulApi()";
    }
}
