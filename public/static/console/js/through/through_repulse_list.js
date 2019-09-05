//@ sourceURL=project_list.js
$(document).ready(function () {
    var through_table;
    loadModule(['dataTable'], function () {
        var throughTbl = $('#through');
        through_table = throughTbl.DataTable($.extend(true,{}, dtAutoOption(throughTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#through_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(through_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "t_id"},
                { data: "phone" },
                { data: "realName" },
                { data: "role_type" },
                { data: "card" },
                { data: "updateTime" },
                { data: "createTime" },
                { data: "status" },
                { data: "t_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                var strHtml = '';
                if(aData.card.length == 2){
                   $.each(aData.card,function(index,value){
                      strHtml += '<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/home/card/'+value+'" data-toggle="modal" data-target="#ModalImg">';
                   });
                }else{
                   strHtml = '<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/home/card/'+aData.card+'" data-toggle="modal" data-target="#ModalImg">';
                }
                $('td:eq(7)', nRow).html("<div class='badge bg-color-blueDark'>打回调整</div>");
               /* $('td:eq(6)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/home/card/'+aData.card+'" data-toggle="modal" data-target="#ModalImg">');*/
                $('td:eq(4)', nRow).html(strHtml);
                $('td:eq(8)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.t_id + '"><i class="fa fa-search"></i>查看详情</a>');
                $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.t_id + '" class="btn btn-xs btn-danger js-stop cale">终止</a>');
                return nRow;
            }
        }));
        $("#through_btn").click(function(){through_table.ajax.reload(null, false)});
        throughTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:through_judge_url,
                    data:{id:id,model:'Through',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
