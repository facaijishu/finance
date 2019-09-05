//@ sourceURL=project_list.js
$(document).ready(function () {
    var project_clue_table;
    loadModule(['dataTable'], function () {
        var projectClueTbl = $('#project_clue');
        project_clue_table = projectClueTbl.DataTable($.extend(true,{}, dtAutoOption(projectClueTbl), {
            sDom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'>r><'table-responsive overflow't><'dt-toolbar-footer'<'col-sm-5 col-xs-12 hidden-xs'i><'col-sm-7 col-xs-12'p>>",
            "aLengthMenu": [20],
            "autoWidth" : false,
            "ordering": true,
            "aaSorting": [[0, "desc"]], //默认排序
            ajax: function (data, callback, settings) {
                var query_data = {};
                var query_form = $("#project_clue_form").serializeArray();
                if(query_form.length > 0) {
                    for (var i in query_form) {
                        if('string' == typeof query_form[i]['name']) {
                            query_data[query_form[i]['name']] = query_form[i]['value'];
                        }
                    }
                }
                $.getJSON(project_clue_url, toEnableParams(data, query_data), function (resp) {
                    callback(resp);
                });
            },
            columns: [
                { data: "clue_id"},
                { data: "pro_name" },
                { data: "pro_origin"},
                { data: "financing_amount" },
                { data: "status" },
                { data: "create_uid" },
                { data: "create_time" },
                { data: "clue_id"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, fnRowCallback) {
                $('td:eq(3)', nRow).append("万");
                $('td:eq(7)', nRow).html('<a href="javascript:void(0);" class="btn btn-xs btn-info cale" data-action="detail" data-id="' + aData.clue_id + '"><i class="fa fa-search"></i>查看详情</a>');
                if (aData.status == 0) {
                    $('td:eq(4)', nRow).html('未处理');
                }else{
                    $('td:eq(4)', nRow).html('已处理');
                }
                if (aData.pro_origin == 0) {
                    $('td:eq(2)', nRow).html('项目投递');
                }else{
                    $('td:eq(2)', nRow).html('机构中心');
                }
                return nRow;
            }
        }));
        $("#project_clue_btn").click(function(){project_clue_table.ajax.reload(null, false)});
        projectClueTbl.on('click','[data-action]',function(){
            var self = $(this);
            var action = self.data("action");
            var id = self.data("id");
            if(action == 'detail'){
                $.ajax({
                    url:project_clue_judge_url,
                    data:{id:id,model:'project_clue',action:'detail'},
                    success:function(resp){
                        loadURL(resp.data.url);
                    }
                })
            }
        });
    });
});
//导出功能
$(".export").on("click",function(){
 //开始时间
 var create_date1 = $("input[name='create_date1']").val();
 //结束时间
 var create_date2 = $("input[name='create_date2']").val();
 //项目名称
 var pro_name = $("input[name='pro_name']").val();
 //项目来源
 var pro_origin = $("input[name='pro_origin']").val();
 window.location.href = "http://fin.jrfacai.com/console/project_clue/pro_clue_client?create_date1="+create_date1+"&create_date2="
                            +create_date2+"&pro_name="+pro_name+"&pro_origin="+pro_origin;
});
