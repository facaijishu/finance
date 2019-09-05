//@ sourceURL=dict_type_list.js
$(document).ready(function () {
    var bonus_table;
    loadModule(['dataTable'], function () {
        var bonusTbl = $('#bonus');
        bonus_table = bonusTbl.DataTable($.extend(true, {}, dtAutoOption(bonusTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                $.getJSON(bonus_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "b_id"},
                {data: "time"},
                {data: "total_amount"},
                {data: "amount"},
                {data: "create_time"},
                {data: "create_uid"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(2)', nRow).html(aData.total_amount+'元');
                $('td:eq(3)', nRow).html(aData.amount+'元');
                return nRow;
            }
        }));
    });
});