<?php

use Formhandler\View\Helper\Form\TwbBundleFormUrl;
use Formhandler\View\Helper\Test;
use Formhandler\View\Helper\TwbBundleAlert;
use Formhandler\View\Helper\TwbBundleBadge;
use Formhandler\View\Helper\TwbBundleDropDown;
use Formhandler\View\Helper\TwbBundleFormButton;
use Formhandler\View\Helper\TwbBundleFormCheckbox;
use Formhandler\View\Helper\TwbBundleFormCollection;
use Formhandler\View\Helper\TwbBundleFormElementErrors;
use Formhandler\View\Helper\TwbBundleFormMultiCheckbox;
use Formhandler\View\Helper\TwbBundleFormRadio;
use Formhandler\View\Helper\TwbBundleFormRow;
use Formhandler\View\Helper\TwbBundleFormStatic;
use Formhandler\View\Helper\TwbBundleFormErrors;
use Formhandler\View\Helper\TwbBundleGlyphicon;
use Formhandler\View\Helper\TwbBundleFontAwesome;
use Formhandler\View\Helper\TwbBundleLabel;

use Laminas\Form\View\Helper\FormCheckbox;
use Laminas\Form\View\Helper\FormRadio;
use Formhandler\Controller\FormhandlerController;
use Interop\Container\ContainerInterface;
use Laminas\Form\View\Helper\FormUrl;

return array(

    /* declare all controllers */
  /*  'controllers' => array(
        'invokables' => array(
            'Formhandler\Controller\FormhandlerController' => 'Formhandler\Controller\FormhandlerController',
        ),
    ),*/
    'controllers' => array(
    'factories' => array(
        FormhandlerController::class => function (ContainerInterface $container, $requestedName) {
            return new FormhandlerController($container);
        },


    ),
    ),
    /**
     * routing configuration.
     * you can adding parameters to url [/:action][/:id] (they called in the controller with $this->params()->fromRoute('id');)
     * for more option and details - http://zf2.readthedocs.io/en/latest/in-depth-guide/understanding-routing.html?highlight=routing
     */
    'router' => array(
        'routes' => array(
            'Formhandler' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/Formhandler[/:action][/:tableName]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'tableName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        '[a-zA-Z][a-zA-Z0-9_-]*'=>'[a-zA-Z][a-zA-Z0-9_-]*',



                    ),
                    'defaults' => array(
                        'controller' => 'Formhandler\Controller\FormhandlerController',
                        'action'     => 'index',
                    ),
                ),
            ),

        ),
    ),





    'view_manager' => array(
        'template_path_stack' => array(
            'Formhandler' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'formhandler/layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        )

    ),
    'view_helpers'=>array(
        'invokables'=>array(
            'Test'=>'Formhandler\View\Helper\Test', //Alert
            //'drugandalcholtable'=>'Formhandler\View\Helper\DrugAndAlcoholUsageTable',//register new table helper

            'alert' => 'Formhandler\View\Helper\TwbBundleAlert',
            //Badge
            'badge' => 'Formhandler\View\Helper\TwbBundleBadge',
            //Button group
            'buttonGroup' => 'Formhandler\View\Helper\TwbBundleButtonGroup',
            //DropDown
            'dropDown' => 'Formhandler\View\Helper\TwbBundleDropDown',
            //Form
            'form' => 'Formhandler\View\Helper\Form\TwbBundleForm',
            'formButton' => 'Formhandler\View\Helper\Form\TwbBundleFormButton',

            'formSubmit' => 'Formhandler\View\Helper\Form\TwbBundleFormButton',
            FormCheckbox::class => \Formhandler\View\Helper\Form\TwbBundleFormCheckbox::class,
            'formCollection' => 'Formhandler\View\Helper\Form\TwbBundleFormCollection',
            'formElementErrors' => 'Formhandler\View\Helper\Form\TwbBundleFormElementErrors',
            'formMultiCheckbox' => 'Formhandler\View\Helper\Form\TwbBundleFormMultiCheckbox',
            FormRadio::class => 'Formhandler\View\Helper\Form\TwbBundleFormRadio',
            'formRow' => 'Formhandler\View\Helper\Form\TwbBundleFormRow',
            'formStatic' => 'Formhandler\View\Helper\Form\TwbBundleFormStatic',
            //Form Errors
            'formErrors' => 'Formhandler\View\Helper\Form\TwbBundleFormErrors',
            //Glyphicon
            'glyphicon' => 'Formhandler\View\Helper\TwbBundleGlyphicon',
            //FontAwesome
            'fontAwesome' => 'Formhandler\View\Helper\TwbBundleFontAwesome',
            //Label
            'label' => 'Formhandler\View\Helper\TwbBundleLabel',
            FormUrl::class => TwbBundleFormUrl::class


        ),
        /*'shared' => array(
            'Test'=>false,
            //'drugandalcholtable'=>'Formhandler\View\Helper\DrugAndAlcoholUsageTable',//register new table helper
            'alert'=>false,
            //Badge
            'badge'=>false,
            //Button group
            'buttonGroup'=>false,
            //DropDown
            'dropDown'=>false,
            //Form
            'form'=>false,
            'formButton'=>false,
            'formSubmit'=>false,
            'formCheckbox'=>false,
            'formCollection'=>false,
            'formElementErrors'=>false,
            'formMultiCheckbox'=>false,
            'formRadio'=>false,
            'formRow'=>false,
            'formStatic'=>false,
            //Form Errors
            'formErrors'=>false,
            //Glyphicon
            'glyphicon'=>false,
            //FontAwesome
            'fontAwesome'=>false,
            //Label
            'label'=>false,
        ),*/


    ),
    'factories' => array (
        'formElement' => 'Formhandler\View\Helper\Form\Factory\TwbBundleFormElementFactory',

    ),
    'twbbundle' => array (
        'ignoredViewHelpers' => array (
            'file',
            'checkbox',
            'radio',
            'submit',
            'multi_checkbox',
            'static',
            'button',
            'reset'
        ),
        'type_map' => array(),
        'class_map' => array(),
    ),

    'service_manager' => array(
        //other services can be registrated here...
        'abstract_factories' => array(
            'Laminas\Form\FormAbstractServiceFactory',
        ),
        //other services can be registrated here...
    ),


    /* register all validators*/
    'validators' => array(
        'invokables' => array(
            /*'DateFromNow' => 'Formhandler\Validator\DateFromNow'*/
        ),
    ),

);
