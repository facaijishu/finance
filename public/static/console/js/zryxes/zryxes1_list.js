//@ sourceURL=project_list.js
$(document).ready(function () {
    var zryxes_table;
    loadModule(['dataTable'], function () {
        var zryxesTbl = $('#zryxes');
        zryxes_table = zryxesTbl.DataTable($.extend(true,{}, dtAutoOption(zryxesTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [30],
            "autoWidth" : false,
//            "ordering": true,
//            "aaSorting": [[6, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#zryxes_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(zryxes_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "secucode" },
                { data: "sec_uri_tyshortname" },
                { data: "direction" },
                { data: "price" },
                { data: "created_at"},
                { data: "enddate"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.sublevel == '基础层'){
                    $('td:eq(1)', nRow).html("<div class='badge bg-color-pink'>基</div>");
                }else{
                    $('td:eq(1)', nRow).html("<div class='badge bg-color-blue'>创</div>");
                }
                if(aData.trademethod == '协议'){
                    $('td:eq(1)', nRow).append("<div class='badge bg-color-orange'>竞</div>");
                }else{
                    $('td:eq(1)', nRow).append("<div class='badge bg-color-red'>市</div>");
                }
                $('td:eq(1)', nRow).append(aData.secucode);
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                return nRow;
            }
        }));
        $("#zryxes_btn").click(function(){zryxes_table.ajax.reload(null, false)});
        zryxesTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:zryxes_judge_url,
                    data:{id:id,model:'Zryxes',action:'sucaidetail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});