//@ sourceURL=project_list.js
$(document).ready(function () {
    var zfmxes_table;
    loadModule(['dataTable'], function () {
        var zfmxesTbl = $('#zfmxes');
        zfmxes_table = zfmxesTbl.DataTable($.extend(true,{}, dtAutoOption(zfmxesTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [30],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[7, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#zfmxes_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(zfmxes_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "sec_uri_tyshortname" },
                { data: "msecucode" },
                { data: "zfmx_examine" },
                { data: "zfmx_exhibition"},
                { data: "zfmx_financing"},
                { data: "stick"},
                { data: "plannoticeddate"},
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
                
                $('td:eq(2)', nRow).append(aData.msecucode);
                
                if (aData.zfmx_examine == 1) {
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-green'>已审核</div>");
                    if(aData.zfmx_exhibition == 1){
                        $('td:eq(4)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                    }else{
                        $('td:eq(4)', nRow).html("<div class='badge bg-color-clue'>隐藏</div>");
                    }
                    if(aData.zfmx_financing == 1){
                        $('td:eq(5)', nRow).html("<div class='badge bg-color-clue'>融资结束</div>");
                    }else{
                        $('td:eq(5)', nRow).html("<div class='badge bg-color-green'>融资中</div>");
                    }
                }else{
                    $('td:eq(3)', nRow).html("<div class='badge bg-color-blue'>待审核</div>");
                    $('td:eq(4)', nRow).html("-");
                    $('td:eq(5)', nRow).html("-");
                }
                if(aData.stick == 1){
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-green'>已置顶</div>");
                    $('td:eq(8)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-false cale">取消置顶</a>');
                }else{
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-blue'>未置顶</div>");
                    $('td:eq(8)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-danger js-hot-true cale">置顶</a>');
                }
                return nRow;
            }
        }));
        $("#zfmxes_btn").click(function(){zfmxes_table.ajax.reload(null, false)});
        zfmxesTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:zfmxes_judge_url,
                    data:{id:id,model:'Zfmxes',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#zfmxes").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消展示该增发明细吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_hide_url,
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
$("#zfmxes").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定展示该增发明细吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_show_url,
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
$("#zfmxes").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该增发吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_settop_true_url,
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
$("#zfmxes").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该增发吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_settop_false_url,
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