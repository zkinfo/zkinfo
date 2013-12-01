
var MiMap = {
    bmap_url: 'http://api.map.baidu.com/api?v=1.4&callback=init_map',
    MapWrapper: 'http://api.map.baidu.com/library/MapWrapper/1.2/src/MapWrapper.min.js',
    EventWrapper: 'http://api.map.baidu.com/library/EventWrapper/1.2/src/EventWrapper.min.js',
    InfoBox: 'http://api.map.baidu.com/library/InfoBox/1.2/src/InfoBox_min.js',
    MarkerManager: 'http://api.map.baidu.com/library/MarkerManager/1.2/src/MarkerManager_min.js',
    RichMarker: 'http://api.map.baidu.com/library/RichMarker/1.2/src/RichMarker_min.js',
    DistanceTool: 'http://api.map.baidu.com/library/DistanceTool/1.2/src/DistanceTool_min.js',
    MarkerClusterer: 'http://api.map.baidu.com/library/MarkerClusterer/1.2/src/MarkerClusterer_min.js',
    TextIconOverlay: 'http://api.map.baidu.com/library/TextIconOverlay/1.2/src/TextIconOverlay_min.js',
    GeoUtils: 'http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js',
    SearchControl: 'http://api.map.baidu.com/library/SearchControl/1.4/src/SearchControl_min.js',

    marker_manager: null,
    measure: null,

    init: function() {
        this.marker_manager = new BMapLib.MarkerManager(mi_map, {
            borderPadding: 200,
            maxZoom: 18,
            trackMarkers: true
        });
        this.show_spots(spot_data);
    },
    show_spots: function(spot_list) {
        mi_map.clearOverlays();
         $('.mi-map-siderctn').html('');
        var spot_marker_array = [],
            side_html_array = [],
            max_sumreal = 0;
        for (var i in spot_list) {
            var spot = spot_list[i];
            if (spot.sumreal > 0) {
                spot.icont = 72;
            }
            this.addMarker(spot);
            // 地图的侧边栏
            var side_html = '';
                side_html += '<div class="mapside-spot mapside-spot-' + spot.spot_code + '" data-sumreal="' + spot.sumreal + '">';
                side_html += '   <h4><i class="mi-map-icon mi-map-icon-' + (spot.icont == 72 ? 'hot' : spot.type) + '"></i>' + spot.spot_name + '</h4>';
                side_html += '   <p>总进货额：<b class="blue"><span class="rmb">￥</span>' + spot.sumreal + '</b></p>';
                side_html += '   <p>地址：' + spot.address + '</p>';
                side_html += '</div>';
             $('.mi-map-siderctn').append(side_html);
        }
       
        // console.log(this.marker_manager);
        // console.log(spot_marker_array);
        // this.marker_manager.addMarkers(spot_marker_array, 4, 15);
        // this.marker_manager.showMarkers();
    },
    addMarker: function(spot) {
        var point   = new BMap.Point(spot.lng, spot.lat),
             //自定义地图图标
             iconImg = new BMap.Icon("/ui/common/images/mi-map-icons.png", new BMap.Size(spot.iconw, spot.iconh), {
                     imageOffset:        new BMap.Size(-spot.iconl, -spot.icont),
                     infoWindowAnchor:   new BMap.Size(spot.iconlb + 5, 1),
                     offset:             new BMap.Size(spot.iconx, spot.iconh)
                 }
             ),
             marker = new BMap.Marker(point, {icon: iconImg, title: spot.spot_name});
         // 创建标注
         mi_map.addOverlay(marker);
         // 标注的弹窗
         var content = '';
             content += '<div>';
             content += '    <h4>' + spot.spot_name + '</h4>';
             content += '    <p>总进货额：<b class="blue"><span class="rmb">￥</span>' + spot.sumreal + '</b></p>';
             content += '    <p>地址：' + spot.address + '</p>';
             content += '</div>';
         var infoWindow = new BMap.InfoWindow(content);
         // 弹窗事件
         marker.addEventListener('click', function(){
             this.openInfoWindow(infoWindow);
             var spot_code = spot.spot_code,
                 spot_name = spot.spot_name,
                 area = spot.area,
                 address = spot.address;
             $('.choosed_spot').text((area == null || area == '' ? '' : area + ' - ') + spot_name);
             $('.choosed_spot').data('code',spot_code);
         });
    
         $('.mapside-spot-' + spot.spot_code).live('click', function(){
             $(this).parent().children().removeClass('active');
             $(this).addClass('active');
             marker.openInfoWindow(infoWindow);
         });
         //spot_marker_array.push(marker);
        // this.marker_manager.addMarker(marker, 4, 15);
         //marker_manager.addMarker(marker, 4, 15);
    },
    search: function() {
        var s_city = $("#s_city").val(),
            s_status = $("#s_status").val(),
            cost_min = $("#cost-min").val(),
            cost_max = $("#cost-max").val(),
            s_type = get_s_type(),
            url ='/boss/market/spot/map?s_city=' + s_city + '&s_type=' + s_type + '&s_status=' + s_status + '&is_submit=1&cost_min=' + cost_min + '&cost_max=' + cost_max;
        $.getJSON(url, function(data){
            MiMap.show_spots(data);
        });
    },
    print: function() {
        document.body.innerHTML = document.getElementById('mi-map-holder').innerHTML;  
        window.print();
    }
};
function get_s_type(){
    var type = document.getElementsByName("s_type");
    var s_type = '';
    for (var i = 0; i < type.length; i++) {
        if (type[i].checked) {
            s_type += type[i].value+',';
        }
    }
    s_type = s_type.substr(0, s_type.length - 1);
    return s_type;
}
var map_width = 0;
$(function() {
    // $(window).resize(function(){
    //     initMapSize();
    //     mapResize();
    // });
    // $(window).bind('scroll', function(){
    //     initMapSize();
    //     mapResize();
    // });
    
    $('.mi-map-search').click(function(){
        MiMap.search();
    });
    $('.map-print').click(function(){
        MiMap.print();
    });
    $('.in-fullscreen').click(function(){
        map_width = $('#mi-map-holder').width();
        $('#mi-map-holder').css({position: 'fixed', margin: 0, top: 0, left: 0, width: '100%', height: '100%', 'z-index': 999});
        $('.out-fullscreen').show();
    });
    $('.out-fullscreen').click(function(){
        $('#mi-map-holder').css({position: 'absolute', marginLeft: 350, top: 0, left: 0, width: map_width, height: 550, 'z-index': 0});
        $('.mi-map-box').height(550);
        $(this).hide();
    });
    // 测距
    $('.measure').click(function(){
        MiMap.measure.open();
    });
    $('.map-siderbtn').toggle(
        function(){
            $('.mi-map-info-nav').animate({left: -350});
            $('.mi-map-sider').animate({left: -350});
            $('.map-siderbtn').animate({left: 0}).addClass('map-siderbtn-close');
            $('.mi-tools-l').animate({left: 0});
            $('.mi-map-tools').animate({paddingLeft: 0});
            $('#mi-map-holder').animate({marginLeft: 0});
        },
        function(){
            $('.mi-map-info-nav').animate({left: 0});
            $('.mi-map-sider').animate({left: 0});
            $('.map-siderbtn').animate({left: 349}).removeClass('map-siderbtn-close');
            $('.mi-tools-l').animate({left: 350});
            $('.mi-map-tools').animate({paddingLeft: 350});
            $('#mi-map-holder').animate({marginLeft: 350});
        }
    );
});
function init_map() {
    var mi_map = new BMap.Map("mi-map-holder");
    mi_map.centerAndZoom('杭州市', 12);
    window.mi_map = mi_map;

    //启用地图滚轮放大缩小
    mi_map.enableScrollWheelZoom();
    //启用键盘上下左右键移动地图
    mi_map.enableKeyboard();

    //向地图中添加缩放控件
    var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
    mi_map.addControl(ctrl_nav);
    //向地图中添加缩略图控件
    var ctrl_ove = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:0});
    mi_map.addControl(ctrl_ove);
    //向地图中添加比例尺控件
    var ctrl_sca = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
    mi_map.addControl(ctrl_sca);

    MiMap.measure = new BMapLib.DistanceTool(mi_map);

    MiMap.init();
}
function initMapSize() {
    window.isPrint = location.href.indexOf("pw=2") > 0;
    if (isPrint) {
        return
    }
    var c = $("#mi-map-holder"), b = $(".map-siderbtn"), a = $(".mi-map-shad"), d = getClientSize().height - 150;
    d = d < 0 ? 0 : d;

    c.height(d);
    a.height(d);
    b.css(top, parseInt((d - 55) / 2));
}
function mapResize() {
    if (isPrint) {
        return
    }
    if (window._resizeTimer) {
        return
    }
    window._resizeTimer = setTimeout(function() {
        var g = $("#mi-map-holder"), c = $(".map-siderbtn"), e = $(".mi-map-shad"), a = getClientSize().width, f = getClientSize().height;
        if (typeof map != "undefined" && map && map.fullScreenMode) {
            f -= 32
        } else {
            f -= 150
        }
        if (typeof map != "undefined" && map && !map.fullScreenMode && sideBar.status == "open") {
            a -= 350
        }
        a = a < 0 ? 0 : a;
        f = f < 0 ? 0 : f;
        var h = (f - 55) < 0 ? 0 : f - 55;
        g.height(f);
        e.height(f);
        if (document.getElementById("ROUTE_CustomTip1")) {
            document.getElementById("ROUTE_CustomTip1").style.left = (a - 235) / 2 + "px"
        }
        var d = parseInt(g.height());
        d = d < 0 ? 0 : d;
        c.css(top, parseInt((f - 55) / 2));
        window._resizeTimer = null;
        if (window.PanoMap && window.PanoMap.isOpen) {
            window.PanoMap.setPanoSize(a, f);
        }
    }, 100)
}
function getClientSize() {
    if (window.innerHeight) {
        return {width: window.innerWidth,height: window.innerHeight}
    } else {
        if (document.documentElement && document.documentElement.clientHeight) {
            return {width: document.documentElement.clientWidth,height: document.documentElement.clientHeight}
        } else {
            return {width: document.body.clientWidth,height: document.body.clientHeight}
        }
    }
}
function scriptRequest(url, echo, id, charset, flag) {
    var isIe = /msie/i.test(window.navigator.userAgent);
    if (isIe && document.getElementById("_script_" + id)) {
        var script = document.getElementById("_script_" + id)
    } else {
        if (document.getElementById("_script_" + id)) {
            document.getElementById("_script_" + id).parentNode.removeChild(document.getElementById("_script_" + id))
        }
        var script = document.createElement("script");
        if (charset != null) {
            script.charset = charset
        }
        if (id != null && id != "") {
            script.setAttribute("id", "_script_" + id)
        }
        script.setAttribute("type", "text/javascript");
        document.body.appendChild(script)
    }
    if (!flag) {
        var t = new Date();
        if (url.indexOf("?") > -1) {
            url += "&t=" + t.getTime()
        } else {
            url += "?t=" + t.getTime()
        }
    }
    var _complete = function() {
        if (!script.readyState || script.readyState == "loaded" || script.readyState == "complete") {
            if (typeof (echo) == "function") {
                try {
                    echo()
                } catch (e) {
                }
            } else {
                eval(echo)
            }
        }
    };
    if (isIe) {
        script.onreadystatechange = _complete
    } else {
        script.onload = _complete
    }
    script.setAttribute("src", url)
}
initMapSize();
scriptRequest(MiMap.bmap_url, function(){
    scriptRequest(MiMap.DistanceTool, function(){}, null, null, true);
    scriptRequest(MiMap.EventWrapper, function(){}, null, null, true);
    scriptRequest(MiMap.InfoBox, function(){}, null, null, true);
    scriptRequest(MiMap.MarkerManager, function(){}, null, null, true);
    scriptRequest(MiMap.RichMarker, function(){}, null, null, true);
    scriptRequest(MiMap.MarkerClusterer, function(){}, null, null, true);
    scriptRequest(MiMap.TextIconOverlay, function(){}, null, null, true);
    scriptRequest(MiMap.GeoUtils, function(){}, null, null, true);
    scriptRequest(MiMap.SearchControl, function(){}, null, null, true);
}, null, null, true);
