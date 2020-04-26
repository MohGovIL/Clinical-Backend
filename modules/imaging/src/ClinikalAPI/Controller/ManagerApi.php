<?php


namespace ClinikalAPI\Controller;


use Interop\Container\ContainerInterface;
use OpenEMR\Common\Csrf\CsrfUtils;
use GenericTools\Controller\BaseController as GenericBaseController;

class ManagerApi extends GenericBaseController
{
    public function __construct(ContainerInterface $container)
    {

    }

    public function getCsrfTokenAction()
    {
        return $this->responseWithNoLayout(
            array(
            "csrf_token" => CsrfUtils::collectCsrfToken('api'),
            "user_data" => array("user_id" => $_SESSION['authUserID'])
            )
        );
    }
}
