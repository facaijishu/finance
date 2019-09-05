//@ sourceURL=project_list.js
$(document).ready(function () {
    var member_table;
    loadModule(['dataTable'], function () {
        var memberTbl = $('#yaoqing');
        member_table = memberTbl.DataTable($.extend(true,{}, dtAutoOption(memberTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#yaoqing_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(yaoqing_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "userPhoto"},
                { data: "userName" },
                { data: "realName" },
                { data: "userPhone" },
                { data: "openId" },
                { data: "userType" },
                { data: "CREATE_TIME" },
                { data: "superior" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(0)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+aData.userPhoto+'" data-toggle="modal" data-target="#ModalImg">');
                if(aData.userType == 0){
                    $('td:eq(5)', nRow).html("游客");
                }else if(aData.userType == 1){
                    $('td:eq(5)', nRow).html("绑定用户");
                }else{
                    $('td:eq(5)', nRow).html("认证合伙人");
                }
                return nRow;
            }
        }));
        $("#yaoqing_btn").click(function(){member_table.ajax.reload(null, false)});
    });
});