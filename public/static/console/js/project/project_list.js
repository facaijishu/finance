//@ sourceURL=project_list.js
$(document).ready(function () {
    var project_table;
    loadModule(['dataTable'], function () {
        var projectTbl = $('#project');
        project_table = projectTbl.DataTable($.extend(true,{}, dtAutoOption(projectTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#project_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(project_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "pro_id"},
                { data: "company_name" },
                { data: "stock_code" },
                { data: "qrcode_path" },
                { data: "status" },
                { data: "create_time" },
                { data: "last_time" },
                { data: "pro_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.pro_id + '"><i class="fa fa-search"></i>查看详情</a>');
                $('td:eq(3)', nRow).html('<img style="width:50px;cursor:pointer;" src="'+root_path_url+'/project/code/'+aData.qrcode_path+'" alt="二维码" data-toggle="modal" data-target="#ModalImg">');
                if (aData.status == 2) {
                    $('td:eq(4)', nRow).html('融资中');
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-success end cale">融资结束</a>');
//                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-danger js-send cale">推送消息</a>');
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale " data-action="sendinfo" data-id="' + aData.pro_id + '"><i class="fa fa-search"></i>推送消息</a>');
                }else if(aData.status == 1){
                    $('td:eq(4)', nRow).html('草稿');
                }else{
                    $('td:eq(4)', nRow).html('融资结束');
//                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-danger js-send cale">推送消息</a>');
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale " data-action="sendinfo" data-id="' + aData.pro_id + '"><i class="fa fa-search"></i>推送消息</a>');
                }
                if(aData.flag == 1){
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    $('td:eq(4)', nRow).append('/显示');
                }else{
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                    $('td:eq(4)', nRow).append('/隐藏');
                }
                return nRow;
            }
        }));
        $("#project_btn").click(function(){project_table.ajax.reload(null, false)});
        projectTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:project_judge_url,
                    data:{id:id,model:'project',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
//            else if(action == 'sendinfo'){
//                $.ajax({
//                    url:project_judge_url,
//                    data:{id:id,model:'project',action:'sendinfo'},
//                    success:function(resp){
//                        loadURL(resp.data.url);
//                    }
//                })
//            }
            else if(action == 'sendinfo'){
               $.ajax({
                   url:project_judge_url,
                   data:{id:id,model:'project',action:'detail2'},
                   success:function(resp){
                       loadURL(resp.data.url);
                   }
               })
           }
        });
    });
});
/*$("#project").on('click','.js-send',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定推送该项目吗？', function () {
        var id = _this.attr("data-id");
        $("body").append(ye);
        $.ajax({
            url: project_send_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    $("body .zidingyi").remove();
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});*/
$("#project").on('click','.end',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定结束融资该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_end_url,
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
$("#project").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_stop_url,
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
$("#project").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_start_url,
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
//$("#project").on('click','.js-send',function(){
//    var _this = $(this);
//    Dialog.confirm('操作提示', '确定推行该项目的模板消息吗？', function () {
//        var id = _this.attr("data-id");
//        $.ajax({
//            url: project_send_url,
//            type: 'post',
//            data: {id: id},
//            dataType: 'json',
//            success: function (resp) {
//                if (resp.code) {
//                    Dialog.success('操作成功', resp.msg, 2000, function () {
//                        location.reload();
//                    });
//                } else {
//                    Dialog.error('操作失败', resp.msg);
//                }
//            }
//        })
//    });
//});
