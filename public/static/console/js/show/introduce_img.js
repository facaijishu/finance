$('#ModalImg').on('show.bs.modal', function (e) {
    var src = $(e.relatedTarget).attr("src");
    $("#ModalImg img").attr({"src":src});
}).on('hidde.bs.modal', function (e) {
    $("#ModalImg img").attr({"src":''});
});
$("#ModalImg").on('click' , '.fa-rotate-left' , function(){
    var src = $("#ModalImg").find("img").attr("src");
    $.ajax({
        url: url,
        type: 'post',
        data: {src: src , direction: 'left'},
        dataType: 'json',
        success: function (resp) {
            $("#ModalImg").find("img").attr({"src":resp.data});
        }
    })
});
$("#ModalImg").on('click' , '.fa-rotate-right' , function(){
    var src = $("#ModalImg").find("img").attr("src");
    $.ajax({
        url: url,
        type: 'post',
        data: {src: src , direction: 'right'},
        dataType: 'json',
        success: function (resp) {
            $("#ModalImg").find("img").attr({"src":resp.data});
        }
    })
});