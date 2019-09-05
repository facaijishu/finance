//@ sourceURL=project_list.js
$(document).ready(function () {
    var study_table;
    loadModule(['dataTable'], function () {
        var studyTbl = $('#study');
        study_table = studyTbl.DataTable($.extend(true,{}, dtAutoOption(studyTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": false,
//            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#study_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(study_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "userPhoto"},
                { data: "userName" },
                { data: "realName" },
                { data: "phone" },
                { data: "openId" },
                { data: "url" },
                { data: "userType" },
                { data: "create_time" },
                { data: "status" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(0)', nRow).html('<img style="width:60px;cursor:pointer;" data-toggle="modal" data-target="#ModalImg" src="'+aData.userPhoto+'">');
                if (aData.userType == 0) {
                    $('td:eq(6)', nRow).html('游客');
                }else if(aData.userType == 1){
                    $('td:eq(6)', nRow).html('绑定用户');
                }else{
                    $('td:eq(6)', nRow).html('认证合伙人');
                }
                if(aData.status == 0){
                    $('td:eq(8)', nRow).html('未处理');
                }else if(aData.status == 1){
                    $('td:eq(8)', nRow).html('已处理');
                }
                return nRow;
            }
        }));
        $("#study_btn").click(function(){study_table.ajax.reload(null, false)});
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
$("#study").on('click','.btn-danger',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定处理该申请吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: study_deal_url,
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