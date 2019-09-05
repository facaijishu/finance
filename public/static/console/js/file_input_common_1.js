/*
* 初始化fileInput控件（第一次初始化）
* path 上传类型 spoiler,project ...
* type 上传类型 image/mp4 ...
* initValue 初始化图片值  多图以,分割
* initConfig 初始化图片配置值  多图以{}分割
* item_num  调用fileinput的元素
* form_item  调用fileinput的表单元素
* file_count  图片上传数量限定
* upload_url  图片上传地址
* delete_url  图片删除地址
* initCaption  input默认名称
*/
function initFileInput(form_item, item_num, path, type,  upload_url, file_count, initCaption, initValue, initConfig) {
    var control;
//    var idName;
    var uploadUrl;
    var initUrls = [];
    var initCaptions = '';
    var maxFileCount;
    var initialPreviewConfig = [];
    if (initValue != undefined) {
        initUrls = initValue;
    }
    if (initConfig != undefined) {
        initialPreviewConfig = initConfig;
    }
    if (initCaption != undefined) {
        initCaptions = initCaption;
    }
    control = item_num;
//    if (item_num == 'this') {
//        control = $(this);
//    }else {
//        idName = "#" + item_num;
//        control = $(idName);
//    }
    maxFileCount = file_count;
    uploadUrl = upload_url;
    control.fileinput({
        initialPreview: initUrls,
        initialCaption: initCaptions,
        initialPreviewAsData: true,
        overwriteInitial: false,
        language: 'zh', //设置语言
        uploadUrl: uploadUrl, //上传的地址(访问接口地址)
        allowedFileExtensions: type,//接收的文件后缀
        uploadExtraData:{type:  path},
        uploadAsync: true, //默认异步上传
        showUpload: true, //是否显示上传按钮
        showRemove: true, //显示移除按钮
        showPreview: true, //是否显示预览
        showCaption: true,//是否显示标题
        browseClass: "btn btn-primary", //按钮样式
//        deleteUrl: "/product/delete-image",
        dropZoneEnabled: true,//是否显示拖拽区域
        //minImageWidth: 50, //图片的最小
        //minImageHeight: 50,//图片的最小高度
        //maxImageWidth: 1000,//图片的最大宽度
        //maxImageHeight: 1000,//图片的最大高度
        maxFileSize: 0,//单位为kb，如果为0表示不限制文件大小
        maxFileCount: maxFileCount, //表示允许同时上传的最大文件个数
        enctype: 'multipart/form-data',
        validateInitialCount: true,
        initialPreviewConfig: initialPreviewConfig,
        previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
        msgFilesTooMany: "选择上传的文件数量({n}) 超过允许的最大数值{m}！",
//        slugCallback: function (filename) {
//            return filename.replace('(', '_').replace(']', '_');
//        }
    })
        .on("filebatchselected", function (event, files) {         //默认上传
            if (file_count == 1) {
                control.fileinput("upload");
            }
        })
        .on("fileuploaded", function (event, data) {       //上传回调事件
            if (file_count == 1) {
                if(!data.response.code){
                    Dialog.error('上传失败', data.response.msg, 2000);
                }else {
                    control.parents(".file-input").siblings("input[class='hide']").attr({"value":data.response.data.id});
                }
            }else {
                if(!data.response.code){
                    Dialog.error('上传失败', data.response.msg, 2000);
                }else {
                    var img = control.parents(".file-input").siblings("input[class='hide']").val()+data.response.data.id+'-'+data.response.data.name+',';
                    control.parents(".file-input").siblings("input[class='hide']").val(img);
                }
            }
//            var name = control.parents(".file-input").siblings("input[class='hide']").attr("name");
//            $('#'+form_item).data('bootstrapValidator').updateStatus(name, 'NOT_VALIDATED', null).validateField(name); 
//            $("#"+form_item).data('bootstrapValidator').destroy();
//            $('#'+form_item).data('bootstrapValidator', null);
//            setValidator();
        })
        .on('filesuccessremove', function (event, key) {      //删除回调事件
            if (file_count == 1) {
                control.parents(".file-input").siblings("input[class='hide']").val('');
            }else {
                var name = $("#"+key).find(".file-footer-caption").attr("title");
                var str = control.parents(".file-input").siblings("input[class='hide']").val();
                var arr = str.split(',');
                for(var i = 0 ; i < arr.length ; i ++ ){
                    var title = arr[i].split('-');
                    if(name == title[1]){
                        arr.splice(i , 1);
                    }
                }
                str = arr.join(',');
                control.parents(".file-input").siblings("input[class='hide']").val(str);
            }
            console.log(control.parents(".file-input").siblings("input[class='hide']").val());
        })
        .on('filedeleted', function (event, key) {   //初始化图片删除事件
            if (file_count == 1) {
                control.parents(".file-input").siblings("input[class='hide']").val('');
            }else {
                var key = 'init_'+key;
                var str = control.parents(".file-input").siblings("input[class='hide']").val();
                var arr = str.split(',');
                for(var i = 0 ; i < arr.length ; i ++ ){
                    var title = arr[i].split('-');
                    if(key == title[1]){
                        arr.splice(i , 1);
                    }
                }
                str = arr.join(',');
                control.parents(".file-input").siblings("input[class='hide']").val(str);
            }
            console.log(control.parents(".file-input").siblings("input[class='hide']").val());
        })
        .on('filecleared', function (event, key) {
            control.parents(".file-input").siblings("input[class='hide']").val('');
            console.log(control.parents(".file-input").siblings("input[class='hide']").val());
        })
        .on('fileuploaderror', function (event, param, msg) {
            Dialog.error('上传失败', msg, 2000);
        })
    ;
}

