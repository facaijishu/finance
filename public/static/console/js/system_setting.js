//@ sourceURL=system_setting.js
$(document).ready(function () {
    var contract_terms_table;
    loadModule(['bootstrapValidator'], function () {
        $('#companySetting')
            .bootstrapValidator({
                feedbackIcons : {
                    valid : 'glyphicon glyphicon-ok',
                    invalid : 'glyphicon glyphicon-remove',
                    validating : 'glyphicon glyphicon-refresh'
                }
            })
            .on('success.form.bv', function(e) {
                e.preventDefault();
                var $form = $(e.target);
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (resp) {
                        if(resp.code){
                            Dialog.success('操作提示', resp.msg, 3000, function () {
                                loadURL(resp.data.url);
                            });
                        }else{
                            Dialog.error('操作提示', resp.msg);
                        }
                    }
                });
            });
    });
});