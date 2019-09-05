loadModule(['bootstrapValidator'], function () {
    /* 修改密码 */
    var pwdTpl = $("#change_pwd_tpl").html();
    $("#change-pwd").click(function () {
        showPwdModal({});
    });
    var showPwdModal = function (data) {
        var html = juicer(pwdTpl, data);
        var modal = Dialog.modal({
            title: "修改密码",
            content: html,
            buttons: {
                "取 消": {
                    icon: "glyphicon glyphicon-remove",
                    close: true
                },
                "确 定": {
                    icon: "glyphicon glyphicon-ok",
                    'class': "btn-primary",
                    callback: function(){modal.find("form").submit();}
                }
            }
        });
        var form = $(".change_pwd_tpl");
        form.bootstrapValidator({
            fields: {
                old_pwd : {
                    validators : {
                        notEmpty : {
                            message : '请输入旧密码'
                        },
                    }
                },
                new_pwd : {
                    validators : {
                        notEmpty : {
                            message : '请输入新密码'
                        },
                    }
                },
                pwd_confirm : {
                    validators : {
                        notEmpty : {
                            message : '请输入确认密码'
                        },
                    }
                }
            }

        }).on("success.form.bv", function(e){
            e.preventDefault();
            var $form = $(e.target);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (resp) {
                    if(resp.code){
                        Dialog.success('提示信息', resp.msg, 3000, function () {
                            modal.modal("hide");
                            window.location.href = resp.data.url;
                        });
                    }else{
                        Dialog.error('提示信息', resp.msg);
                    }
                }
            });
            return false;
        });
    }
    /* 修改密码 */
});