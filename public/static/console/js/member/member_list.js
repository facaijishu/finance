//@ sourceURL=project_list.js
$(document).ready(function () {
    var member_table;
    loadModule(['dataTable'], function () {
        var memberTbl = $('#member');
        member_table = memberTbl.DataTable($.extend(true,{}, dtAutoOption(memberTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [10],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#member_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(member_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "uid"},
                { data: "userName" },
                { data: "userPhoto" },
                { data: "realName" },
                { data: "userPhone" },
                { data: "openId" },
                { data: "is_follow" },
                { data: "userType" },
                { data: "superior" },
                { data: "createTime" },
                { data: "lastTime" },
                { data: "pullback" },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(2)', nRow).html('<img style="width:60px;cursor:pointer;" src="'+aData.userPhoto+'" data-toggle="modal" data-target="#ModalImg">');
                $('td:eq(5)', nRow).addClass("openid");
                if(aData.is_follow == 1){
                    $('td:eq(6)', nRow).html("是");
                }else{
                    $('td:eq(6)', nRow).html("否");
                }
                if(aData.userType == 0){
                    $('td:eq(7)', nRow).html("游客");
                }else if(aData.userType == 1){
                    $('td:eq(7)', nRow).html("绑定用户");
                }else{
                    $('td:eq(7)', nRow).html("认证合伙人");
                }
                if(aData.pullback == 1){
                    $('td:eq(11)', nRow).html("<button class='in_nc' style='color:#fff;background-color:#404040;border:1px solid #404040;outline:none;' data='2'>启用</button>");
                }else{
                    $('td:eq(11)', nRow).html("<button class='in_nc' style='color:#fff;background-color:#404040;border:1px solid #404040;outline:none;' data='1'>禁用</button>");
                }
                return nRow;
            }
        }));
        $("#member_btn").click(function(){member_table.ajax.reload(null, false)});
    });
    
    
    //导出项目管理需求excel
    $(".export").on("click",function(){
        //点击跳转之前将查询的数据进行统计
        var create_date1 		= $("input[name='create_date1']").val();
        var create_date2 		= $("input[name='create_date2']").val();
        var last_date1 			= $("input[name='last_date1']").val();
        var last_date2 			= $("input[name='last_date2']").val();
        var userName 			= $("input[name='userName']").val();
        var realName 			= $("input[name='realName']").val();
        var userPhone 			= $("input[name='userPhone']").val();
        var openId 				= $("input[name='openId']").val();
        var is_follow 			= $("input[name='is_follow']:checked").val(); 
        var userType  			= $("input[name='userType']:checked").val();
      
        window.location.href = "http://fin.jrfacai.com/console/member/exportMember?create_date1="+create_date1+"&create_date2="
                                +create_date2+"&last_date1="+last_date1+"&last_date2="+last_date2+"&status="+status+"&userName="+userName+"&realName="
                                +realName+"&userPhone="+userPhone+"&openId="+openId+"&is_follow="+is_follow+"&userType="+userType;
    });

    
});
