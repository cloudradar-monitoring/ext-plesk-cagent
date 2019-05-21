document.observe("dom:loaded",function(){
    if($("register_button")) {
        $("register_button").observe('click', function (e) {
            e.preventDefault();
            $("register_button").writeAttribute({disabled:"disabled"}).update('Please wait').up().addClassName('disabled btn-waiting');
            $$("span.field-errors").each(Element.hide);
            $$(".form-row").each(function(el){
                Element.removeClassName(el,'error');
            });

            var data = $('cagent-form').serialize(true);
            console.log('button clicked', data);
            new Ajax.Request('/modules/cloudradar/index.php/json/register', {
                method: 'post',
                parameters: data,
                onSuccess: function (response) {
                    console.log('server response', response.responseJSON);
                    $("register_button").writeAttribute({disabled:null}).update('Start free trial').up().removeClassName('disabled btn-waiting');
                    var json = response.responseJSON;
                    if(json.success){
                        $("js-registration-success-message").update(json.data.message);
                        $("cagent-form").hide();
                        $("js-registration-success").show();
                    }else{
                        for(var field in json.errors){
                            if(json.errors.hasOwnProperty(field)){
                                $(field+"-form-row").addClassName('error');
                                $$('#'+field+"-form-row .field-errors").first().update(json.errors[field]).show();
                            }
                        }
                    }
                },
                onComplete:function(){
                    $("register_button").writeAttribute({disabled:null}).update('Start free trial').up().removeClassName('disabled btn-waiting');
                }
            })
        });
    }
    if($("install_button")) {
        $("install_button").observe('click', function (e) {
            e.preventDefault();
            $("install_button").writeAttribute({disabled:"disabled"}).update('Please wait').up().addClassName('disabled btn-waiting');;
            $$("span.field-errors").each(Element.hide);
            $$(".form-row").each(function(el){
                Element.removeClassName(el,'error');
            });

            var data = $('hub-form').serialize(true);
            new Ajax.Request('/modules/cloudradar/index.php/json/install', {
                method: 'post',
                parameters: data,
                onSuccess: function (response) {
                    console.log('server response', response.responseJSON);
                    $("install_button").writeAttribute({disabled:null}).update('Install').up().removeClassName('disabled btn-waiting');
                    var json = response.responseJSON;
                    if(json.success){
                        $("js-installation-success-message").update(json.message);
                        $("hub-form").hide();
                        $("js-installation-success").show();
                    }else{
                        for(var field in json.errors){
                            if(json.errors.hasOwnProperty(field)){
                                $(field+"-form-row").addClassName('error');
                                $$('#'+field+"-form-row .field-errors").first().update(json.errors[field]).show();
                            }
                        }
                    }
                },
                onComplete:function(){
                    $("install_button").writeAttribute({disabled:null}).update('Install').up().removeClassName('disabled btn-waiting');
                }
            })
        });
    }
    if($("host_register_button")) {
        $("host_register_button").observe('click', function (e) {
            e.preventDefault();
            $("host_register_button").writeAttribute({disabled:"disabled"}).update('Please wait').up().addClassName('disabled btn-waiting');
            $$("span.field-errors").each(Element.hide);
            $$(".form-row").each(function(el){
                Element.removeClassName(el,'error');
            });

            var data = $('host-register-form').serialize(true);
            new Ajax.Request('/modules/cloudradar/index.php/json/register-host', {
                method: 'post',
                parameters: data,
                onSuccess: function (response) {
                    $("host_register_button").writeAttribute({disabled:null}).update('Register host and start monitoring').up().removeClassName('disabled btn-waiting');
                    console.log('server response', response.responseJSON);
                    var json = response.responseJSON;
                    if(json.success){
                        $("js-host-register-success-message").update(json.message);
                        $("host-register-form").hide();
                        $("js-host-register-success").show();
                    }else{
                        for(var field in json.errors){
                            if(json.errors.hasOwnProperty(field)){
                                $(field+"-form-row").addClassName('error');
                                $$('#'+field+"-form-row .field-errors").first().update(json.errors[field]).show();
                            }
                        }
                    }
                },
                onComplete:function(){
                    $("host_register_button").writeAttribute({disabled:null}).update('Register host and start monitoring').up().removeClassName('disabled btn-waiting');
                }
            })
        });
    }
});