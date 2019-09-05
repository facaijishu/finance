/* 
 *  summernote.js
 */
$("#summernote").summernote({
    height: 400,
    focus: true,   
    lang:'zh-CN',
    toolbar: [
//        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
//        ['fontname', ['fontname']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ],
    callbacks:{
        onImageUpload: function(files, editor, $editable) {
            sendFile(files[0],editor,$editable);  
        },
        onChange: function() {
            var content = $(this).summernote("code");
            $(this).siblings("input[name='summernote']").val(content);
            $("#content form").data("bootstrapValidator").updateStatus('summernote', 'NOT_VALIDATED', null).validateField('summernote');
        }
    }
});
function sendFile(file, editor, $editable) {
    var data = new FormData();  
    data.append("file", file);  
    $.ajax({  
        data : data,  
        type : "POST",  
        url : send_file_url, //图片上传出来的url，返回的是图片上传后的路径，http格式  
        cache : false,  
        contentType : false,  
        processData : false,  
        dataType : "json",  
        success: function(data) {
            $('#summernote').summernote('insertImage', data.data.url , data.data.name);  
        },  
        error:function(){  
            alert("上传失败");  
        }  
    });  
}
