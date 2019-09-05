//@ sourceURL=project_list.js
$(document).ready(function () {
    var spoiler_comments_table;
    loadModule(['dataTable'], function () {
        var spoilerCommentsTbl = $('#spoiler_comments');
        spoiler_comments_table = spoilerCommentsTbl.DataTable($.extend(true,{}, dtAutoOption(spoilerCommentsTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#spoiler_comments_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(spoiler_comments_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "spc_id"},
                { data: "sp_id" },
                { data: "pid" },
                { data: "content" },
                { data: "uid"},
                { data: "create_time"},
                { data: "spc_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(6)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-id="' + aData.spc_id + '">删除</a>');
                return nRow;
            }
        }));
        $("#spoiler_comments_btn").click(function(){spoiler_comments_table.ajax.reload(null, false)});
    });
});
$("#spoiler_comments").on('click','.btn-danger',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定删除该剧透吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: spoiler_comments_delete_url,
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