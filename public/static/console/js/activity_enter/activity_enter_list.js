//@ sourceURL=project_list.js
$(document).ready(function () {
    var order_table;
    loadModule(['dataTable'], function () {
        var orderTbl = $('#order');
        order_table = orderTbl.DataTable($.extend(true,{}, dtAutoOption(orderTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "autoWidth" : false,
            "ordering": true,
            "aLengthMenu": [20],
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#order_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(order_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "o_id"},
                { data: "uid" },
                { data: "userName" },
                { data: "name" },
                { data: "phone" },
                { data: "introduce" },
                { data: "is_follow" },
                { data: "company_jc" },
                { data: "position" },
                { data: "body" },
                { data: "create_time" },
                { data: "review" },
                { data: "o_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
            	if(aData.is_follow == 1){
                    $('td:eq(6)', nRow).html("是");
                }else{
                	$('td:eq(6)', nRow).html("否");
                }
            	
            	if(aData.review == 2){
                    $('td:eq(11)', nRow).html("不通过");
                }else if(aData.review == 1){
                    $('td:eq(11)', nRow).html("通过");
                }else{
                	$('td:eq(11)', nRow).html("待审核");
                }
            	
        		$('td:eq(12)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info  cale" data-action="detail"  data-id="' + aData.o_id + '"><i class="fa fa-search"></i>查看详细</a>');
                $('td:eq(12)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-success btn-update cale " data-id="' + aData.o_id + '"> <i class="fa fa-caret-square-o-up">更新</a>');
               
                return nRow;
            }
        }));
        $("#order_btn").click(function(){order_table.ajax.reload(null, false)});
        order_table.on('click','[data-action]',function(){
            var self 	= $(this);
            var action  = self.data("action");
            var id 		= self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:order_judge_url,
                    data:{id:id,model:'ActivityEnter',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});

//更新报名信息
$("#order").on('click','.btn-update',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定更新该会员信息吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: order_update_url,
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