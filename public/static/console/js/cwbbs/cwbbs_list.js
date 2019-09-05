//@ sourceURL=project_list.js
$(document).ready(function () {
    var cwbbs_table;
    loadModule(['dataTable'], function () {
        var cwbbsTbl = $('#cwbbs');
        cwbbs_table = cwbbsTbl.DataTable($.extend(true,{}, dtAutoOption(cwbbsTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#cwbbs_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(cwbbs_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "zqjc" },
                { data: "neeq" },
                { data: "type" },
                { data: "reportdate" },
                { data: "created_at"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(6)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.type == 0){
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-blue'>资产负债表</div>");
                }else if(aData.type == 1){
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-green'>利润表</div>");
                }else if(aData.type == 2){
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-pink'>现金流量表</div>");
                }else{
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-orange'>财务分析表</div>");
                }
                return nRow;
            }
        }));
        $("#cwbbs_btn").click(function(){cwbbs_table.ajax.reload(null, false)});
        cwbbsTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:cwbbs_judge_url,
                    data:{id:id,model:'Cwbbs',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});