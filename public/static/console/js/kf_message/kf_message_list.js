//@ sourceURL=project_list.js
$(document).ready(function () {
    var kf_message_table;
    loadModule(['dataTable'], function () {
        var kfmessageTbl = $('#kf_message');
        kf_message_table = kfmessageTbl.DataTable($.extend(true,{}, dtAutoOption(kfmessageTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#kf_message_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(kf_message_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "km_id"},
                { data: "m_uid" },
                { data: "direction" },
                
                { data: "scs_id" },
                { data: "type" },
                { data: "content" },
                { data: "create_time" },
                { data: "openId" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.direction == '-->'){
                    $('td:eq(2)', nRow).html('--》');
                }else{
                    $('td:eq(2)', nRow).html('《--');
                }
                if(aData.type == 'text'){
                    $('td:eq(5)', nRow).html(aData.content);
                }else if(aData.type == 'image'){
                    $('td:eq(5)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+aData.content+'" data-toggle="modal" data-target="#ModalImg">');
                }else{
                    $('td:eq(5)', nRow).html('<p class="voice"><span class="fa fa-rss"></span><audio id="musicBox" src="/'+aData.content+'" preload=""></audio></p>');
                }
                return nRow;
            }
        }));
        $("#kf_message_btn").click(function(){kf_message_table.ajax.reload(null, false)});
    });
});
$("#kf_message").on('click' , '.voice' , function(){
    $(this).find("audio")[0].play();
});