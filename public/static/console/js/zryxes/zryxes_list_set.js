//@ sourceURL=project_list.js
$(document).ready(function () {
    var zryxes_table;
    loadModule(['dataTable'], function () {
        var zryxesTbl = $('#zryxes');
        zryxes_table = zryxesTbl.DataTable($.extend(true,{}, dtAutoOption(zryxesTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [30],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
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
                { data: "sec_uri_tyshortname" },
                { data: "secucode" },
                { data: "zryx_exhibition"},
                { data: "zryx_financing"},
                { data: "stick"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.sublevel == '基础层'){
                    $('td:eq(2)', nRow).html("<div class='badge bg-color-pink'>基</div>");
                }else{
                    $('td:eq(2)', nRow).html("<div class='badge bg-color-blue'>创</div>");
                }
                if(aData.trademethod == '协议'){
                    $('td:eq(2)', nRow).append("<div class='badge bg-color-orange'>竞</div>");
                }else{
                    $('td:eq(2)', nRow).append("<div class='badge bg-color-red'>市</div>");
                }
                $('td:eq(2)', nRow).append(aData.secucode);
                if(aData.zryx_exhibition == 1){
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                }else{
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-clue'>隐藏</div>");
                }
                if(aData.zryx_financing == 1){
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-clue'>融资结束</div>");
                }else{
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-green'>融资中</div>");
                }
                if(aData.stick == 1){
                    $('td:eq(5)', nRow).html("<div class='badge bg-color-green'>已置顶</div>");
                    $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-false cale">取消置顶</a>');
                }else{
                    $('td:eq(5)', nRow).html("<div class='badge bg-color-blue'>未置顶</div>");
                    $('td:eq(6)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-true cale">置顶</a>');
                }
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
                    data:{id:id,model:'Zryxes',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#zryxes").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消展示该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_hide_url,
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
$("#zryxes").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定展示该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_show_url,
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
$("#zryxes").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该大宗吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_settop_true_url,
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
$("#zryxes").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该大宗吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_settop_false_url,
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