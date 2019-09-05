//@ sourceURL=project_list.js
$(document).ready(function () {
    var road_show_table;
    loadModule(['dataTable'], function () {
        var roadshowTbl = $('#road_show');
        road_show_table = roadshowTbl.DataTable($.extend(true,{}, dtAutoOption(roadshowTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[5, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#road_show_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(road_show_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "rs_id"},
                { data: "road_name" },
                { data: "duration" },
                { data: "type" },
                { data: "video_url" },
                { data: "list_order" },
                { data: "status" },
                { data: "rs_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.rs_id + '"><i class="fa fa-search"></i>查看详情</a>');
                $('td:eq(7)', nRow).append('<a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#ModalOrder">设置排序</a>');
                if (aData.status == 1) {
                    $('td:eq(6)', nRow).html('使用中');
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.rs_id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                }else{
                    $('td:eq(6)', nRow).html('已停用');
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.rs_id + '" class="btn btn-xs btn-primary js-start cale">开启</a>');
                }
                
                return nRow;
            }
        }));
        $("#road_show_btn").click(function(){road_show_table.ajax.reload(null, false)});
        roadshowTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:road_show_judge_url,
                    data:{id:id,model:'RoadShow',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#road_show").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该路演的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: road_show_stop_url,
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
$("#road_show").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该路演的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: road_show_start_url,
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
    var list_order = $(e.relatedTarget).parents("tr").find("td:eq(5)").text();
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