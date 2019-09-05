//@ sourceURL=project_list.js
$(document).ready(function () {
    var self_customer_service_table;
    loadModule(['dataTable'], function () {
        var selfcustomerServiceTbl = $('#self_customer_service');
        self_customer_service_table = selfcustomerServiceTbl.DataTable($.extend(true,{}, dtAutoOption(selfcustomerServiceTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#self_customer_service_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(self_customer_service_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "scs_id"},
                { data: "kf_account" },
                { data: "uid" },
                { data: "key_number" },
                { data: "kf_headimgurl" },
                { data: "kf_nick" },
                { data: "kf_openId" },
//                { data: "kf_qr_code" },
                { data: "create_time" },
                { data: "status" },
                { data: "scs_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.status == 1){
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-green'>正常</div>");
                    $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.scs_id + '"><i class="fa fa-edit"></i>修改</a><a href="javascript:void(0);" class="btn btn-xs btn-danger btn-disable cale" data-id="' + aData.scs_id + '">禁用</a>');
                }else{
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-blue'>不可用</div>");
                    $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.scs_id + '"><i class="fa fa-edit"></i>修改</a><a href="javascript:void(0);" class="btn btn-xs btn-success btn-recover cale " data-id="' + aData.scs_id + '">启用</a>');
                }
                $('td:eq(4)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+root_path_url+'/'+aData.kf_headimgurl+'" data-toggle="modal" data-target="#ModalImg">');                                                                                                                                                                       /*
                $('td:eq(9)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-warning cale" data-action="edit" data-id="' + aData.scs_id + '"><i class="fa fa-edit"></i>修改</a><a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-id="' + aData.scs_id + '">禁用</a>');*/
                return nRow;
            }
        }));
        $("#self_customer_service_btn").click(function(){self_customer_service_table.ajax.reload(null, false)});
        selfcustomerServiceTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'edit'){
                $.ajax({
                    url:self_customer_service_judge_url,
                    data:{id:id,model:'SelfCustomerService',action:'edit'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#self_customer_service").on('click','.btn-disable',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定禁用该客服吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: self_customer_service_delete_url,
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
//恢复客服
$("#self_customer_service").on('click','.btn-recover',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定启用该客服吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: self_customer_service_recover_url,
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
