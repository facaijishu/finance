//@ sourceURL=project_list.js
$(document).ready(function () {
    var report_table;
    loadModule(['dataTable'], function () {
        var reportTbl = $('#industryReport');
        report_table = reportTbl.DataTable($.extend(true,{}, dtAutoOption(reportTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#report_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(report_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "title" },
                { data: "status" },
                { data: "is_hot" },
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.is_hot == 1){
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-green'>已置顶</div>");
                }else{
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-blue'>未置顶</div>");
                }
                $('td:eq(4)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                if (aData.status == 1) {
                    $('td:eq(2)', nRow).html('已发布');
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">取消发布</a>');
                    if(aData.is_hot == -1){
                        $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-true cale">置顶</a>');
                    }else{
                        $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-false cale">取消置顶</a>');
                    }
                }else{
                    $('td:eq(2)', nRow).html('未发布');
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">发布</a>');
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger js-delete cale" data-id="' + aData.id + '">删除</a>');
                }
                return nRow;
            }
        }));
        $("#report_btn").click(function(){report_table.ajax.reload(null, false)});
        reportTbl.on('click','[data-action]',function(){
            var self 	= $(this);
            var action 	= self.data("action");
            var id 		= self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:report_judge_url,
                    data:{id:id,model:'IndustryReport',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#industryReport").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消发布该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: report_stop_url,
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
$("#industryReport").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定发布该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: report_start_url,
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
$("#industryReport").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: report_hot_true_url,
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
$("#industryReport").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: report_hot_false_url,
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
$("#industryReport").on('click','.js-delete',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定删除该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: report_delete_url,
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