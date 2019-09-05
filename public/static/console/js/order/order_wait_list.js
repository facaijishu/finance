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
                { data: "out_trade_no" },
                { data: "uid" },
                { data: "type" },
                { data: "name" },
                { data: "phone" },
                { data: "body" },
                { data: "total_fee" },
                { data: "create_time" },
                { data: "pay_time"},
                { data: "o_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(10)', nRow).html('');
//                $('td:eq(10)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.o_id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.type == 'activity'){
                    $('td:eq(3)', nRow).html('活动中心');
                }else{
                    $('td:eq(3)', nRow).html('学习中心');
                }
                return nRow;
            }
        }));
        $("#order_btn").click(function(){activity_table.ajax.reload(null, false)});
        orderTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:order_judge_url,
                    data:{id:id,model:'Order',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});