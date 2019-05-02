document.observe("dom:loaded",function(){
    if($("register_button")) {
        $("register_button").observe('click', function (e) {
            e.preventDefault();
            $$("span.field-errors").each(Element.hide);
            $$(".form-row").each(function(el){
                Element.removeClassName(el,'error');
            });

            var data = $('cagent-form').serialize(true);
            console.log('button clicked', data);
            new Ajax.Request('/modules/cagent/index.php/json/register', {
                method: 'post',
                parameters: data,
                onSuccess: function (response) {
                    console.log('server response', response.responseJSON);
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
                }
            })
        });
    }
    if($("install_button")) {
        $("install_button").observe('click', function (e) {
            e.preventDefault();
            $$("span.field-errors").each(Element.hide);
            $$(".form-row").each(function(el){
                Element.removeClassName(el,'error');
            });

            var data = $('hub-form').serialize(true);
            new Ajax.Request('/modules/cagent/index.php/json/install', {
                method: 'post',
                parameters: data,
                onSuccess: function (response) {
                    console.log('server response', response.responseJSON);
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
                }
            })
        });
    }
});