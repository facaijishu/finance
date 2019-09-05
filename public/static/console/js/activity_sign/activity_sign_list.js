//@ sourceURL=project_list.js
$(document).ready(function () {
    var activity_table;
    loadModule(['dataTable'], function () {
        var activityTbl = $('#activity');
        activity_table = activityTbl.DataTable($.extend(true,{}, dtAutoOption(activityTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "autoWidth" : false,
            "ordering": true,
            "aLengthMenu": [20],
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#activity_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(activity_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "a_id"},
                { data: "act_name" },
                { data: "sign_time" },
                { data: "realName" },
                { data: "mobile" },
                { data: "company" },
                { data: "position" },
                { data: "channel" }
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
               return nRow;
            }
        }));
        $("#activity_btn").click(function(){activity_table.ajax.reload(null, false)});
        
    });
});
