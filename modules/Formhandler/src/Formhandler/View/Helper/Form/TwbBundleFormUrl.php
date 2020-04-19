<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Formhandler\View\Helper\Form;

use Traversable;
use InvalidArgumentException;
use LogicException;

use Zend\Form\Element\Collection;
use Zend\Form\Factory;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\Translator\Translator;
use Formhandler\Options\ModuleOptions;
use Zend\Form\Element\Button;
use Zend\Form\View\Helper\FormUrl;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\Exception;

class TwbBundleFormUrl extends AbstractHelper
{

    /**
     * Attributes valid for the input tag
     *
     * @var array
     */
    protected $validTagAttributes = [
        'name'           => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'list'           => true,
        'maxlength'      => true,
        'pattern'        => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'size'           => true,
        'type'           => true,
        'value'          => true,
        'href'           => true,
        'apicall'        => true,
        'popup'          => true,
        'id'             => true,
        'class'          => true,
        'title'          => true,
        'width'          => true,
        'height'          => true,
        'css'          => true,

    ];


    public function __invoke(ElementInterface $element = null)
    {
        if (! $element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <href> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $elementOptions = $element->getOptions();

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $type                = $this->getType($element);
        $attributes['type']  = $type;
        $attributes['value'] = $element->getValue();

        $finalElement="";

        $popup=$attributes['popup'];
        $content=$attributes['content'];
        $title=$attributes['title'];
        $css=$attributes['css'];

        if ($popup==="true" && $content==="api"){

            $modalId="formmodal_".$attributes['id'];

            $finalElement.=$this->generateModalHtml($modalId,"");

            $width= (intval($attributes['width'])) ? intval($attributes['width']) : 500;
            $height=(intval($attributes['height'])) ? intval($attributes['height']) : 400;
            $href=$GLOBALS['webroot'].'/'.$attributes['href'];

            $finalElement.= '<a id="'.$attributes['id'].'" class="'.$attributes['class'].'"  name="'.$attributes['name'].'" href="#" style="'.$attributes['css'].'" onclick="dlgopen(\''.$href.'\', \'_blank\', '.$width.', '.$height.');">'.xl($title).'</a>';

            //$finalElement.= '<a id="'.$attributes['id'].'" class="'.$attributes['class'].'"  name="'.$attributes['name'].'" href="#" data-toggle="modal" data-target="#'.$modalId.'">'.xl($title).'</a>';
            return sprintf($finalElement);
        }

        $finalElement= '<a id="'.$attributes['id'].'" class="'.$attributes['class'].'" name="'.$attributes['name'].'" href="'.$attributes['href'].'" style="'.$attributes['css'].'">'.xl($title).'</a>';
        return sprintf($finalElement);
    }

    protected function renderOptions(){

    }

    /**
     *  type of url
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'url';
    }

    protected function FetchDataFromApi($RouteControllerAction){

        $RouteControllerAction=str_replace("/","\\",$RouteControllerAction);
        $arrControllerAction = explode("::",$RouteControllerAction);
        $controller = $arrControllerAction[0];
        $action = $arrControllerAction[1];
        $instance = new $controller($this->container);
        $data =  $instance->$action();
        return $data;
    }


    protected function generateModalHtml($id,$data,$title=""){
        $modalHtml=' <div class="modal fade" id="'.$id.'" role="dialog">';
        $modalHtml.='<div class="modal-dialog">';
        $modalHtml.='<div class="modal-content">';
        $modalHtml.='<div class="modal-header">';
        $modalHtml.='<button type="button" class="close" data-dismiss="modal">&times;</button>';
        $modalHtml.='<h4 class="modal-title">'.xl($title).'</h4></div>';
        $modalHtml.='<div class="modal-body">';
        $modalHtml.='<p>'.$data.'</p></div>';
        $modalHtml.='<div class="modal-footer">';
        $modalHtml.='<button type="button" class="btn btn-default" data-dismiss="modal">'.xl("close").'</button>';
        $modalHtml.='</div></div></div></div>';
        return $modalHtml;
    }




}

