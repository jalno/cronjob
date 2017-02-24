var TasksEvents = function(){
    var $form = $(".create_form");
    var runSelect2 = function() {
        $('select', $form).attr('dir', 'rtl').select2({
            language: 'fa',
            minimumResultsForSearch: 'Infinity'
        });
        $('select[name=name]', $form).attr('dir', 'rtl').select2({
            tags: true,
            multiple: false,
            language: 'fa'
        });
        $('select[name=name]', $form).on("change", validateData);
    }
    var validateSchedules = function(schedules){
        $("input[name^=all][type=checkbox]", $form).prop('checked', false).trigger('change');
        var keys = ['month', 'day', 'hour', 'minute'];
        for(var i = 0;i!=schedules.length;i++){
            for(var j = 0;j!=4;j++){
                if(!schedules[i].hasOwnProperty(keys[j]) || schedules[i][keys[j]]== null){
                    $("input[name=all"+keys[j]+"s]", $form).prop('checked', true).trigger('change');
                }else{
                    var $input = $("input[name='"+keys[j]+"s[]'][value='"+schedules[i][keys[j]]+"']", $form);
                    $input.prop('checked', true).trigger('change');
                }
            }
        }
    }
    var validateData = function() {
        var $val = $(this).val();
        var $option = $('option[value="'+$val+'"]', this);
        var $isNew = $option.data('select2-tag') || $option.data('custom');
        if($isNew){
            $("input[name='process']", $form).val("").parents(".process").slideDown();
            $("input[name='parameters']", $form).val("").parents(".parameters").slideDown();
            $("input[name^=all][type=checkbox]", $form).prop('checked', false).trigger('change');
        }else{
            $("input[name='process']", $form).val($option.data('process')).parents(".process").slideUp();
            $("input[name='parameters']", $form).val($option.data('parameters')).parents(".parameters").slideUp();
            var schedules;
            if(schedules = $option.data('schedules')){
                validateSchedules(schedules);
            }
        }
    }
    var initalForm = function(){
        var $name = $('select[name=name]', $form);
        var $val = $name.val();
        var $option = $('option[value="' + $val + '"]', $name);
        var $isNew = $option.data('select2-tag') || $option.data('custom');
        if (!$isNew) {
            $("input[name='process']", $form).val($option.data('process')).parents(".process").slideUp();
            $("input[name='parameters']", $form).val($option.data('parameters')).parents(".parameters").slideUp();
            var schedules;
            if (schedules = $option.data('schedules')) {
                validateSchedules(schedules);
            }
        }
    }
    var runTagsInput = function () {
        $("input[name=parameters]").tagsInput({
            width: 'auto',
            height: '210px'
        });
    };
    var MyDatePlugin = function(){
        $("input[type=checkbox]", $form).on('change click', function(){
            var $label = $(this).parent();
            var isAll = $(this).val() == 'all';
            if($(this).prop('checked')){
                $label.addClass("badge");
                if(isAll){
                    $label.addClass("badge-success");
                    $("input[type=checkbox][data-type='"+$(this).data('type')+"']", $form).not(this).prop('checked', true).trigger('change');
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
                        case('weekdays'):
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
                    case('weekdays'):
                        $label.removeClass("badge-warning");
                        break;
                }
                if(isAll){
                    $label.removeClass("badge-success");
                    $("input[type=checkbox][data-type='"+$(this).data('type')+"']", $form).not(this).prop('checked', false).trigger('change');                    
                }
            }
        });
        $("input[type=checkbox]:checked", $form).trigger('change');
        
    }
    return {
        init: function(){
            runTagsInput();
            runSelect2();
            MyDatePlugin();
            initalForm();
        }
    }
}();
$(function(){
    TasksEvents.init();
});