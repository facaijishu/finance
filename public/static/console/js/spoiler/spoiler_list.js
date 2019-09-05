//@ sourceURL=project_list.js
$(document).ready(function () {
    var spoiler_table;
    loadModule(['dataTable'], function () {
        var spoilerTbl = $('#spoiler');
        spoiler_table = spoilerTbl.DataTable($.extend(true,{}, dtAutoOption(spoilerTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#spoiler_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(spoiler_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "sp_id"},
                { data: "content" },
                { data: "release_date" },
                { data: "create_uid" },
                { data: "sp_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(4)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.sp_id + '"><i class="fa fa-edit"></i>修改</a>');
                var img = aData.img;
                if(img != ''){
                    var arr = img.split(',');
                    var str = '';
                    for(var i = 0 ; i < arr.length ; i ++ ){
                        //str += '<img style="height:60px;margin-right:5px;" src="'+root_path_url+'/'+arr[i]+'">';
                        var pos = arr[i].lastIndexOf('.');
                        var suffix = arr[i].substr(pos+1);
                        if (suffix == 'pdf') {
                            str += '<br/>' + arr[i] + '<br/>';
                        } else {
                            str += '<img style="height:60px;margin-right:5px;" src="'+root_path_url+'/'+arr[i]+'">';
                        }            
                    }
                    $('td:eq(1)', nRow).append('<p class="form-inline" style="margin-top:10px;max-width:500px;">'+str+'</p>');
                }
                if (aData.status == 1) {
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.sp_id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                }else{
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.sp_id + '" class="btn btn-xs btn-primary js-start cale">开启</a>');
                    $('td:eq(4)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-id="' + aData.sp_id + '">删除</a>');
                }
                return nRow;
            }
        }));
        $("#spoiler_btn").click(function(){spoiler_table.ajax.reload(null, false)});
        spoilerTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'edit'){
                $.ajax({
                    url:spoiler_judge_url,
                    data:{id:id,model:'Spoiler',action:'edit'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#spoiler").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该剧透的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: spoiler_stop_url,
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
$("#spoiler").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该剧透的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: spoiler_start_url,
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
$("#spoiler").on('click','.btn-danger',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定删除该剧透吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: spoiler_delete_url,
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
