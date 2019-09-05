//@ sourceURL=project_list.js
$(document).ready(function () {
    var gonggaos_table;
    loadModule(['dataTable'], function () {
        var gonggaosTbl = $('#gonggaos');
        gonggaos_table = gonggaosTbl.DataTable($.extend(true,{}, dtAutoOption(gonggaosTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[3, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#gonggaos_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(gonggaos_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "title" },
                { data: "secu_s_name" },
                { data: "date" },
                { data: "created_at" },
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(5)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        $("#gonggaos_btn").click(function(){gonggaos_table.ajax.reload(null, false)});
        gonggaosTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:gonggaos_judge_url,
                    data:{id:id,model:'StGonggaos',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});