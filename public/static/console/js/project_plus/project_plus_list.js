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
                { data: "pro_name" },
                { data: "qrcode_path" },
                { data: "status" },
                { data: "create_time" },
                { data: "last_time" },
                { data: "pro_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(6)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.pro_id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.qrcode_path==null){
                	$('td:eq(2)', nRow).html('--');
                }else{
                	$('td:eq(2)', nRow).html('<img style="width:50px;cursor:pointer;" src="'+root_path_url+'/project/code/'+aData.qrcode_path+'" alt="二维码" data-toggle="modal" data-target="#ModalImg">');
                }
                if (aData.status == 2 && aData.flag == 1 ) {
                    $('td:eq(3)', nRow).html('上架中');
                    $('td:eq(6)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-danger down cale">下架</a>');
                }else if(aData.status == 2 && aData.flag == 0){
                    $('td:eq(3)', nRow).html('下架中');
                    $('td:eq(6)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-success up cale">上架</a>');
                }else if(aData.status == 0 ||aData.status == 1) {
                    $('td:eq(3)', nRow).html('待审核');
                    $('td:eq(6)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-primary approve cale">审核</a>');
                    if(aData.qrcode_path==null){
                    	$('td:eq(6)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pro_id + '" class="btn btn-xs btn-danger qrcode cale">生成二维码</a>');
                    }
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
                    data:{id:id,model:'project_plus',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
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


$("#project").on('click','.approve',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定该项目审核通过吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_approve_url,
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

$("#project").on('click','.up',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定上架该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_up_url,
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

$("#project").on('click','.down',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定下架该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_down_url,
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

$("#project").on('click','.qrcode',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '该项目确定需要生成二维码吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_qrcode_url,
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