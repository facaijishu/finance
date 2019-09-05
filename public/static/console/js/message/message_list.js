//@ sourceURL=project_list.js
$(document).ready(function () {
    var message_table;
    loadModule(['dataTable'], function () {
        var messageTbl = $('#message');
        message_table = messageTbl.DataTable($.extend(true,{}, dtAutoOption(messageTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[5, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#message_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(message_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "m_id"},
                { data: "content" },
                { data: "pro_id" },
                { data: "create_uid" },
                { data: "create_time" },
                { data: "last_time" },
                { data: "m_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(6)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.m_id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        $("#message_btn").click(function(){message_table.ajax.reload(null, false)});
        messageTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url: message_judge_url,
                    data:{id:id,model:'message',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});