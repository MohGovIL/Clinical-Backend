<?php

namespace Inheritance\Controller;

use Zend\View\Model\ViewModel;

/**
 * This is is the configuration for the openemr module installer.
 * here we adding the openemr hooks and the Acl (permission).
 * alse we put here the path to css and js file (now it's in zend public folder but in could change).
 * */
class ModuleconfigController
{
    /* base path for js file in public folder */
    const JS_BASE_PATH = '/js';
    const CSS_BASE_PATH = '/css';

    public function getHookConfig()
    {
        //NAME KEY SPECIFIES THE NAME OF THE HOOK FOR UNIQUELY IDENTIFYING IN A MODULE.
        //TITLE KEY SPECIFIES THE TITLE OF THE HOOK TO BE DISPLAYED IN THE CALLING PAGES.
        //PATH KEY SPECIFIES THE PATH OF THE RESOURCE, SHOULD SPECIFY THE CONTROLLER
        //AND IT’S ACTION IN THE PATH, (INCLUDING INDEX ACTION)

        //EXAMPLES!!
        $hooks = array (
            // hook for patient screen low security
            array (
                'name' => "inheritance",
                'title' => "Inheritance",
                'path' => "public/inheritance",
            )
        );
        return $hooks;
    }

    public function getDependedModulesConfig()
    {
        return array();
    }

    public function getAclConfig()
    {
        //new acl rule for disallow using in the General setting screen
        //EXAMPLES!!
        $acl = array(
            array (
            'section_id' => "inheritance",
            'section_name' => "Inheritance",
            'parent_section' => "",
            )
        );
        return $acl;

      }

}