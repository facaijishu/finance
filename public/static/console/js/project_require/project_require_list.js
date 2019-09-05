//@ sourceURL=publish_list.js
$(document).ready(function(){
	var project_require_table;
	loadModule(['dataTable'],function(){
		var projectRequireTbl = $('#project_require');
		  project_require_table = projectRequireTbl.DataTable($.extend(true,{},dtAutoOption(projectRequireTbl),{
		  	sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#project_require_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(project_require_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "pr_id" },
                { data: "realName" },
                { data: "create_time" },
                { data: "deadline" },
                { data: "business_type" },
                { data: "business_des" },
                { data: "is_show" },
                { data: "rank"},
                { data: "pr_id" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                
                $('td:eq(4)',nRow).html(aData.value+' - '+aData.value2);
            	if (aData.is_show == 1) {
            		$('td:eq(6)', nRow).html('展示中');
            	}

            	if (aData.is_show == 2) {
            		$('td:eq(6)', nRow).html('已隐藏');
            	}
                
                if (aData.is_show == 3) {
                    $('td:eq(6)', nRow).html('已删除');
                }

                $('td:eq(8)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.pr_id + '"><i class="fa fa-search"></i>查看详情</a>');
                
                if (aData.is_show < 3) {
                	$('td:eq(8)', nRow).append('<a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#ModalOrder">设置排序</a>');
                    
                    if (aData.is_show == 1) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                        
                    }
                    if (aData.is_show == 2) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-start cale">显示</a>');
                       
                    }
                    if (aData.is_top == 1) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-hot-false cale">取消置顶</a>');
                       
                    }
                    if (aData.is_top == 2) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-hot-true cale">置顶</a>');
                    }
                    if (aData.is_care == 1) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-care-false cale">取消置精</a>');
                       
                    }
                    if (aData.is_care == 2) {
                        $('td:eq(8)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.pr_id + '" class="btn btn-xs btn-primary js-care-true cale">置精</a>');
                    }
                }
                
                return nRow;
            }
		  }));
		  
		  //查询
		  $("#project_require_btn").click(function(){project_require_table.ajax.reload(null, false)});
		  //查看详情
	      projectRequireTbl.on('click','[data-action]',function(){
	    	  var self 	= $(this);
	          var action 	= self.data("action");
	          var id 		= self.data("id");
	          if(action == 'detail'){
	        	  $.ajax({
	        		  url:project_require_judge_url,
                      data:{id:id,model:'project_require',action:'detail'},
                      success:function(resp){
                    	  loadURL(resp.data.url);
                      }
	        	  })
	          }
	      });
	});
});

//隐藏该发布
$('#project_require').on('click','.js-stop',function(){
	var _this = $(this);
    Dialog.confirm('操作提示', '确定隐藏该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_stop_url,
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
$('#project_require').on('click','.js-start',function(){
	var _this = $(this);
    Dialog.confirm('操作提示', '确定显示该发布吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_start_url,
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

//导出项目管理需求excel
$(".export").on("click",function(){
    //点击跳转之前将查询的数据进行统计
    //发布项目id
    var pr_id 		= $("input[name='pr_id']").val();
    //发布人uid
    var uid 		= $("input[name='uid']").val();
    //展示状态
    var is_show  	= $("input[name='is_show']:checked").val();
    //状态
    var status  	= $("input[name='status']:checked").val();
    window.location.href = "http://fin.jrfacai.com/console/project_require/pr_client?pr_id="+pr_id+"&uid="
                            +uid+"&is_show="+is_show+"&status="+status;
    
    //window.location.href = "http://jr.yingtongkeji.com/console/zfmxes/dingzeng_export";
});

//取消置顶
$("#project_require").on('click','.js-hot-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置顶该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_settop_false_url,
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
$("#project_require").on('click','.js-hot-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置顶该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_settop_true_url,
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
//取消致精
$("#project_require").on('click','.js-care-false',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消置精该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_setcare_false_url,
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
//致精
$("#project_require").on('click','.js-care-true',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定置精该项目吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: project_require_setcare_true_url,
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
    var list_order = $(e.relatedTarget).parents("tr").find("td:eq(6)").text();
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
