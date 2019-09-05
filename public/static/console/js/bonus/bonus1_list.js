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
                $.getJSON(bonus1_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                {data: "b_id"},
                {data: "content"},
                {data: "create_time"},
                {data: "create_uid"},
                {data: "b_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.status == 1){
                    $('td:eq(4)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.b_id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                }else{
                    $('td:eq(4)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.b_id + '" class="btn btn-xs btn-danger js-start cale">开启</a>');
                }
                return nRow;
            }
        }));
    });
});
$("#bonus").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该公告的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: bonus_stop_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});
$("#bonus").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该公告的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: bonus_start_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('操作成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('操作失败', resp.msg);
                }
            }
        })
    });
});