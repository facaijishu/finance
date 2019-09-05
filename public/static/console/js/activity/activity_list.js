//@ sourceURL=project_list.js
$(document).ready(function () {
    var activity_table;
    loadModule(['dataTable'], function () {
        var activityTbl = $('#activity');
        activity_table = activityTbl.DataTable($.extend(true,{}, dtAutoOption(activityTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "autoWidth" : false,
            "ordering": true,
            "aLengthMenu": [20],
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#activity_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(activity_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "a_id"},
                { data: "act_name" },
                { data: "is_show" },
                { data: "num_people" },
                { data: "start_time" },
                { data: "end_time" },
                { data: "list_order" },
                { data: "fee" },
                { data: "create_time"},
                { data: "a_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
            	
            	
                $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.a_id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.is_show == 1){
                    $('td:eq(2)', nRow).html('上架中');
                	$('td:eq(9)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.a_id + '" class="btn btn-xs btn-primary js-stop cale" style="background-color:#951c1c;border-color:#951c1c;">下架</a>');
                }else{
                	$('td:eq(2)', nRow).html('下架中');
                	$('td:eq(9)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.a_id + '" class="btn btn-xs btn-primary js-start cale">上架</a>');
                }
                return nRow;
            }
        }));
        $("#activity_btn").click(function(){activity_table.ajax.reload(null, false)});
        activityTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:activity_judge_url,
                    data:{id:id,model:'Activity',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#activity").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定下架该活动吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: activity_stop_url,
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
$("#activity").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定上架该活动吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: activity_start_url,
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