<?php
/********************************************
 * Cagent monitoring Plugin for Plesk Panel
 * @Author:   Artur Troian troian dot ap at gmail dot com
 * @Author:   cloudradar GmbH
 * @Copyright: (c) 2019
 *********************************************/

class Modules_Cagent_View
{
    public $icons;
    private $bufs = array();
    private $_parentview;

    public function __construct($view)
    {
        define('SUBMIT', 'submit');
        $this->_parentview = $view;
        define('MODULE_URL', pm_Context::getBaseUrl());

        $this->icons = array(
            'm_logo_cagent' => 'images/cloudradar_logo.svg',
            'ico_info' => '/theme/icons/32/plesk/server-info.png',
            'ico_error' => '/theme/icons/32/plesk/file-error.png',
            'ico_warning' => '/theme/icons/32/plesk/file-alert.png',
            'm_server_install' => '/theme/icons/32/plesk/web-servers.png',
            'm_server_uninstall' => '/theme/icons/32/plesk/file-delete.png',
        );
    }

    public function dispatch()
    {
        echo implode("\n", $this->bufs);
    }

    public function PageHeader($do)
    {
        $buf = <<<EEN
			<div class="formArea"><form name="cagentform"><input type="hidden" name="step" value="1"/><input type="hidden" name="do" value="$do"/>
			<div class="form-box">
EEN;
        $this->bufs[] = $buf;
    }

    public function PageFooter()
    {
        $this->bufs[] = "</div></form></div>";
    }

    public function ModuleError($step)
    {
        $buf = $this->screen_title('Complete Cagent Extension Installation');
        $script = PSA_BASE . '/admin/plib/modules/cagent/scripts/installer';

        if ($step == 1) {
            $err = "<pre>For security reasons, please login as root user via ssh to manually run the following command:

	sh $script

If successful, remove the installation script:

	rm $script	</pre>";
            $title = 'Please manually run the script to finish installation';
        }
        elseif ($step == 2) {
            $err = "Please remove the install script before using this extension <br/> rm $script \n";
            $title = 'Install script needs to be removed after installation.';
        }
        else {
            $err = 'Please download and reinstall the extension from zip file.';
            $title = 'Module is not installed properly';
        }
        $buf .= $this->error_panel_mesg($title, $err);
        $this->bufs[] = $buf;
    }

    private function tool_list($list)
    {
        $buf = '<div class="content-area"><div class="content-wrapper"><ul class="tools-list">';
        foreach ($list as $li) {
            $buf .= '<li class="tools-item"><div class="tool-block">'
                . '<span class="tool-icon">';
            if ( $li['icon'] != '')
                $buf .= '<img src="' . $li['icon'] . '"></img>';

            $buf .= '</span><span class="tool-name">' . $li['name']
                . '</span><span class="tool-info">' . $li['info']
                . '</span></li>';
        }

        $buf .= '</ul></div></div>' . "\n";

        return $buf;
    }

    public function MainMenu($info)
    {
        $this->screen_title("Cagent Extension", FALSE);

        $buf = '<div id="heading" style="color: #333; font-size: 21px; padding: 10px 20px;">'
            . '<div style="text-align: center;"><img style="width: 420px;" '
            . 'alt="Cagent" src="' . $this->icons['m_logo_cagent']
            . '"/ onclick="window.open(\'https://cloudradar.io\')" ></div></div>';

        if ($info['cagent_installed']['code'] == 0) {
            $buf .= $this->show_config_status($info['cagent_configured']);
            $buf .= $this->show_running_status($info['cagent_running']);
        } else {
            $buf .= $this->section_title('Install Cagent monitoring tool');
            $list = array();

            $ver = 'Download and install the latest stable release: ';
            $ver .= $info['cagent_latest']['version'];
            $li_version = array(
                'icon' => $this->icons['m_server_install'],
                'name' => '<a href="?do=install">Install</a> Cagent',
                'info' => $ver);
            $list[] = $li_version;

            $buf .= $this->tool_list($list);
        }

        $buf .= '<div id="main" class="clearfix">';

        $buf .= '<p></p>
<p style="margin-top:30px;color:#a0a0a0;text-align:right;font-size:11px">This extension is developed by cloudradar GmbH. Plesk is not responsible for
support.<br/>Please contact Cloudradar at https://cloudradar.io for all related questions and issues.<br/><br/>Cagent Monitoring Extension for Plesk v'
            . MODULE_VERSION . ' </p>

</div>';

        $this->bufs[] = $buf;
    }

    public function InstallPrepare($info) {
        $buf = $this->screen_title('Installing Cagent');

        $buf .= $this->section_title('Cagent configuration');

        $input = $this->input_text('hub_url', $info['hub_url']);
        $buf .= $this->form_row('Hub URL:', $input, $info['error']['hub_url']);

        $input = $this->input_text('hub_user', $info['hub_user']);
        $buf .= $this->form_row('Hub User:', $input, $info['error']['hub_user']);

        $input = $this->input_password('hub_password', $info['hub_password']);
        $buf .= $this->form_row('Hub Password:', $input, $info['error']['hub_password']);

        $buf .= $this->button_panel_cancel_next('Cancel', 'Install');

        $this->bufs[] = $buf;
    }

    public function Install($info) {
        $buf = $this->screen_title('Install Cagent');

        if ($info['return'] != 0) {
            $title = 'Error when installing Cagent';
            $buf .= $this->error_panel_mesg($title, $info['output']);
        }
        else {
            $buf .= $this->info_mesg('Cagent installed successfully');
            $buf .= $this->show_running_status($info['cagent_running']);
        }

        $buf .= $this->button_panel_back('OK');
        $this->bufs[] = $buf;
    }

    private function show_running_status($info)
    {
        if ($info['code'] == 0) {
            $info['stdout'] = str_replace(PHP_EOL, '', $info['stdout']);
            if ($info['stdout'] == 'running') {
                $output = $this->info_mesg('cagent is running');
            } else {
                $msg = 'cagent is not running. status: ';
                $msg .= $info['stdout'];
                $output = $this->warning_mesg($msg);
            }
        } else {
            $msg = 'cagent get status: ';
            $msg .= $info['stderr'];
            $output = $this->error_mesg($msg);
        }

        return $output;
    }

    private function show_config_status($info)
    {
        if ($info['code'] == 0) {
            $output = $this->info_mesg('cagent is configured');
        } else {
            $output = $this->error_mesg($info['stdout']);
        }

        return $output;
    }

    private function screen_title($title, $uplinkself=TRUE)
    {
        $this->_parentview->pageTitle = $title;
        if ($uplinkself)
            $this->_parentview->uplevelLink = MODULE_URL;
        return '';
    }

    private function section_title($title)
    {
        //$div = '<div class="title"><div class="title-area" style="margin:15px 0 10px 0"><h3>' . $title . '</h3></div></div>' . "\n";
        $div = "<div style=\"margin-top:10px\"><fieldset><legend>$title</legend></fieldset></div>\n";
        return $div;
    }

    private function input_text($name, $value, $size_class=0)
    {
        //size 0 : default, size 1: f-middle-size, 2: long
        $iclass = 'input-text';
        if ($size_class == 1)
            $iclass = 'f-middle-size ' . $iclass;
        elseif ($size_class == 2)
            $iclass = '" size="90';
        $input = '<input type="text" class="' . $iclass . '" name="' . $name . '" value="'. $value . '"/>';
        return $input;
    }

    private function input_select($name, $options, $default)
    {
        $input = '<select name="' . $name . '">';
        foreach ($options as $key => $val) {
            $input .= '<option value="' . $key . '" label="' . $val . '"';
            if ($default == $key)
                $input .= ' selected="selected"';
            $input .= '>' . $val . '</option>';
        }
        $input .= '</select>';
        return $input;
    }

    private function input_password($name, $value)
    {
        $input = '<input type="password" name="' . $name . '" value="' . $value . '"/>';
        return $input;
    }

    private function input_checkbox($name, $value, $ischecked)
    {
        $checked = $ischecked ? 'checked="checked"' : '';
        $input = '<input type="checkbox" class="checkbox" name="' . $name . '" value="' . $value . '"'. " $checked />";
        return $input;
    }

    private function input_radio($name, $value, $ischecked)
    {
        $checked = $ischecked ? 'checked="checked"' : '';
        $input = '<input type="radio" class="radiobox" name="' . $name . '" value="' . $value . '"'. " $checked />";
        return $input;
    }

    private function input_hidden($name, $value)
    {
        $input = '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';
        return $input;
    }

    private function form_row($label, $field, $err, $hints=NULL, $is_single=FALSE)
    {
        $divclass = 'form-row';
        $errspan = '';
        $hintspan = '';
        if ($err != NULL) {
            $divclass .= ' error';
            $errspan = '<span class="error-hint">' . $err . '</span>';
        }
        if ($hints != NULL) {
            if (is_array($hints))
                $hintspan = '<span class="hint">' . implode('<br>', $hints) . '</span>';
            else
                $hintspan = '<span class="hint">' . $hints . '</span>';
        }
        $div = '<div class="' . $divclass . '">';

        if ($is_single) {
            $div .= '<div class="single-row">' . $field . "<label>&nbsp;$label</label>";
        }
        else {
            $div .= '<div class="field-name"><label>' . $label . '&nbsp;</label></div><div class="field-value">' . $field;
        }
        $div .= $errspan . $hintspan . '</div></div>' . "\n";

        return $div;
    }

    private function div_msg_box($mesg, $subtype='')
    {
        $style = 'msg-box';
        if ($subtype != '')
            $style .= " $subtype";

        $div = '<div class="' . $style . '"><div class="msg-content">';
        if (is_array($mesg)) {
            $div .= '<ul><li>';
            $div .= implode('</li><li>', $mesg);
            $div .= '</li></ul>';
        }
        else
            $div .= $mesg;

        $div .= '</div></div>';
        return $div;
    }

    private function info_mesg($mesg)
    {
        return $this->div_msg_box($mesg, 'msg-info');
    }

    private function error_mesg($mesg)
    {
        return $this->div_msg_box($mesg, 'msg-error');
    }

    private function warning_mesg($mesg)
    {
        return $this->div_msg_box($mesg, 'msg-warning');
    }

    private function info_panel_mesg($title, $mesg)
    {
        return $this->div_mesg_panel($title, $mesg, $this->icons['ico_info']);
    }

    private function error_panel_mesg($title, $mesg)
    {
        return $this->div_mesg_panel($title, $mesg, $this->icons['ico_error']);
    }

    private function warning_panel_mesg($title, $mesg)
    {
        return $this->div_mesg_panel($title, $mesg, $this->icons['ico_warning']);
    }

    private function div_mesg_panel($title, $mesg, $icon)
    {
        $box = '<div class="p-box"><div class="p-box-content">';
        if ($title != NULL) {
            $box .= '<div class="title"><div class="title-area"><h4><img src="' . $icon . '" alt=""></img> ' . $title . '</h4><p></p></div></div>';
        }
        $box .= '<div class="content"><div class="content-area"><p>';

        if (is_array($mesg))
            $box .= implode('</p><p>', $mesg);
        else
            $box .= $mesg;

        $box .= '</p></div></div></div></div>' . "\n";
        return $box;
    }

    private function script_button($url, $name, $title, $disabled = 'false')
    {
        if ($url != SUBMIT) {
            $buf = '<span class="btn" onclick="window.location.href=\'' . $url . '\'"><button type="button" value="" name="' .
                $name . '">' . $title . '</button></span>';
        }
        else {
            $buf = '<span id="btn-' . $name . '" class="btn action"><button type="button" value="" name="'
                . $name . '" onclick="Jsw.submit(this)">' . $title . '</button></span>';
        }

        return $buf;
    }

    private function button_panel_cancel_next($cancel_title, $next_title)
    {
        $buf = '<div class="btns-box"><div class="box-area"><div class="form-row"><div class="field-name"> </div>';
        if ($cancel_title != NULL)
            $buf .= $this->script_button(MODULE_URL, 'cancel', $cancel_title);
        if ($next_title != NULL)
            $buf .= $this->script_button(SUBMIT, 'next', $next_title);
        $buf .= '</div></div></div>';
        return $buf;
    }

    private function button_panel_back($back_title)
    {
        $buf = '<div class="btns-box"><div class="box-area"><div class="form-row"><div class="field-name"> </div>';
        $buf .= $this->script_button(MODULE_URL, 'back', $back_title);
        $buf .= '</div></div></div>';
        return $buf;
    }
}