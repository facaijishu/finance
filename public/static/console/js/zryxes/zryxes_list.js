//@ sourceURL=project_list.js
$(document).ready(function () {
    var zryxes_table;
    loadModule(['dataTable'], function () {
        var zryxesTbl = $('#zryxes');
        zryxes_table = zryxesTbl.DataTable($.extend(true,{}, dtAutoOption(zryxesTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [30],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#zryxes_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(zryxes_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "secucode" },
                { data: "sec_uri_tyshortname" },
                { data: "qr_code" },
                { data: "direction" },
                { data: "price" },
                { data: "zryx_exhibition"},
                { data: "zryx_financing"},
                { data: "zryx_sign"},
                { data: "boutique"},
                { data: "create_time"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                //大宗项目融资没有结束的，显示的都加上推送消息
                $('td:eq(3)', nRow).html('<img style="width:50px;cursor:pointer;" src="'+root_path_url+'/project/code/'+aData.qrcode_path+'" alt="二维码" data-toggle="modal" data-target="#ModalImg">');
                if(aData.sublevel == '基础层'){
                    $('td:eq(1)', nRow).html("<div class='badge bg-color-pink'>基</div>");
                }else{
                    $('td:eq(1)', nRow).html("<div class='badge bg-color-blue'>创</div>");
                }
                if(aData.trademethod == '协议'){
                    $('td:eq(1)', nRow).append("<div class='badge bg-color-orange'>竞</div>");
                }else{
                    $('td:eq(1)', nRow).append("<div class='badge bg-color-red'>市</div>");
                }
                $('td:eq(1)', nRow).append(aData.secucode);
                $('td:eq(11)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.id + '"><i class="fa fa-search"></i>查看详情</a>');
                if(aData.zryx_exhibition == 1){
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                    $('td:eq(11)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                }else{
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-clue'>隐藏</div>");
                    $('td:eq(11)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-start cale">展示</a>');
                }
                if(aData.zryx_financing == 1){
                    $('td:eq(7)', nRow).html("<div class='badge bg-color-clue'>融资结束</div>");
                }else{
                    $('td:eq(7)', nRow).html("<div class='badge bg-color-green'>融资中</div>");
                }
                if(aData.boutique == 1){
                    $('td:eq(9)', nRow).html("<div class='badge bg-color-red'>精品</div>");
                    $('td:eq(11)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-stop-boutique cale">取消致精</a>');
                }else{
                    $('td:eq(9)', nRow).html("-");
                    $('td:eq(11)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.id + '" class="btn btn-xs btn-primary js-boutique cale">致精</a>');
                }
                if(aData.zryx_sign == 1){
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-green'>是</div>");
                }else{
                    $('td:eq(8)', nRow).html("<div class='badge bg-color-clue'>否</div>");
                }
                if(aData.zryx_financing != 1){
                    $('td:eq(11)', nRow).append('<a href="javascript:void(0);" class="btn btn-xs btn-danger cale" data-action="sendinfo" data-id="' + aData.id + '"><i class="fa fa-search"></i>推送消息</a>');
                }
                return nRow;
            }
        }));
        $("#zryxes_btn").click(function(){zryxes_table.ajax.reload(null, false)});
        zryxesTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:zryxes_judge_url,
                    data:{id:id,model:'Zryxes',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }else if(action == 'sendinfo'){
                $.ajax({
                    url:zryxes_judge_url,
                    data:{id:id,model:'Zryxes',action:'detail2'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#zryxes").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消展示该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_hide_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('取消展示成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('取消展示失败', resp.msg);
                }
            }
        })
    });
});
$("#zryxes").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定展示该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_show_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('展示成功', resp.msg, 2000, function () {
                        location.reload();
                    });
                } else {
                    Dialog.error('展示失败', resp.msg);
                }
            }
        })
    });
});
$("#zryxes").on('click','.js-boutique',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定致精该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_boutique_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('致精成功', resp.msg, 2000, function () {
                        loadURL(resp.data.url);
                    });
                } else {
                    Dialog.error('致精失败', resp.msg);
                }
            }
        })
    });
});
//取消致精
$("#zryxes").on('click','.js-stop-boutique',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消致精该大宗交易吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zryxes_stop_boutique_url,
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
                if (resp.code) {
                    Dialog.success('取消致精成功', resp.msg, 2000, function () {
                        loadURL(resp.data.url);
                    });
                } else {
                    Dialog.error('取消致精失败', resp.msg);
                }
            }
        })
    });
});
//导出功能
$(".export").on("click",function(){
	//点击跳转之前将查询的数据进行统计
	//开始时间
	var create_date1 = $("input[name='create_date1']").val();
	//结束时间
	var create_date2 = $("input[name='create_date2']").val();
	//企业名称
	var sec_uri_tyshortname = $("input[name='sec_uri_tyshortname']").val();
	//股票代码
	var secucode = $("input[name='secucode']").val();
	//展示状态
	var zryx_exhibition = $("input:radio[name='zryx_exhibition']:checked").val();
	//融资状态
	var zryx_financing = $("input:radio[name='zryx_financing']:checked").val();
	//签约状态
	var zryx_sign = $("input:radio[name='zryx_sign']:checked").val();
	/***
	console.log("开始时间:"+create_date1);
	console.log("结束时间:"+create_date2);
	console.log("企业名称:"+sec_uri_tyshortname);
	console.log("股票代码:"+secucode);
	console.log("审核状态:"+zfmx_examine);
	console.log("展示状态:"+zryx_exhibition);
	console.log("融资状态:"+zryx_financing);
	console.log("签约状态:"+zryx_sign);
	***/
	window.location.href = "http://fin.jrfacai.com/console/zryxes/dazong_client?create_date1="+create_date1+"&create_date2="
							+create_date2+"&sec_uri_tyshortname="+sec_uri_tyshortname+"&secucode="+secucode
							+"&zryx_exhibition="+zryx_exhibition+"&zryx_financing="+zryx_financing+"&zryx_sign="+zryx_sign;
	
	//window.location.href = "http://jr.yingtongkeji.com/console/zfmxes/dingzeng_export";
});
