<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   Anton Gribanov anton dot gribanov at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cloudradar_CloudRadarCheckboxDecorator extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        return "<div id='" . $this->getOption('id', '') . "-form-row' class='form-row'>" . $content . "
<div class='field-errors' style='display: none'></div>    
</div>";
    }
}