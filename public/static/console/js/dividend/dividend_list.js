//@ sourceURL=dict_type_list.js
$(document).ready(function () {
    var dividend_table;
    loadModule(['dataTable'], function () {
        var dividendTbl = $('#dividend');
        dividend_table = dividendTbl.DataTable($.extend(true, {}, dtAutoOption(dividendTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                $.getJSON(dividend_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "month"},
                {data: "jifen"},
                {data: "ratio"},
                {data: "total_money"},
                {data: "peoples"},
                {data: "accounting"},
                {data: "success"},
                {data: "di_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.ratio == '-'){
                    $('td:eq(2)', nRow).html('-');
                }else{
                    $('td:eq(2)', nRow).html('1:'+aData.ratio);
                }
                if(aData.accounting == -1){
                    $('td:eq(5)', nRow).html('未核算');
                }else{
                    $('td:eq(5)', nRow).html('已核算');
                }
                if(aData.success == -1){
                    $('td:eq(6)', nRow).html('未发放');
                }else{
                    $('td:eq(6)', nRow).html('已发放');
                }
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.di_id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        dividendTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:dividend_judge_url,
                    data:{id:id,model:'Dividend',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});