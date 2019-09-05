//@ sourceURL=project_list.js
$(document).ready(function () {
    var project_hot_table;
    loadModule(['dataTable'], function () {
        var ProjectHotTbl = $('#project_hot');
        project_hot_table = ProjectHotTbl.DataTable($.extend(true,{}, dtAutoOption(ProjectHotTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[3, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#project_hot_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(project_hot_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "pro_id"},
                { data: "pro_name" },
                { data: "stock_code" },
                { data: "list_order" },
                { data: "status" },
                { data: "pro_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(5)', nRow).html('<a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#ModalOrder">设置排序</a>');
                if (aData.status == 2) {
                    $('td:eq(4)', nRow).html("融资中");
                }else{
                    $('td:eq(4)', nRow).html('-');
                }
                return nRow;
            }
        }));
        $("#project_hot_btn").click(function(){project_hot_table.ajax.reload(null, false)});
    });
});
$('#ModalOrder').on('show.bs.modal', function (e) {
    var list_order = $(e.relatedTarget).parents("tr").find("td:eq(3)").text();
    var id = $(e.relatedTarget).parents("tr").find("td:eq(0)").text();
    $("#ModalOrder").find("input[name='list_order']").val(list_order);
    $("#ModalOrder").find("input[name='id']").val(id);
    loadModule('bootstrapValidator', function () {
        $('#set_list_order_form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                list_order: {
                    validators: {
                        notEmpty: {
                            message:'请输入排序号'
                        },
                        regexp: {
                            regexp: /^\d+$/,
                            message: '请输入有效排序号'
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
    $('#set_list_order_form').bootstrapValidator('destroy');
    $('#set_list_order_form')[0].reset();
});