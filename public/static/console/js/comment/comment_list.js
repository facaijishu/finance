//@ sourceURL=comment_list.js
$(document).ready(function(){
	var comment_table;
	loadModule(['dataTable'],function(){
		var commentTbl = $('#comment');
		  comment_table = commentTbl.DataTable($.extend(true,{},dtAutoOption(commentTbl),{
		  	sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};

                query_data['pub_id']= $("#publish_detail").attr('data-id');
                // console.log(query_data['resource_id']);
                $.getJSON(comment_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "cid"},
                { data: "realName"},
                { data: "content" },
                { data: "create_time" },
                { data: "is_del" },
                { data: "id"},
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if (aData.is_del == 1) {
                    $('td:eq(4)',nRow).html('未删除');
                }

                if (aData.is_del == 2) {
                    $('td:eq(4)',nRow).html('已删除');
                }

                $('td:eq(5)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-pub_id="'+aData.pub_id+'" data-id="' + aData.cid + '"><i class="fa fa-search"></i>查看回复</a>');
                
                if (aData.status == 1) {
                    $('td:eq(5)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.cid + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    
                }
                if (aData.status == 2) {
                    $('td:eq(5)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.cid + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                   
                }
                return nRow;
            }
		  }));
		  //查询
		  $("#comment_btn").click(function(){comment_table.ajax.reload(null, false)});
		  //查看回复
	        commentTbl.on('click','[data-action]',function(){
	            var self = $(this);
	            var action = self.data("action");
                var pub_id = self.data("pub_id"); 
	            var id = self.data("id");
	            if(action == 'detail'){
	                $.ajax({
	                    url:comment_judge_url,
	                    data:{id:id,pub_id:pub_id,model:'comment',action:'detail'},
	                    success:function(resp){
	                        loadURL(resp.data.url);
	                    }
	                })
	            }
	        });
	});

});

//隐藏该评论
$('#comment').on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该评论吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: comment_stop_url,
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
$('#comment').on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该评论吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: comment_start_url,
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