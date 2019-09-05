//@ sourceURL=comment_reply_reply_list.js
$(document).ready(function(){
	var comment_reply_table;
	loadModule(['dataTable'],function(){
		var comment_replyTbl = $('#reply-comment');
		  comment_reply_table = comment_replyTbl.DataTable($.extend(true,{},dtAutoOption(comment_replyTbl),{
		  	sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                // var query_form = $("#comment_reply_form").serializeArray();
                // if(query_form.length > 0) {
                //     for (var i in query_form) {
                //         if('string' == typeof query_form[i]['name']) {
                //             query_data[query_form[i]['name']] = query_form[i]['value'];
                //         }
                //     }
                // }
                query_data['fid'] = $("#reply-comment").attr('data-id');
                $.getJSON(comment_reply_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "cid"},
                { data: "realName"},
                { data: "content" },
                { data: "create_time" },
                { data: "is_del" },
                { data: "cid"},
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if (aData.is_del == 1) {
                    $('td:eq(4)',nRow).html('未删除');
                }

                if (aData.is_del == 2) {
                    $('td:eq(4)',nRow).html('已删除');
                }
                
                if (aData.status == 1) {
                    $('td:eq(5)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.cid + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    
                }
                if (aData.status == 2) {
                    $('td:eq(5)', nRow).html('<a href="javascript:void(0);" data-id="' + aData.cid + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                   
                }
                return nRow;
            }
		  }));
		  //查询
		  // $("#comment_reply_btn").click(function(){comment_reply_table.ajax.reload(null, false)});
		  //查看详情
	        // comment_replyTbl.on('click','[data-action]',function(){
	        //     var self = $(this);
	        //     var action = self.data("action");
	        //     var id = self.data("id");
	        //     if(action == 'detail'){
	        //         $.ajax({
	        //             url:comment_reply_judge_url,
	        //             data:{id:id,model:'comment_reply',action:'detail'},
	        //             success:function(resp){
	        //                 loadURL(resp.data.url);
	        //             }
	        //         })
	        //     }
	        // });
	});

});

//隐藏该回复
$('#reply-comment').on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该回复吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: comment_reply_stop_url,
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
//显示该评论
$('#reply-comment').on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该评论吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: comment_reply_start_url,
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