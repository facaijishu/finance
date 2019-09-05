//@ sourceURL=project_list.js
$(document).ready(function () {
    var zfmxes_table;
    loadModule(['dataTable'], function () {
        var zfmxesTbl = $('#zfmxes');
        zfmxes_table = zfmxesTbl.DataTable($.extend(true,{}, dtAutoOption(zfmxesTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [30],
            "autoWidth" : false,
            "ordering": true,
//            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#zfmxes_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(zfmxes_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "id"},
                { data: "neeq" },
                { data: "neeq" },
                { data: "qr_code" },
                { data: "zfmx_examine" },
                { data: "zfmx_exhibition"},
                { data: "stick"},
                { data: "zfmx_financing"},
                { data: "zfmx_sign"},
                { data: "plannoticeddate"},
                { data: "id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                if(aData.stick == 1){
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-red'>置顶</div>");
                }else{
                    $('td:eq(6)', nRow).html("<div class='badge bg-color-clue'>置顶</div>");
                }
                $('td:eq(1)', nRow).html(aData.sec_uri_tyshortname);
                $('td:eq(3)', nRow).html('<img style="width:50px;cursor:pointer;" src="'+root_path_url+'/project/code/'+aData.qrcode_path+'" alt="二维码" data-toggle="modal" data-target="#ModalImg">');
                if(aData.sublevel == '基础层'){
                    $('td:eq(2)', nRow).html("<div class='badge bg-color-pink'>基</div>");
                }else{
                    $('td:eq(2)', nRow).html("<div class='badge bg-color-blue'>创</div>");
                }
                if(aData.trademethod == '协议'){
                    $('td:eq(2)', nRow).append("<div class='badge bg-color-orange'>竞</div>");
                }else{
                    $('td:eq(2)', nRow).append("<div class='badge bg-color-red'>市</div>");
                }
                $('td:eq(2)', nRow).append(aData.msecucode);
                $('td:eq(10)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.r_id + '"><i class="fa fa-search"></i>查看详情1</a>');
                if (aData.zfmx_examine == 1) {
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-green'>已审核</div>");
                    if(aData.zfmx_exhibition == 1){
                        $('td:eq(5)', nRow).html("<div class='badge bg-color-green'>展示</div>");
                        $('td:eq(10)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.r_id + '" class="btn btn-xs btn-primary js-stop cale">隐藏</a>');
                    }else{
                        $('td:eq(5)', nRow).html("<div class='badge bg-color-clue'>隐藏</div>");
                        $('td:eq(10)', nRow).append('<a href="javascript:void(0);" data-id="' + aData.r_id + '" class="btn btn-xs btn-primary js-start cale">展示</a>');
                    }
                    if(aData.zfmx_financing == 1){
                        $('td:eq(7)', nRow).html("<div class='badge bg-color-clue'>融资结束</div>");
                    }else{
                        $('td:eq(7)', nRow).html("<div class='badge bg-color-green'>融资中</div>");
                    }
                    if(aData.zfmx_sign == 1){
                        $('td:eq(8)', nRow).html("<div class='badge bg-color-green'>是</div>");
                    }else{
                        $('td:eq(8)', nRow).html("<div class='badge bg-color-clue'>否</div>");
                    }
                }else{
                    $('td:eq(4)', nRow).html("<div class='badge bg-color-blue'>待审核</div>");
                    $('td:eq(5)', nRow).html("-");
                    $('td:eq(7)', nRow).html("-");
                    $('td:eq(8)', nRow).html("-");
                }
                return nRow;
            }
        }));
        $("#zfmxes_btn").click(function(){zfmxes_table.ajax.reload(null, false)});
        zfmxesTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:zfmxes_judge_url,
                    data:{id:id,model:'Zfmxes',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
$("#zfmxes").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定取消展示该增发明细吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_hide_url,
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
$("#zfmxes").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定展示该增发明细吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: zfmxes_show_url,
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
//导出功能
$(".export").on("click",function(){
	//点击跳转之前将查询的数据进行统计
	//开始时间
	var create_date1 = $("input[name='create_date1']").val();
	//结束时间
	var create_date2 = $("input[name='create_date2']").val();
	//企业名称
	//var sec_uri_tyshortname = $("input[name='sec_uri_tyshortname']").val();
	//股票代码
	var neeq = $("input[name='neeq']").val();
	//审核状态
	var zfmx_examine = $("input:radio[name='zfmx_examine']:checked").val();
	//展示状态
	var zfmx_exhibition = $("input:radio[name='zfmx_exhibition']:checked").val();
	//融资状态
	var zfmx_financing = $("input:radio[name='zfmx_financing']:checked").val();
	//签约状态
	var zfmx_sign = $("input:radio[name='zfmx_sign']:checked").val();
	/***
	console.log("开始时间:"+create_date1);
	console.log("结束时间:"+create_date2);
	console.log("企业名称:"+sec_uri_tyshortname);
	console.log("股票代码:"+msecucode);
	console.log("审核状态:"+zfmx_examine);
	console.log("展示状态:"+zfmx_exhibition);
	console.log("融资状态:"+zfmx_financing);
	console.log("签约状态:"+zfmx_sign);
	***/
	window.location.href = "http://fin.jrfacai.com/console/zfmxes/dingzeng_export?create_date1="+create_date1+"&create_date2="
							+create_date2+"&neeq="+neeq+"&zfmx_examine="+zfmx_examine
							+"&zfmx_exhibition="+zfmx_exhibition+"&zfmx_financing="+zfmx_financing+"&zfmx_sign="+zfmx_sign;
	
	//window.location.href = "http://jr.yingtongkeji.com/console/zfmxes/dingzeng_export";
});
