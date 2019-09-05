//@ sourceURL=organize_list.js
$(document).ready(function () {
    var organize_table;
    loadModule(['dataTable'], function () {
        var organizeTbl = $('#organize');
        organize_table = organizeTbl.DataTable($.extend(true,{}, dtAutoOption(organizeTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#organize_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(organize_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "org_id"},
                { data: "org_name" },
                { data: "qrcode_path" },
                { data: "list_order" },
                { data: "flag" },
                { data: "create_time" },
                { data: "last_time" },
                { data: "org_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.org_id + '"><i class="fa fa-search"></i>查看详情</a>');
                $('td:eq(2)', nRow).html('<img style="width:50px;cursor:pointer;" src="'+root_path_url+'/organize/code/'+aData.qrcode_path+'" alt="二维码" data-toggle="modal" data-target="#ModalImg">');
               if(aData.status == 1){
                    $('td:eq(4)', nRow).html('草稿');
                }else{
                    $('td:eq(4)', nRow).html('发布');
                }
                if(aData.flag == 1){
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.org_id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    $('td:eq(4)', nRow).append('/显示');
                }else{
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.org_id + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                    $('td:eq(4)', nRow).append('/隐藏');
                }
                return nRow;
            }
        }));
        $("#organize_btn").click(function(){organize_table.ajax.reload(null, false)});
        organizeTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:organize_judge_url,
                    data:{id:id,model:'organize',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});

$("#organize").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该机构吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: organize_stop_url,
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
$("#organize").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: organize_start_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code == 1) {
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else if(resp.code == 2){
                    Dialog.error('操作失败', resp.msg);
                }else{
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});
//导出功能
$(".export").on("click",function(){
    //点击跳转之前将查询的数据进行统计
    //开始时间
    var create_date1 = $("input[name='create_date1']").val();
    //结束时间
    var create_date2 = $("input[name='create_date2']").val();
    //机构名称
    var org_name 	 = $("input[name='org_name']").val();
    //状态
    var flag 		 = $("input[name='flag']:checked").val();
    window.location.href = "http://fin.jrfacai.com/console/organize/org_client?create_date1="+create_date1+"&create_date2="
                            +create_date2+"&org_name="+org_name+"&flag="+flag;
    
});
