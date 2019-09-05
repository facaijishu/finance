//@ sourceURL=publish_list.js
$(document).ready(function(){
	var publish_table;
	loadModule(['dataTable'],function(){
		var publishTbl = $('#publish');
		  publish_table = publishTbl.DataTable($.extend(true,{},dtAutoOption(publishTbl),{
		  	sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#publish_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(publish_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "uid"},
                { data: "realName" },
                { data: "create_time" },
                { data: "content" },
                { data: "num" },
                { data: "is_del" },
                { data: "rank"},
                { data: "id"},
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {

            	if (aData.is_del == 1) {
            		$('td:eq(6)', nRow).html('已删除');
            	}

            	if (aData.is_del == 2) {
            		$('td:eq(6)', nRow).html('未删除');
            	}

                $('td:eq(8)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                $('td:eq(8)', nRow).append('<a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#ModalOrder">设置排序</a>');

                if (aData.status == 1) {
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    
                }

                if (aData.status == 2) {
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                   
                }
                if (aData.is_top == 1) {
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-hot-false cale">取消置顶</a>');
                   
                }
                if (aData.is_top == 2) {
                    $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-hot-true cale">置顶</a>');
                   
                }

                return nRow;
            }

		  }));
		  //查询
		  $("#publish_btn").click(function(){publish_table.ajax.reload(null, false)});
		  //查看详情
	        publishTbl.on('click','[data-action]',function(){
	            var self = $(this);
	            var action = self.data("action");
	            var id = self.data("id");
                // var resource = $("#publish").attr('data-resource');
	            if(action == 'detail'){
	                $.ajax({
	                    url:publish_judge_url,
	                    data:{id:id,model:'publish',action:'detail'},
	                    success:function(resp){
	                        loadURL(resp.data.url);
	                    }
	                })
	            }
	        });
	});

});
//隐藏该发布
$('#publish').on('click','.js-stop',function(){
	var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: publish_stop_url,
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
//显示该发布
$('#publish').on('click','.js-start',function(){
	var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: publish_start_url,
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
//导出发布需求excel
$(".export").on("click",function(){
    //点击跳转之前将查询的数据进行统计
    //发布人id
    var uid = $("input[name='uid']").val();
    //发布人
    var username = $("input[name='username']").val();
    window.location.href = "http://fin.jrfacai.com/console/publish/pub_client?uid="+uid+"&username="
                            +username;
    
    //window.location.href = "http://jr.yingtongkeji.com/console/zfmxes/dingzeng_export";
});
//取消置顶
$("#publish").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: publish_settop_false_url,
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
//置顶
$("#publish").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: publish_settop_true_url,
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
//排序
$('#ModalOrder').on('show.bs.modal', function (e) {
    var list_order = $(e.relatedTarget).parents("tr").find("td:eq(7)").text();
    //console.log(list_order);
    var id = $(e.relatedTarget).parents("tr").find("td:eq(0)").text();
    $("#ModalOrder").find("input[name='list_order']").val(list_order);
    $("#ModalOrder").find("input[name='id']").val(id);
    loadModule('bootstrapValidator', function () {
        $('#set_list_order_form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                list_order: {
                    validators: {
                        notEmpty: {
                            message:'请输入排序号'
                        },
                        regexp: {
                            regexp: /^\d+$/,
                            message: '请输入有效排序号'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (resp) {
                    if (resp.code) {
                        Dialog.success('操作成功', resp.msg, 3000, function () {
                            loadURL(resp.data.url);
                        });
                    } else {
                        Dialog.error('操作失败', resp.msg);
                    }
                }
            });
        });
    });
}).on('hidden.bs.modal', function (e) {
    $('#set_list_order_form').bootstrapValidator('destroy');
    $('#set_list_order_form')[0].reset();
});