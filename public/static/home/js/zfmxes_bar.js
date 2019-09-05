function common(){
	// Define a plugin to provide data labels
	Chart.plugins.register({
		afterDatasetsDraw: function(chart) {
			var ctx_bar = chart.ctx;
			
			chart.data.datasets.forEach(function(dataset, i) {
				var meta = chart.getDatasetMeta(i);
				if (!meta.hidden) {
					if(meta.type != "pie" && meta.type != "line"){
						meta.data.forEach(function(element, index) {
							// Draw the text in black, with the specified font
							ctx_bar.fillStyle = '#666'; 
							var fontSize = 12;
							var fontStyle = 'normal';
							var fontFamily = 'Helvetica Neue';
							ctx_bar.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
							// Just naively convert to string for now
							var dataString = dataset.data[index].toString();
							//console.log(dataString);
							if(dataString != 0){
								// Make sure alignment settings are correct
								ctx_bar.textAlign = 'center';
								ctx_bar.textBaseline = 'middle';
								var padding = 0;
								var position = element.tooltipPosition();
								ctx_bar.fillText(dataString, position.x, position.y - (fontSize / 2) - padding);
							}
							
						});
					}
					
				}
			});
		}
	})
}
//净利润
function aa(lirun_name,lirun_data,lirun_color,lirun_bi_color,lirun_bi_data){
	if (lirun.length != 0) {
		config_bar_lirun = {
			type: 'bar',
			data: {
				labels: lirun_name,
				datasets: [{
						type: 'bar',
						label: '净利润',
						data: lirun_data,
						backgroundColor: lirun_color,
						borderColor: lirun_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,							
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '万元'
							}
						}]
				}
				
			}
			
		};
		if (lirun_bi.length != 0) {
			config_bar_lirun = {
				type: 'bar',
				data: {
					labels: lirun_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: lirun_bi_color,
							backgroundColor: lirun_bi_color,
							borderWidth: 1,
							fill: false,
							data: lirun_bi_data,
							
						}, {
							type: 'bar',
							label: '净利润(元)',
							data: lirun_data,
							backgroundColor: lirun_color,
							borderColor: lirun_color,
							borderWidth: 1,
							
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//}
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '万元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				},
			};
		}
		common();
	}
	if (config_bar_lirun != []) {
        var ctx_bar = document.getElementById('chart-area-1').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_lirun);
    }
}
function bb(shouru_name,shouru_data,shouru_color,shouru_bi_color,shouru_bi_data){
	if (shouru.length != 0) {
		config_bar_shouru = {
			type: 'bar',
			data: {
				labels: shouru_name,
				datasets: [{
						type: 'bar',
						label: '营业收入',
						data: shouru_data,
						backgroundColor: shouru_color,
						borderColor: shouru_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '万元'
							}
						}]
				}
			}
		};
		if (shouru_bi.length != 0) {
			config_bar_shouru = {
				type: 'bar',
				data: {
					labels: shouru_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: shouru_bi_color,
							backgroundColor: shouru_bi_color,
							borderWidth: 2,
							fill: false,
							data: shouru_bi_data
						}, {
							type: 'bar',
							label: '营业收入',
							data: shouru_data,
							backgroundColor: shouru_color,
							borderColor: shouru_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '万元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_shouru != []) {
        var ctx_bar = document.getElementById('chart-area-2').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_shouru);
    }
}
function cc(zichan_name,zichan_data,zichan_color,zichan_bi_color,zichan_bi_data){
	if (zichan.length != 0) {
		config_bar_zichan = {
			type: 'bar',
			data: {
				labels: zichan_name,
				datasets: [{
						type: 'bar',
						label: '每股净资产',
						data: zichan_data,
						backgroundColor: zichan_color,
						borderColor: zichan_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '元'
							}
						}]
				}
			}
		};
		if (zichan_bi.length != 0) {
			config_bar_zichan = {
				type: 'bar',
				data: {
					labels: zichan_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: zichan_bi_color,
							backgroundColor: zichan_bi_color,
							borderWidth: 2,
							fill: false,
							data: zichan_bi_data
						}, {
							type: 'bar',
							label: '每股净资产',
							data: zichan_data,
							backgroundColor: zichan_color,
							borderColor: zichan_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_zichan != []) {
        var ctx_bar = document.getElementById('chart-area-3').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_zichan);
    }
}
function dd(xianjinliu_name,xianjinliu_data,xianjinliu_color,xianjinliu_bi_color,xianjinliu_bi_data){
	if (xianjinliu.length != 0) {
		config_bar_xianjinliu = {
			type: 'bar',
			data: {
				labels: xianjinliu_name,
				datasets: [{
						type: 'bar',
						label: '每股现金流',
						data: xianjinliu_data,
						backgroundColor: xianjinliu_color,
						borderColor: xianjinliu_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '元'
							}
						}]
				}
			}
		};
		if (xianjinliu_bi.length != 0) {
			config_bar_xianjinliu = {
				type: 'bar',
				data: {
					labels: xianjinliu_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: xianjinliu_bi_color,
							backgroundColor: xianjinliu_bi_color,
							borderWidth: 2,
							fill: false,
							data: xianjinliu_bi_data
						}, {
							type: 'bar',
							label: '每股现金流',
							data: xianjinliu_data,
							backgroundColor: xianjinliu_color,
							borderColor: xianjinliu_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_xianjinliu != []) {
        var ctx_bar = document.getElementById('chart-area-4').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_xianjinliu);
    }
}
function ee(maolilv_name,maolilv_data,maolilv_color,maolilv_bi_color,maolilv_bi_data){
	if (maolilv.length != 0) {
		config_bar_maolilv = {
			type: 'bar',
			data: {
				labels: maolilv_name,
				datasets: [{
						type: 'bar',
						label: '毛利率',
						data: maolilv_data,
						backgroundColor: maolilv_color,
						borderColor: maolilv_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '%'
							}
						}]
				}
			}
		};
		if (maolilv_bi.length != 0) {
			config_bar_maolilv = {
				type: 'bar',
				data: {
					labels: maolilv_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: maolilv_bi_color,
							backgroundColor: maolilv_bi_color,
							borderWidth: 2,
							fill: false,
							data: maolilv_bi_data
						}, {
							type: 'bar',
							label: '毛利率',
							data: maolilv_data,
							backgroundColor: maolilv_color,
							borderColor: maolilv_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '%'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_maolilv != []) {
        var ctx_bar = document.getElementById('chart-area-5').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_maolilv);
    }
}
function ff(shouyi_name,shouyi_data,shouyi_color,shouyi_bi_color,shouyi_bi_data){
	if (shouyi.length != 0) {
		config_bar_shouyi = {
			type: 'bar',
			data: {
				labels: shouyi_name,
				datasets: [{
						type: 'bar',
						label: '每股收益',
						data: shouyi_data,
						backgroundColor: shouyi_color,
						borderColor: shouyi_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '元'
							}
						}]
				}
			}
		};
		if (shouyi_bi.length != 0) {
			config_bar_shouyi = {
				type: 'bar',
				data: {
					labels: shouyi_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: shouyi_bi_color,
							backgroundColor: shouyi_bi_color,
							borderWidth: 2,
							fill: false,
							data: shouyi_bi_data
						}, {
							type: 'bar',
							label: '每股收益',
							data: shouyi_data,
							backgroundColor: shouyi_color,
							borderColor: shouyi_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_shouyi != []) {
        var ctx_bar = document.getElementById('chart-area-6').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_shouyi);
    }
}
function gg(gongjijin_name,gongjijin_data,gongjijin_color,gongjijin_bi_color,gongjijin_bi_data){
	if (gongjijin.length != 0) {
		config_bar_gongjijin = {
			type: 'bar',
			data: {
				labels: gongjijin_name,
				datasets: [{
						type: 'bar',
						label: '每股公积金',
						data: gongjijin_data,
						backgroundColor: gongjijin_color,
						borderColor: gongjijin_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '元'
							}
						}]
				}
			}
		};
		if (gongjijin_bi.length != 0) {
			config_bar_gongjijin = {
				type: 'bar',
				data: {
					labels: gongjijin_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: gongjijin_bi_color,
							backgroundColor: gongjijin_bi_color,
							borderWidth: 2,
							fill: false,
							data: gongjijin_bi_data
						}, {
							type: 'bar',
							label: '每股公积金',
							data: gongjijin_data,
							backgroundColor: gongjijin_color,
							borderColor: gongjijin_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_gongjijin != []) {
        var ctx_bar = document.getElementById('chart-area-7').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_gongjijin);
    }
}
function ss(weifenpei_name,weifenpei_data,weifenpei_color,weifenpei_bi_color,weifenpei_bi_data){
	if (weifenpei.length != 0) {
		config_bar_weifenpei = {
			type: 'bar',
			data: {
				labels: weifenpei_name,
				datasets: [{
						type: 'bar',
						label: '每股未分配',
						data: weifenpei_data,
						backgroundColor: weifenpei_color,
						borderColor: weifenpei_color,
						borderWidth: 1
					}]
			},
			options: {
				scales: {
					xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '报告期'
							}
						}],
					yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: '元'
							}
						}]
				}
			}
		};
		if (weifenpei_bi.length != 0) {
			config_bar_weifenpei = {
				type: 'bar',
				data: {
					labels: weifenpei_name,
					datasets: [{
							type: 'line',
							label: '同比',
							borderColor: weifenpei_bi_color,
							backgroundColor: weifenpei_bi_color,
							borderWidth: 2,
							fill: false,
							data: weifenpei_bi_data
						}, {
							type: 'bar',
							label: '每股未分配',
							data: weifenpei_data,
							backgroundColor: weifenpei_color,
							borderColor: weifenpei_color,
							borderWidth: 1
						}]
				},
				options: {
					scales: {
						xAxes: [{
								//display: true,
								//scaleLabel: {
								//	display: true,
								//	labelString: '报告期'
								//},
								gridLines: {
									display:false
								}
							}],
						yAxes: [{
								position:'right',
								display: true,
								scaleLabel: {
									display: false,
									labelString: '元'
								},
								gridLines: {
									display:false
								}
							}]
					}
				}
			};
		}
		common();
	}
	if (config_bar_weifenpei != []) {
        var ctx_bar = document.getElementById('chart-area-8').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_weifenpei);
    }
}

var config_pie = {
    type: 'pie',
    data: {
        datasets: [{
                data: pie_data,
                backgroundColor: pie_color,
                label: 'Dataset 1'
            }],
        labels: pie_name
    }
};
window.onload = function () {
	/***
    if (config_bar_lirun != []) {
        var ctx_bar = document.getElementById('chart-area-1').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_lirun);
    }
    if (config_bar_shouru != []) {
        var ctx_bar = document.getElementById('chart-area-2').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_shouru);
    }
    if (config_bar_zichan != []) {
        var ctx_bar = document.getElementById('chart-area-3').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_zichan);
    }
    if (config_bar_xianjinliu != []) {
        var ctx_bar = document.getElementById('chart-area-4').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_xianjinliu);
    }
    if (config_bar_maolilv != []) {
        var ctx_bar = document.getElementById('chart-area-5').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_maolilv);
    }
    if (config_bar_shouyi != []) {
        var ctx_bar = document.getElementById('chart-area-6').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_shouyi);
    }
    if (config_bar_gongjijin != []) {
        var ctx_bar = document.getElementById('chart-area-7').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_gongjijin);
    }
    if (config_bar_weifenpei != []) {
        var ctx_bar = document.getElementById('chart-area-8').getContext('2d');
        window.myPie = new Chart(ctx_bar, config_bar_weifenpei);
    }
	***/
    var ctx_pie = document.getElementById("myChart").getContext("2d");
    window.myPie2 = new Chart(ctx_pie, config_pie);
};
var swiper = new Swiper('.swiper-container', {
    pagination: '.swiper-pagination',
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',
    paginationClickable: true,
    slidesPerView: 2,
    observer: true,
    observeParents: true
});

function tabs(oa, oc) {
    $(oa).on("click", function () {
		if(oa == ".info-son-tab a"){
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		}
        $(oa).removeClass("actived");
        $(this).addClass("actived");
        $(oc).css({"display": "none","position": "absolute", "left": "-999px"});
        $(oc).eq($(this).index()).css({"display": "block", "position": "relative", "left": "0"});
		$(".information2").readmore({
			moreLink: '<a href="###" class="btn-open2 btn-change"><i class="fa fa-angle-down"></i></a>',
			lessLink: '<a href="###" class="btn-open2 btn-change"><i class="fa fa-angle-up"></i></a>',
			maxHeight: 30,
			embedCSS: true,
			expandedClass:"op"
		});
		
    });
};
tabs(".info-tab a", ".info-content");
tabs(".info-son-tab a", ".info-son-con");
tabs(".tab-1 span", ".tab-box");
$(".info-son-con[name='ok']").show();

$(".info-son-tab a[name='false']").css({"display":"none"});
$(".info-son-con[name='false']").css({"display":"none"});
$(".info-content").hide();
$(".info-content").eq(0).show();
$(".info-son-tab a[name='ok']").eq(0).addClass("actived");
$(".info-son-con").hide();
$(".info-son-con[name='ok']").eq(0).show();
$(".list-1 > .list4 li:even").addClass("even");
$(".list-2 li:odd").addClass("odd");
$(".toggle dl dd").hide();
$(".toggle dl dt").click(function () {
	$(".toggle dl dd").not($(this).next()).hide();
	$(".toggle dl dt").not($(this).next()).removeClass("current");
	$(this).next().css("border-bottom", "1px solid #b3b3b3").slideToggle(500);
	$(this).toggleClass("current");
});

$('.information').readmore({
	moreLink: '<a href="###" class="btn-open btn-change" style="border-top:none;"><i class="fa fa-angle-down"></i><span>展开</span></a>',
	lessLink: '<a href="###" class="btn-open btn-change" style="border-top:none;"><i class="fa fa-angle-up"></i><span>收起</span></a>',
	maxHeight: 88
});

$(".information2").readmore({
	moreLink: '<a href="###" class="btn-open2 btn-change"><i class="fa fa-angle-down"></i></a>',
	lessLink: '<a href="###" class="btn-open2 btn-change"><i class="fa fa-angle-up"></i></a>',
	maxHeight: 30,
	embedCSS: true,
	expandedClass:"op"
});

//净利润	
aa(lirun_name,lirun_data,lirun_color,lirun_bi_color,lirun_bi_data);
$(".cli .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				aa(lirun_name,lirun_data,lirun_color,lirun_bi_color,lirun_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				aa(lirun_name_zhong,lirun_zhong,lirun_color,lirun_bi_color,lirun_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//营业收入
bb(shouru_name,shouru_data,shouru_color,shouru_bi_color,shouru_bi_data);
$(".cli1 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				bb(shouru_name,shouru_data,shouru_color,shouru_bi_color,shouru_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				bb(yingye_time_zhong,yingye_zhong,shouru_color,shouru_bi_color,ying_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//每股净资产
cc(zichan_name,zichan_data,zichan_color,zichan_bi_color,zichan_bi_data);
$(".cli2 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				cc(zichan_name,zichan_data,zichan_color,zichan_bi_color,zichan_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				cc(jing_time,jing_zhong,zichan_color,zichan_bi_color,jing_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//每股现金流
dd(xianjinliu_name,xianjinliu_data,xianjinliu_color,xianjinliu_bi_color,xianjinliu_bi_data);
$(".cli3 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				dd(xianjinliu_name,xianjinliu_data,xianjinliu_color,xianjinliu_bi_color,xianjinliu_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				dd(xianjin_time_zhong,xianjin_zhong,xianjinliu_color,xianjinliu_bi_color,xianjin_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//毛利率
ee(maolilv_name,maolilv_data,maolilv_color,maolilv_bi_color,maolilv_bi_data);
$(".cli4 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				ee(maolilv_name,maolilv_data,maolilv_color,maolilv_bi_color,maolilv_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				ee(lilv_time_zhong,lilv_zhong,maolilv_color,maolilv_bi_color,lilv_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//每股收益
ff(shouyi_name,shouyi_data,shouyi_color,shouyi_bi_color,shouyi_bi_data);
$(".cli5 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				ff(shouyi_name,shouyi_data,shouyi_color,shouyi_bi_color,shouyi_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				ff(meigu_time_zhong,meigu_zhong,shouyi_color,shouyi_bi_color,meigu_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//每股公积金
gg(gongjijin_name,gongjijin_data,gongjijin_color,gongjijin_bi_color,gongjijin_bi_data);
$(".cli6 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				gg(gongjijin_name,gongjijin_data,gongjijin_color,gongjijin_bi_color,gongjijin_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				gg(gong_time_zhong,gong_zhong,gongjijin_color,gongjijin_bi_color,gong_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
//每股未分配
ss(weifenpei_name,weifenpei_data,weifenpei_color,weifenpei_bi_color,weifenpei_bi_data);
$(".cli7 .text").on("click",function(){
	var _this = $(this);
	if(!_this.hasClass("op")){
		_this.addClass("op");
		_this.parent().find(".lie").show();
	}else{
		_this.removeClass("op");
		_this.parent().find(".lie").hide();
	}
	_this.parent().find(".lie").children().each(function(){
		$(this).on("click",function(){
			var txt = $(this).attr("data-index");
			$(this).parents(".change").find("span").html($(this).html());
			var on = $(this).parents(".info-son-con");
			if(txt == 1){
				//年报
				ss(weifenpei_name,weifenpei_data,weifenpei_color,weifenpei_bi_color,weifenpei_bi_data);
				on.find(".zhong").hide();
				on.find(".nian").show();
			}else{
				ss(weifenpei_time_zhong,weifenpei_zhong,weifenpei_color,weifenpei_bi_color,weifenpei_bi_zhong);
				on.find(".zhong").show();
				on.find(".nian").hide();
			}
			$(".lie").hide();
			$(".f-h4 .text").removeClass("op");
		});
	});
});
