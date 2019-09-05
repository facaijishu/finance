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
                { data: "authors" },
                { data: "rate" },
                { data: "change" },
                { data: "relation" },
                { data: "report_date" },
                { data: "examine" },
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(10)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.codename == ''){
                    $('td:eq(1)', nRow).html('-');
                }
                if(aData.rate == ''){
                    $('td:eq(5)', nRow).html('-');
                }
                if(aData.change == ''){
                    $('td:eq(6)', nRow).html('-');
                }
                if(aData.authors == ''){
                    $('td:eq(4)', nRow).html('-');
                }
                if(aData.examine == 1){
                    $('td:eq(9)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                    $('td:eq(10)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                }else{
                    $('td:eq(9)', nRow).html("<div class='badge bg-color-blue'>隐藏</div>");
                    $('td:eq(10)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">展示</a>');
                }
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
                    data:{id:id,model:'StYanbaos',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#yanbaos").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该研报吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: yanbaos_hide_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('取消展示成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('取消展示失败', resp.msg);
                }
            }
        })
    });
});
$("#yanbaos").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定展示该研报吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: yanbaos_show_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('展示成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('展示失败', resp.msg);
                }
            }
        })
    });
});