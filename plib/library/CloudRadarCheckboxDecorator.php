<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 04.04.2019
 * Time: 14:56
 */

class Modules_Cagent_CloudRadarCheckboxDecorator extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        return "<div id='" . $this->getOption('id', '') . "-form-row' class='form-row'>" . $content . "
<div class='field-errors' style='display: none'></div>    
</div>";
    }
}