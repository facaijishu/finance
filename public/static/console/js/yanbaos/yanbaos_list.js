//@ sourceURL=project_list.js
$(document).ready(function () {
    var yanbaos_table;
    loadModule(['dataTable'], function () {
        var yanbaosTbl = $('#yanbaos');
        yanbaos_table = yanbaosTbl.DataTable($.extend(true,{}, dtAutoOption(yanbaosTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#yanbaos_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(yanbaos_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "codename" },
                { data: "title" },
                { data: "org" },
                { data: "yb_id" },
                { data: "rate" },
                { data: "change" },
                { data: "report_date" },
                { data: "created_at" },
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.codename == ''){
                    $('td:eq(1)', nRow).html('-');
                }
                if(aData.rate == ''){
                    $('td:eq(5)', nRow).html('-');
                }
                if(aData.change == ''){
                    $('td:eq(6)', nRow).html('-');
                }
                if(aData.author == ''){
                    $('td:eq(4)', nRow).html('-');
                }else{
                    $('td:eq(4)', nRow).html(aData.author);
                }
                $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        $("#yanbaos_btn").click(function(){yanbaos_table.ajax.reload(null, false)});
        yanbaosTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:yanbaos_judge_url,
                    data:{id:id,model:'StYanbaos',action:'sucaidetail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});