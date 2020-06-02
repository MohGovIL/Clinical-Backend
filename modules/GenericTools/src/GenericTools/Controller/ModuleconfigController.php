<?php

namespace GenericTools\Controller;

use Laminas\View\Model\ViewModel;

class ModuleconfigController
{

    public function getHookConfig()
    {
        //NAME KEY SPECIFIES THE NAME OF THE HOOK FOR UNIQUELY IDENTIFYING IN A MODULE.
        //TITLE KEY SPECIFIES THE TITLE OF THE HOOK TO BE DISPLAYED IN THE CALLING PAGES.
        //PATH KEY SPECIFIES THE PATH OF THE RESOURCE, SHOULD SPECIFY THE CONTROLLER
        //AND IT’S ACTION IN THE PATH, (INCLUDING INDEX ACTION)
        $hooks = array (
            // hook for patient screen low security
            array (
                'name' => "zf2_module_skeleton",
                'title' => "PUT NAME HERE",
                'path' => "public/zf2-module-skeleton",
            ),

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
        $acl = array(
                array (
                    'section_id' => "zf2_module_skeleton",
                    'section_name' => "zf2-module-skeleton",
                    'parent_section' => "",
                ),
            array (
                'section_id' => "zf2_module_skeleton_screen",
                'section_name' => "zf2-module-skeleton",
                'parent_section' => "zf2_module_skeleton",
                )
            );
            return $acl;

      }

}
