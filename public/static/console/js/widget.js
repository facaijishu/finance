var main_html = '<div class="widget"> ' +
    '<span class="setting">' +
    '<a class="btn" href="javascript:void(0);" onclick="refresh()" ><i class="fa fa-refresh"></i></a>' +
    '<a class="btn" href="javascript:void(0);" onclick="goBack()" ><i class="fa fa-reply"></i></a>' +
    '<a class="btn" href="javascript:void(0);" id="setting"><i class="fa fa-cogs fa-spin txt-color-blueDark"></i></a>' +
    '</span> ' +
    '<form>' +
    '<legend class="no-padding margin-bottom-10">系统布局</legend>' +
    '<section>' +
    '<label><input name="subscription" id="smart-fixed-header" type="checkbox" class="checkbox style-0"><span>固定头部</span></label>' +
    '<label><input type="checkbox" name="terms" id="smart-fixed-navigation" class="checkbox style-0"><span>固定菜单</span></label>' +
    '<label><input type="checkbox" name="terms" id="smart-fixed-ribbon" class="checkbox style-0"><span>固定功能区</span></label>' +
    '<label><input type="checkbox" name="terms" id="smart-fixed-footer" class="checkbox style-0"><span>固定脚部</span></label>' +
    '<label><input type="checkbox" name="terms" id="smart-fixed-container" class="checkbox style-0"><span>窄屏显示</span></label>' +
    '<label style="display:block;"><input type="checkbox" name="terms" id="smart-rtl" class="checkbox style-0"><span>左右转换</span></label>' +
    '<label style="display:block;"><input type="checkbox" id="smart-topmenu" class="checkbox style-0"><span>菜单置顶</span></label>' +
    '<label style="display:block;"><input type="checkbox" id="colorblind-friendly" class="checkbox style-0"><span>色盲模式</span></label>' +
    '<span id="smart-bgimages"></span>' +
    '</section>' +
    '<section>' +
    '<h6 class="margin-top-10 semi-bold margin-bottom-5">清除设置</h6>' +
    '<a href="javascript:void(0);" class="btn btn-xs btn-block btn-primary" id="reset-smart-widget"><i class="fa fa-refresh"></i> 恢复设置</a>' +
    '</section> ' +
    '<h6 class="margin-top-10 semi-bold margin-bottom-5">主题</h6>' +
    '<section id="smart-styles">' +
    '<a href="javascript:void(0);" id="smart-style-0" data-skinlogo="img/logo.png" class="btn btn-block btn-xs txt-color-white margin-right-5" style="background-color:#4E463F;"><i class="fa fa-check fa-fw" id="skin-checked"></i>系统默认</a>' +
    '<a href="javascript:void(0);" id="smart-style-1" data-skinlogo="img/logo-white.png" class="btn btn-block btn-xs txt-color-white" style="background:#3A4558;">暗黑高雅</a>' +
    '<a href="javascript:void(0);" id="smart-style-2" data-skinlogo="img/logo-blue.png" class="btn btn-xs btn-block txt-color-darken margin-top-5" style="background:#fff;">高亮主题</a>' +
    '<a href="javascript:void(0);" id="smart-style-3" data-skinlogo="img/logo-pale.png" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:#f78c40">谷歌风格</a>' +
    '<a href="javascript:void(0);" id="smart-style-4" data-skinlogo="img/logo-pale.png" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background: #bbc0cf; border: 1px solid #59779E; color: #17273D !important;">色素拼接</a> ' +
    '<a href="javascript:void(0);" id="smart-style-5" data-skinlogo="img/logo-pale.png" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background: rgba(153, 179, 204, 0.2); border: 1px solid rgba(121, 161, 221, 0.8); color: #17273D !important;">玻璃主题 </a>' +
    '<a href="javascript:void(0);" id="smart-style-6" data-skinlogo="img/logo-pale.png" class="btn btn-xs btn-block txt-color-white margin-top-6" style="background: #2196F3; border: 1px solid rgba(0, 0, 0, 0.3); color: #FFF !important;">清澈蓝 <sup>beta</sup> </a>' +
    '</section>' +
    '</form> ' +
    '</div>';
$("#main").append(main_html);
var smartbgimage = "<h6 class='margin-top-10 semi-bold'>Background</h6>" +
    "<img src='img/pattern/graphy-xs.png' data-htmlbg-url='img/pattern/graphy.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'>" +
    "<img src='img/pattern/tileable_wood_texture-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/tileable_wood_texture.png' class='margin-right-5 bordered cursor-pointer'>" +
    "<img src='img/pattern/sneaker_mesh_fabric-xs.png' width='22' height='22' data-htmlbg-url='img/pattern/sneaker_mesh_fabric.png' class='margin-right-5 bordered cursor-pointer'>" +
    "<img src='img/pattern/nistri-xs.png' data-htmlbg-url='img/pattern/nistri.png' width='22' height='22' class='margin-right-5 bordered cursor-pointer'>" +
    "<img src='img/pattern/paper-xs.png' data-htmlbg-url='img/pattern/paper.png' width='22' height='22' class='bordered cursor-pointer'>";

var w_storage = window.localStorage;

var defaultSetting = { fixed_header: 0, fixed_navigation: 0, fixed_ribbon: 0, fixed_footer: 0, rtl: 0, topmenu: 0, fixed_container: 0, colorblind_friendly: 0 };

w_storage.widgetSetting = w_storage.widgetSetting == undefined || w_storage.widgetSetting == 'undefined' ? JSON.stringify(defaultSetting) : w_storage.widgetSetting;
widget = (function (widget) {
    widget.set_fixed_header = function () {
        if(this.setting('fixed_header')) {
            $.root_.addClass("fixed-header");
            $('input[type="checkbox"]#smart-fixed-header').prop("checked", !0);
        } else {
            $('input[type="checkbox"]#smart-fixed-ribbon').prop("checked", !1);
            $('input[type="checkbox"]#smart-fixed-navigation').prop("checked", !1);
            $.root_.removeClass("fixed-header");
            $.root_.removeClass("fixed-navigation");
            $.root_.removeClass("fixed-ribbon");
        }
    };
    widget.set_fixed_navigation = function () {
        if(this.setting('fixed_navigation')) {
            $('input[type="checkbox"]#smart-fixed-header').prop("checked", !0);
            $('input[type="checkbox"]#smart-fixed-navigation').prop("checked", !0);
            $.root_.addClass("fixed-header"), $.root_.addClass("fixed-navigation");
            $('input[type="checkbox"]#smart-fixed-container').prop("checked", !1);
            $.root_.removeClass("container");
        } else {
            $('input[type="checkbox"]#smart-fixed-ribbon').prop("checked", !1);
            $.root_.removeClass("fixed-navigation");
            $.root_.removeClass("fixed-ribbon");
        }
    };
    widget.set_fixed_ribbon = function () {
        if(this.setting('fixed_ribbon')) {
            $('input[type="checkbox"]#smart-fixed-header').prop("checked", !0);
            $('input[type="checkbox"]#smart-fixed-navigation').prop("checked", !0);
            $('input[type="checkbox"]#smart-fixed-ribbon').prop("checked", !0);
            $.root_.addClass("fixed-header");
            $.root_.addClass("fixed-navigation");
            $.root_.addClass("fixed-ribbon");
            $('input[type="checkbox"]#smart-fixed-container').prop("checked", !1);
            $.root_.removeClass("container");
        } else {
            $.root_.removeClass("fixed-ribbon");
        }
    };
    widget.set_fixed_footer = function () {
        if(this.setting('fixed_footer')) {
            $('input[type="checkbox"]#smart-fixed-footer').prop("checked", !0);
            $.root_.addClass("fixed-page-footer");
        } else {
            $.root_.removeClass("fixed-page-footer");
        }
    };
    widget.set_rtl = function () {
        if(this.setting('rtl')) {
            $('input[type="checkbox"]#smart-rtl').prop("checked", !0);
            $.root_.addClass("smart-rtl");
        } else {
            $.root_.removeClass("smart-rtl");
        }
    };
    widget.set_topmenu = function () {
        if(this.setting('topmenu')) {
            localStorage.setItem("sm-setmenu", "top");
            $("#smart-topmenu").prop("checked", !0);
        } else {
            localStorage.setItem("sm-setmenu", "left");
            $("#smart-topmenu").prop("checked", !1);
        }
    };
    widget.set_colorblind_friendly = function () {
        if(this.setting('colorblind_friendly')) {
            $.root_.addClass("colorblind-friendly");
            $('input[type="checkbox"]#colorblind-friendly').prop("checked", !0);
        } else {
            $.root_.removeClass("colorblind-friendly");
        }
    };
    widget.set_fixed_container = function () {
        if(this.setting('fixed_container')) {
            $.root_.addClass("container");
            $('input[type="checkbox"]#smart-fixed-ribbon').prop("checked", !1);
            $.root_.removeClass("fixed-ribbon");
            $('input[type="checkbox"]#smart-fixed-navigation').prop("checked", !1);
            $.root_.removeClass("fixed-navigation");
            $('input[type="checkbox"]#smart-fixed-container').prop("checked", !0);
            if(smartbgimage) {
                $("#smart-bgimages").append(smartbgimage).fadeIn(1e3);
                $("#smart-bgimages img").bind("click", function () {
                    var a = $(this), b = $("html");
                    bgurl = a.data("htmlbg-url");
                    b.css("background-image", "url(" + bgurl + ")")
                });
                smartbgimage = null
            } else {
                $("#smart-bgimages").fadeIn(1e3);
            }
        } else {
            $.root_.removeClass("container"), $("#smart-bgimages").fadeOut();
        }
    };
    widget.load = function () {
        this.set_fixed_header();
        this.set_fixed_navigation();
        this.set_fixed_ribbon();
        this.set_fixed_footer();
        this.set_rtl();
        this.set_topmenu();
        this.set_colorblind_friendly();
        this.set_fixed_container();
    };
    widget.setting = function (name) {
        var value = (typeof arguments[1] != 'undefined')  ? arguments[1] : -1;

        var _setting = JSON.parse(window.localStorage.widgetSetting);

        if(!(name in _setting)) {
            throw "attribute is not exist!";
        }

        if(0 > value) {
            return _setting[name];
        } else {
            _setting[name] = value;
            window.localStorage.widgetSetting = JSON.stringify(_setting);
        }

    };
    return widget;
})({});



$("#smart-bgimages").fadeOut(),
$("#setting").click(function () {
    $(".widget").toggleClass("activate")}
),
$('input[type="checkbox"]#smart-fixed-header').click(function () {
    widget.setting('fixed_header', $(this).is(":checked") ? 1 : 0);
    widget.set_fixed_header();
}),
$('input[type="checkbox"]#smart-fixed-navigation').click(function () {
    widget.setting('fixed_navigation', $(this).is(":checked") ? 1 : 0);
    widget.set_fixed_navigation();
}),
$('input[type="checkbox"]#smart-fixed-ribbon').click(function () {
    widget.setting('fixed_ribbon', $(this).is(":checked") ? 1 : 0);
    widget.set_fixed_ribbon();
}),
$('input[type="checkbox"]#smart-fixed-footer').click(function () {
    widget.setting('fixed_footer', $(this).is(":checked") ? 1 : 0);
    widget.set_fixed_footer();
}),
$('input[type="checkbox"]#smart-rtl').click(function () {
    widget.setting('rtl', $(this).is(":checked") ? 1 : 0);
    widget.set_rtl();
}),
$("#smart-topmenu").on("change", function (a) {
    widget.setting('topmenu', $(this).is(":checked") ? 1 : 0);
    widget.set_topmenu();
    location.reload();
}),
$('input[type="checkbox"]#smart-fixed-container').click(function () {
    widget.setting('fixed_container', $(this).is(":checked") ? 1 : 0);
    widget.set_fixed_container();
}),
$('input[type="checkbox"]#colorblind-friendly').click(function () {
    widget.setting('colorblind_friendly', $(this).is(":checked") ? 1 : 0);
    widget.set_colorblind_friendly();
}),
$("#reset-smart-widget").bind("click", function () {
    return $("#refresh").click(), !1
}),
$("#smart-styles > a").on("click", function () {
    var a = $(this), b = $("#logo img");
    $.root_.removeClassPrefix("smart-style").addClass(a.attr("id")),
        $("html").removeClassPrefix("smart-style").addClass(a.attr("id")),
        b.attr("src", a.data("skinlogo")),
        $("#smart-styles > a #skin-checked").remove(),
        a.prepend("<i class='fa fa-check fa-fw' id='skin-checked'></i>")
});
jQuery(document).ready(function() {
    widget.load();
});
