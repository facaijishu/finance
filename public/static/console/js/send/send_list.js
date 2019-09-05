//@ sourceURL=project_list.js
$(document).ready(function () {
    var send_table;
    loadModule(['dataTable'], function () {
        var sendTbl = $('#send');
        send_table = sendTbl.DataTable($.extend(true,{}, dtAutoOption(sendTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#send_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(send_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "type" },
                { data: "act_id" },
                { data: "created_at" },
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if (aData.type == 'project') {
                    $('td:eq(1)', nRow).html('精品项目');
                }
                if (aData.type == 'zryxes') {
                    $('td:eq(1)', nRow).html('大宗转让');
                }
                $('td:eq(2)', nRow).html(aData.pro_name);
                $('td:eq(4)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        $("#send_btn").click(function(){send_table.ajax.reload(null, false)});
        sendTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:send_judge_url,
                    data:{id:id,model:'Send',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
