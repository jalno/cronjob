import * as $ from "jquery";
import "jquery.growl";
import "select2";
import "../jquery.userAutoComplete";
import "bootstrap-tagsinput";
import "webuilder/formAjax";
import "jquery.growl";
import "bootstrap-inputmsg";

export default class Managements{
	private static $form = $('body.cronjob-task form.managements');
	private static validateSchedules(schedules):void{
        $("input[name^=all][type=checkbox]", Managements.$form).prop('checked', false).trigger('change');
        const keys:string[] = ['month', 'day', 'hour', 'minute'];
        for(let i = 0; i != schedules.length; i++){
            for(let j = 0;j!=4;j++){
                if(!schedules[i].hasOwnProperty(keys[j]) || schedules[i][keys[j]] == null){
                    $("input[name=all" + keys[j] + "s]", Managements.$form).prop('checked', true).trigger('change');
                }else{
                    const $input:JQuery = $("input[name='"+keys[j]+"s[]'][value='"+schedules[i][keys[j]]+"']", Managements.$form);
                    $input.prop('checked', true).trigger('change');
                }
            }
        }
    }
	private static validateData(){
        let $val = $(this).val();
        let $option = $('option[value="' + $val + '"]', $(this));
        let $isNew = $option.data('select2-tag') || $option.data('custom');
        if($isNew){
            $("input[name='process']", Managements.$form).val("").parents(".process").slideDown();
            $("input[name='parameters']", Managements.$form).val("").parents(".parameters").slideDown();
            $("input[name^=all][type=checkbox]", Managements.$form).prop('checked', false).trigger('change');
        }else{
            $("input[name='process']", Managements.$form).val($option.data('process')).parents(".process").slideUp();
            $("input[name='parameters']", Managements.$form).val($option.data('parameters')).parents(".parameters").slideUp();
            let schedules:number[];
            if(schedules = $option.data('schedules')){
                Managements.validateSchedules(schedules);
            }
        }
    }

	private static runSelect2() {

        const isRTL = Translator.isRTL();

		$('select', Managements.$form).attr('dir', isRTL ? 'rtl' : 'ltr').select2({
			language: Translator.getActiveShortLang(),
			minimumResultsForSearch: Infinity
		});

		$('select[name=name]', Managements.$form).select2({
			tags: true,
			multiple: false,
			language: Translator.getActiveShortLang(),
		});

		$('select[name=name]', Managements.$form).on("change", Managements.validateData);
	}
	private static initalForm(){
        const $name:JQuery = $('select[name=name]', Managements.$form);
        let $val:string = $name.val() as string;
        let $option:JQuery = $('option[value="' + $val + '"]', $name);
        let $isNew:boolean = $option.data('select2-tag') || $option.data('custom');
        if (!$isNew) {
            $("input[name='process']", Managements.$form).val( $option.data('process') ).parents(".process").slideUp();
            $("input[name='parameters']", Managements.$form).val( $option.data('parameters') ).parents(".parameters").slideUp();
            let schedules:string;
            if (schedules = $option.data('schedules')) {
                Managements.validateSchedules(schedules);
            }
        }
    }
	private static runTagsInput() {
        $('input[name=tags]').tagsinput({
			trimValue: true
		});
    }
	private static runSchedulePlugin(){
        $("input[type=checkbox]", Managements.$form).on('change click', function(){
            const $label:JQuery = $(this).parent();
            const isAll:boolean = ($(this).val() == 'all');
            if($(this).prop('checked')){
                $label.addClass("badge");
                if(isAll){
                    $label.addClass("badge-success");
                    $("input[type=checkbox][data-type='"+$(this).data('type')+"']", Managements.$form).not(this).prop('checked', true).trigger('change');
                }else{
                    switch($(this).data('type')){
                        case('hours'):
                            $label.addClass("badge-info");
                            break;
                        case('days'):
                            $label.addClass("badge-danger");
                            break;
                        case('months'):
                            $label.addClass("badge-inverse");
                            break;
                        case('minutes'):
                            $label.addClass("badge-warning");
                            break;
                    }
                }
            }else{
                $label.removeClass("badge");
                switch($(this).data('type')){
                    case('hours'):
                        $label.removeClass("badge-info");
                        break;
                    case('days'):
                        $label.removeClass("badge-danger");
                        break;
                    case('months'):
                        $label.removeClass("badge-inverse");
                        break;
                    case('minutes'):
                        $label.removeClass("badge-warning");
                        break;
                }
                if(isAll){
                    $label.removeClass("badge-success");
                    $("input[type=checkbox][data-type='"+$(this).data('type')+"']", Managements.$form).not(this).prop('checked', false).trigger('change');                    
                }
            }
        });
        $("input[type=checkbox]:checked", Managements.$form).trigger('change');
	}
	private static runSubmitFormListener() {
		Managements.$form.on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				data: new FormData(this as HTMLFormElement),
				contentType: false,
				processData: false,
				success: (response) => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
					if (response.hasOwnProperty("redirect")) {
						window.location.href = response.redirect;
					}
				},
				error: function(response){
					if(response.error == 'data_duplicate' || response.error == 'data_validation'){
						let $input = $('[name='+response.input+']');
						let $params = {
							title: t("error.fatal.title"),
							message: '',
						};
						if (response.error == 'data_validation') {
							$params.message = t(response.error);
						}
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
					}else{
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("userpanel.formajax.error"),
						});
					}
				}
			});
		});
	}
	public static init(){
		Managements.runSelect2();
		Managements.initalForm();
		Managements.runTagsInput();
		Managements.runSchedulePlugin();
		Managements.runSubmitFormListener();
	}
	public static initIfNeeded(){
		if(Managements.$form.length){
			Managements.init();
		}
	}
}