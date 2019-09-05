//@ sourceURL=user_list.js
$(document).ready(function () {
    var user_table;
    loadModule(['dataTable', 'bootstrapValidator'], function () {
        var userTbl = $('#user');
        user_table = userTbl.DataTable($.extend(true, {}, dtAutoOption(userTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#user_form").serializeArray();
                if (query_form.length > 0) {
                    for (var i in query_form) {
                        if ('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(user_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "temp_id"},
                {data: "first"},
                {data: "keyword1"},
                {data: "keyword2"},
                {data: "keyword3"},
                {data: "keyword4"},
                {data: "keyword5"},
                {data: "user_type"},
                {data: "remark"},
                {data: "url"},
                {data: "type"},
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.uid + '"><i class="fa fa-edit"></i>修改</a>');
                if (aData.status == 1) {
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-green'>正常</div>");
                }else {
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-blue'>禁用</div>");
                }
                if (aData.is_admin == 1) {
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.uid + '" class="btn btn-xs btn-danger js-user-del cale disabled">删除</a>');
                } else {
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.uid + '" class="btn btn-xs btn-danger js-user-del cale">删除</a>');
                }
                return nRow;
            }
        }));
        $("#user_btn").click(function(){user_table.ajax.reload(null, false)});
        userTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'edit'){
                $.ajax({
                    url:user_judge_url,
                    data:{id:id,model:'User',action:'edit'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                });
            }
        });
    });
});
