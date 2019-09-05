//@ sourceURL=project_list.js
$(document).ready(function () {
    var consultation_table;
    loadModule(['dataTable'], function () {
        var consultationTbl = $('#pc_consultation');
        consultation_table = consultationTbl.DataTable($.extend(true,{}, dtAutoOption(consultationTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#pc_consultation_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(pc_consultation_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "con_id"},
//                { data: "top_img" },
                { data: "title" },
                { data: "source" },
                { data: "type" },
//                { data: "list_order" },
                { data: "create_time" },
                { data: "create_uid" },
                { data: "status" },
                { data: "is_hot" },
                { data: "con_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.is_hot == 1){
                    $('td:eq(7)', nRow).html("<div class='badge bg-color-green'>已置顶</div>");
                }else{
                    $('td:eq(7)', nRow).html("<div class='badge bg-color-blue'>未置顶</div>");
                }
                $('td:eq(8)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.con_id + '"><i class="fa fa-search"></i>查看详情</a>');
//                $('td:eq(1)', nRow).html('<img style="width:60px;" src="'+root_path_url+'/'+aData.top_img+'">');
                if (aData.status == 1) {
                    $('td:eq(6)', nRow).html('已发布');
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.con_id + '" class="btn btn-xs btn-primary js-stop cale">取消发布</a>');
                    if(aData.is_hot == -1){
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.con_id + '" class="btn btn-xs btn-danger js-hot-true cale">置顶</a>');
                    }else{
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.con_id + '" class="btn btn-xs btn-danger js-hot-false cale">取消置顶</a>');
                    }
                }else{
                    $('td:eq(6)', nRow).html('未发布');
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.con_id + '" class="btn btn-xs btn-primary js-start cale">发布</a>');
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger js-delete cale" data-id="' + aData.con_id + '">删除</a>');
                }
                return nRow;
            }
        }));
        $("#pc_consultation_btn").click(function(){consultation_table.ajax.reload(null, false)});
        consultationTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:pc_consultation_judge_url,
                    data:{id:id,model:'PcConsultation',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#pc_consultation").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消发布该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: pc_consultation_stop_url,
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
$("#pc_consultation").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定发布该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: pc_consultation_start_url,
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
$("#pc_consultation").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: pc_consultation_hot_true_url,
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
$("#pc_consultation").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: pc_consultation_hot_false_url,
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
$("#pc_consultation").on('click','.js-delete',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定删除该资讯吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: pc_consultation_delete_url,
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
