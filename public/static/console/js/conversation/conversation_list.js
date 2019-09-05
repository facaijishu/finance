//@ sourceURL=project_list.js
$(document).ready(function () {
    var conversation_table;
    loadModule(['dataTable'], function () {
        var conversationTbl = $('#conversation');
        conversation_table = conversationTbl.DataTable($.extend(true,{}, dtAutoOption(conversationTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#conversation_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(conversation_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "c_id"},
                { data: "userName" },
                { data: "openId" },
                { data: "kf_nick" },
                { data: "kf_account" },
                { data: "create_time" },
//                { data: "c_id"}
            ],
//            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
//                $('td:eq(6)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.c_id + '"><i class="fa fa-search"></i>查看详情</a>');
//                return nRow;
//            }
        }));
        $("#conversation_btn").click(function(){conversation_table.ajax.reload(null, false)});
//        conversationTbl.on('click','[data-action]',function(){
//            var self = $(this);
//            var action = self.data("action");
//            var id = self.data("id");
//            if(action == 'detail'){
//                $.ajax({
//                    url:conversation_judge_url,
//                    data:{id:id,model:'Conversation',action:'detail'},
//                    success:function(resp){
//                        loadURL(resp.data.url);
//                    }
//                })
//            }
//        });
    });
});