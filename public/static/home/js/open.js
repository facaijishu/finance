
$(".box a").click(function(){

	var id = $(this).attr("data-id");

	var href = $(this).attr("data-href");

	var openid = $(this).attr("data-openId");

	$.ajax({

		url : "http://fin.jrfacai.com/home/author_controller/collection_study",

		data : {id : id , openid : openid ,url:href},

		type : "POST",

		success : function(resp){

		console.log(resp);

		if(resp == '阅读记录添加成功'){
		//location.href = "http://www.baidu.com";
		location.href = href;
		}else{
		   return false;
		}

		}

	});

});
