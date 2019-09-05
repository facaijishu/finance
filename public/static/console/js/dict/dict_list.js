//@ sourceURL=dict_type_list.js
$(document).ready(function () {
    var dict_table;
    loadModule(['dataTable'], function () {
        var dictTbl = $('#dict');
        dict_table = dictTbl.DataTable($.extend(true, {}, dtAutoOption(dictTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'pl>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[5, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#dict_form").serializeArray();
                if (query_form.length > 0) {
                    for (var i in query_form) {
                        if ('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(dict_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "id"},
                {data: "value"},
                {data: "dt_id"},
                {data: "create_time"},
                {data: "create_uid"},
                {data: "status"},
                {data: "dt_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if (aData.status == 1) {
                    $('td:eq(5)', nRow).html('<div class="badge bg-color-green">显示</div>');
                    if(aData.des == 'notice'){
                        $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale disabled">停用</a>');
                    }else{
                        $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                    }
                }else{
                    $('td:eq(5)', nRow).html('<div class="badge bg-color-red">隐藏</div>');
                    if(aData.des != ''){
                        $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale disabled">开启</a>');
                    }else{
                        $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">开启</a>');
                    }
                }
                if(aData.des == 'notice'){
                    $('td:eq(6)', nRow).append('<a class="btn btn-xs btn-warning cale disabled" data-toggle="modal" data-target="#editModalData" data-id="' + aData.id + '"><i class="fa fa-edit"></i>修改</a>');
                }else{
                    $('td:eq(6)', nRow).append('<a class="btn btn-xs btn-warning cale" data-toggle="modal" data-target="#editModalData" data-id="' + aData.id + '"><i class="fa fa-edit"></i>修改</a>');
                }
                
                return nRow;
            }
        }));
        $("#dict_btn").click(function () {
            dict_table.ajax.reload(null, false)
        });
    });
});
$("#dict").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该数据的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: dict_stop_url,
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
$("#dict").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该数据的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: dict_start_url,
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
$('#addModalData').on('show.bs.modal', function (e) {
    loadModule('bootstrapValidator', function () {
        $('#add_dict_form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                value: {
                    validators: {
                        notEmpty: {
                            message:'请输入数据名称'
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
    $('#add_dict_form').bootstrapValidator('destroy');
    $('#add_dict_form')[0].reset();
});
$('#editModalData').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).attr("data-id");
    $.ajax({
        url:dict_find_url,
        data:{"id":id},
        type:"POST",
        dataType:"json",
        success:function(resp){
            if (resp.code) {
                $("#edit_dict_form").find("option[value='"+resp.data.dt_id+"']").attr({"selected":"true"});
                $("#edit_dict_form").find("input[name='value']").val(resp.data.value);
                $("#edit_dict_form").find("input[name='list_order']").val(resp.data.list_order);
                $("#edit_dict_form").find("textarea").val(resp.data.des);
                $("#edit_dict_form").find(".id").val(resp.data.id);
            } else {
                Dialog.error('搜索数据失败', resp.msg);
            }
        }
    })
    $('#edit_dict_form').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            value: {
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
    $('#edit_dict_form').bootstrapValidator('destroy');
    $('#edit_dict_form')[0].reset();
});