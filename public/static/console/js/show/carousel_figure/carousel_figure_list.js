//@ sourceURL=project_list.js
$(document).ready(function () {
    var carousel_figure_table;
    loadModule(['dataTable'], function () {
        var CarouselFigureTbl = $('#carousel_figure');
        carousel_figure_table = CarouselFigureTbl.DataTable($.extend(true,{}, dtAutoOption(CarouselFigureTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[6, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#carousel_figure_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(carousel_figure_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "cf_id"},
                { data: "title" },
                { data: "img" },
                { data: "list_order" },
//                { data: "url" },
//                { data: "introduce" },
                { data: "create_time" },
                { data: "create_uid" },
                { data: "status" },
                { data: "cf_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.cf_id + '"><i class="fa fa-edit"></i>修改</a>');
                $('td:eq(2)', nRow).html("<img style='width:100px;cursor:pointer;' src='"+root_path_url+'/'+aData.img+"' data-toggle='modal' data-target='#ModalImg'>");
                if (aData.status == 1) {
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-green'>使用中</div>");
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.cf_id + '" class="btn btn-xs btn-primary js-stop cale">停用</a>');
                }else{
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-blue'>已停用</div>");
                    $('td:eq(7)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.cf_id + '" class="btn btn-xs btn-primary js-start cale">开启</a>');
                }
                return nRow;
            }
        }));
        $("#carousel_figure_btn").click(function(){carousel_figure_table.ajax.reload(null, false)});
        CarouselFigureTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'edit'){
                $.ajax({
                    url:carousel_figure_judge_url,
                    data:{id:id,model:'CarouselFigure',action:'edit'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#carousel_figure").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定停止该轮播图的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: carousel_figure_stop_url,
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
$("#carousel_figure").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定开启该轮播图的使用吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: carousel_figure_start_url,
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
