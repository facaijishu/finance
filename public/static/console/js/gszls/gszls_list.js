//@ sourceURL=project_list.js
$(document).ready(function () {
    var gszls_table;
    loadModule(['dataTable'], function () {
        var gszlsTbl = $('#gszls');
        gszls_table = gszlsTbl.DataTable($.extend(true,{}, dtAutoOption(gszlsTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#gszls_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(gszls_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "zqjc" },
                { data: "neeq" },
                { data: "gszls_status" },
                { data: "updated_at"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(5)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                if (aData.gszls_status == -1) {
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-blue'>隐藏</div>");
                    $('td:eq(5)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">展示</a>');
                }else{
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                    $('td:eq(5)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                }
                return nRow;
            }
        }));
        $("#gszls_btn").click(function(){gszls_table.ajax.reload(null, false)});
        gszlsTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:gszls_judge_url,
                    data:{id:id,model:'Gszls',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#gszls").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该企业的展示吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: gszls_hide_url,
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
$("#gszls").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该企业的展示吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: gszls_show_url,
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