$("#through").on('click','.js-stop',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定终止该审核吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: through_stop_url,
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
$("#through").on('click','.js-start',function(){
    var _this = $(this);
    Dialog.confirm('操作提示', '确定还原该审核吗？', function () {
        var id = _this.attr("data-id");
        $.ajax({
            url: through_start_url,
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