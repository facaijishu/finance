//@ sourceURL=dict_type_list.js
$(document).ready(function () {
    var dict_type_table;
    loadModule(['dataTable'], function () {
        var dict_typeTbl = $('#dict_type');
        dict_type_table = dict_typeTbl.DataTable($.extend(true, {}, dtAutoOption(dict_typeTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[4, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#dict_type_form").serializeArray();
                if (query_form.length > 0) {
                    for (var i in query_form) {
                        if ('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(dict_type_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "dt_id"},
                {data: "name"},
                {data: "create_time"},
                {data: "create_uid"},
                {data: "status"},
                {data: "dt_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if (aData.status == 1) {
                    $('td:eq(4)', nRow).html('<div class="badge bg-color-green">显示</div>');
                    $('td:eq(5)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.dt_id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                }else{
                    $('td:eq(4)', nRow).html('<div class="badge bg-color-red">隐藏</div>');
                    $('td:eq(5)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.dt_id + '" class="btn btn-xs btn-primary js-start cale">开启</a>');
                }
                $('td:eq(5)', nRow).append('<a class="btn btn-xs btn-warning cale" data-toggle="modal" data-target="#editModalType" data-id="' + aData.dt_id + '"><i class="fa fa-edit"></i>修改</a>');
                return nRow;
            }
        }));
        $("#dict_type_btn").click(function () {
            dict_type_table.ajax.reload(null, false)
        });
    });
});
$("#dict_type").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该类型的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: dict_type_stop_url,
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
$("#dict_type").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该类型的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: dict_type_start_url,
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
$('#addModalType').on('show.bs.modal', function (e) {
    loadModule('bootstrapValidator', function () {
        $('#add_dict_type_form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: {
                    validators: {
                        notEmpty: {
                            message:'请输入数据类型名称'
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
    $('#add_dict_type_form').bootstrapValidator('destroy');
    $('#add_dict_type_form')[0].reset();
});
$('#editModalType').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).attr("data-id");
    $.ajax({
        url:dict_type_find_url,
        data:{"id":id},
        type:"POST",
        dataType:"json",
        success:function(resp){
            if (resp.code) {
                $("#edit_dict_type_form").find("option[value='"+resp.data.type+"']").attr({"selected":"true"});
                $("#edit_dict_type_form").find("input[name='name']").val(resp.data.name);
                $("#edit_dict_type_form").find(".dt_id").val(resp.data.dt_id);
            } else {
                Dialog.error('搜索数据失败', resp.msg);
            }
        }
    })
    $('#edit_dict_type_form').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: '请输入数据类型名称'
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
}).on('hidden.bs.modal', function (e) {
    $('#edit_dict_type_form').bootstrapValidator('destroy');
    $('#edit_dict_type_form')[0].reset();
});