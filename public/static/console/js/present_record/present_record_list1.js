//@ sourceURL=project_list.js
$(document).ready(function () {
    var present_record;
    loadModule(['dataTable'], function () {
        var present_recordTbl = $('#present_record');
        present_record = present_recordTbl.DataTable($.extend(true,{}, dtAutoOption(present_recordTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
//            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#present_record_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(present_record_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "partner_trade_no"},
                { data: "openid" },
                { data: "amount" },
                { data: "create_time" },
                { data: "create_uid" },
                { data: "type" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.type = 'on-line'){
                    $('td:eq(5)', nRow).html("<div class='badge bg-color-green'>在线支付</div>");
                }else{
                    $('td:eq(5)', nRow).html("<div class='badge bg-color-blue'>线下支付</div>");
                }
                return nRow;
            }
        }));
        $("#present_record_btn").click(function(){present_record.ajax.reload(null, false)});
    });
});
