//@ sourceURL=project_list.js
$(document).ready(function () {
    var customer_service_table;
    loadModule(['dataTable'], function () {
        var customerServiceTbl = $('#customer_service');
        customer_service_table = customerServiceTbl.DataTable($.extend(true,{}, dtAutoOption(customerServiceTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r>t<'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'pl>>",
            ordering: true,
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#customer_service_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(customer_service_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "cs_id"},
                { data: "kf_account" },
                { data: "uid" },
                { data: "kf_headimgurl" },
                { data: "kf_nick" },
                { data: "kf_wx" },
                { data: "kf_qr_code" },
                { data: "createTime" },
                { data: "status" },
                { data: "cs_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.status == 1){
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-green'>正常</div>");
                }else{
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-blue'>不可用</div>");
                }
                $('td:eq(3)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/'+aData.kf_headimgurl+'" data-toggle="modal" data-target="#ModalImg">');
                $('td:eq(6)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/'+aData.kf_qr_code+'" data-toggle="modal" data-target="#ModalImg">');
                $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.cs_id + '"><i class="fa fa-edit"></i>修改</a><a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-id="' + aData.cs_id + '">删除</a>');
                if(aData.kf_wx == ''){
                    $('td:eq(9)', nRow).append('<a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#ModalBinding">邀请绑定</a>');
                }
                return nRow;
            }
        }));
        $("#customer_service_btn").click(function(){customer_service_table.ajax.reload(null, false)});
        customerServiceTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'edit'){
                $.ajax({
                    url:customer_service_judge_url,
                    data:{id:id,model:'CustomerService',action:'edit'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$('#ModalBinding').on('show.bs.modal', function (e) {
    loadModule('bootstrapValidator', function () {
        $('#binding_customer_form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                kf_wx: {
                    validators: {
                        notEmpty: {
                            message:'请输入微信号'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (resp) {
                    if (resp.code) {
                        Dialog.success('操作成功', resp.msg, 3000, function () {
                            loadURL(resp.data.url);
                        });
                    } else {
                        Dialog.error('操作失败', resp.msg);
                    }
                }
            });
        });
    });
}).on('hidden.bs.modal', function (e) {
    $('#binding_customer_form').bootstrapValidator('destroy');
    $('#binding_customer_form')[0].reset();
});
$(".synchronization").click(function(){
    Dialog.confirm('操作提示', '确定同步客服信息吗？', function () {
        $.ajax({
            url: customer_service_synchronization_url,
            type: 'post',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});
$("#customer_service").on('click','.btn-danger',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定删除该客服吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: customer_service_delete_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});