/* by kkt 2013-12-18
example:
<script>
    $(function(){
        $(function(){
            $("#nav").switching({  // these are default params
                back : "yellow",
                event: "click",
                speed: 500,
                span_align:"center"  //this means the text's position which is come from  "<li><span>xxxxx</span ></li>" default value is "center"
            });    
        })
    })
</script>
<body>
    <ul id = "nav">
        <li switch ="first"">   //  switch="first" is  must
            <span>first</span>
        </li>
        <li switch ="second"  class="current" > // class="current" means it's default select
            <span>second</span>
        </li>
        <li switch ="third">
            <span>third</span>
        </li>
        <li switch ="forth">
            <span>forth</span>
        </li>
    </ul>
    <div id = "nav_content">   //  this "div" must be set height and width. example : "style='height:200px;width:900px'"
        <div id="first_content"  >11111</div>
        <div id="second_content">22222</div>
        <div id="third_content">33333</div>
        <div id="forth_content">44444</div>
    </div>
</body>
*/
(function($){
    $.fn.switching = function(param){
        var config = $.extend({}, $.fn.switching.switchingConfig, param);
        var event       = config.event;
        var back        = config.back;
        var speed       = config.speed;
        var span_align  = config.span_align;
        var id          = $(this).attr("id");
        var content     = id + "_content";
        
        $content = $("#" + content);
        $content.css("overflow", "hidden");
        $default_selector = ($("li").hasClass("current"))? $("li.current") : $(this).children("li:first").addClass("current");
        $ul = $(this);
        $ul_pos = $ul.offset();
        $first = $ul.children("li:first");
        $first_content = $("#" + $("li.current").attr("switch") + "_content");
        $li = $ul.children("li");
        $li.css("height", $ul.height());
        $span = $li.children("span");

        $copy_span = $span.clone();
        $span.remove();
        $.each($copy_span, function(k, v){
            $each_li = $ul.children("li").eq(k);
            $each_li_pos = $each_li.offset();
            $(v).css({
                "text-align" : span_align,
                "overflow" : "hidden",
                "width" : $each_li.width()+"px",
                "height" : $each_li.height()+"px",
                "line-height" : $each_li.height()+"px",
                "display" : "block",
                "position" : "absolute",
                "z-index" : "10",
                "top" : $each_li_pos.top+"px",
                "left" : $each_li_pos.left+"px" 
            });
        })
        var current_pos = $default_selector.offset();
        var default_selector_index = $default_selector.index();
        
        $background = $("<div id='li_background'/>");
        $background.css({
            "position"    : "absolute",
            "background"  : back,
            "left"        : current_pos.left,
            "top"         : current_pos.top,
            "width"       : $default_selector.width(),
            "height"      : $default_selector.height(),
            "z-index"     : 8
        });
        $dom_container = $("<div id='dom_containter' style='position:absolute;top:0;left:0;z-index:20;' />");
        $dom_container.appendTo($("body"));
        $copy_span.appendTo($dom_container);
        $background.appendTo($dom_container);
        
        $content_div = $content.children("div");
        $content_div.css({"float" : "left", "width" : $content.width()+"px", "height" : $content.height()+"px"})
        var single_child_width = $content_div.width();
        var single_child_height = $content_div.height();
        var children_container_width = $content_div.length*single_child_width;
        $content.css({"width" : single_child_width, "height" : single_child_height, "position" : "absolute", "top" : $ul_pos.top+$ul.height()+"px", "left" : $ul_pos.left+"px"});
        
        $children_container = $("<div id='children_container'  />");
        $children_container.css({
            "position"    : "absolute",
            "left"        : -(single_child_width*default_selector_index)+"px",
            "top"         : 0,
            "width"       : children_container_width
        });
        $children_container.append($content_div);
        $content.empty();
        $children_container.appendTo($content);
        // all doms had been created.
        $copy_span.bind(event, function(){
            $parent_li = $li.eq($(this).index());
            if(!$parent_li.hasClass("current")){
                var destination;
                $("li.current").removeClass("current");
                $parent_li.addClass("current");
                
                $children_container.stop(false);
                destination = -($parent_li.index()*single_child_width);
                $children_container.animate({
                    left : destination+"px"
                }, speed);
            }
        });
        $copy_span.bind("mouseenter", function(){
            $parent_li = $li.eq($(this).index());
            $this_li_pos = $parent_li.offset();
            $background.stop(false);
            $background.animate({
                left    : $this_li_pos.left,
                width   : $(this).width()
            }, speed);
        });
        $dom_container.bind("mouseleave", function(){
            var current_li_left_pos = $("li.current").offset().left;
            $background.stop(false);
            $background.animate({
                left : current_li_left_pos,
                width : $("li.current").width()
            },speed);
        });
    };
    $.fn.switching.switchingConfig = {
        span_align  : "center",
        event       : "click",
        back        : "yellow",
        speed       : 500
    }
})(jQuery)

