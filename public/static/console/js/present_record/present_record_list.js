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
                { data: "pr_id" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(5)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-id="' + aData.pr_id + '">处理</a>');
//                $('td:eq(5)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-success cale" data-id="' + aData.pr_id + '">线下处理</a>');
                return nRow;
            }
        }));
        $("#present_record_btn").click(function(){present_record.ajax.reload(null, false)});
//        studyTbl.on('click','[data-action]',function(){
//            var self = $(this);
//            var action = self.data("action");
//            var id = self.data("id");
//            if(action == 'edit'){
//                $.ajax({
//                    url:spoiler_judge_url,
//                    data:{id:id,model:'Spoiler',action:'edit'},
//                    success:function(resp){
//                        loadURL(resp.data.url);
//                    }
//                })
//            }
//        });
    });
});
$("#present_record").on('click','.btn-danger',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定处理该申请吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: present_record_deal_url,
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
$("#present_record").on('click','.btn-success',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定线下处理该申请吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: present_record_line_deal_url,
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