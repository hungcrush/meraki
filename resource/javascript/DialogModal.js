(function(e) {
    $.fn.DialogModal = function(e) {
        var t = {
            modulid: "dialog",
            theme: "default",
            style: "white",
            overlay: true,
            overlaycolor: "#000",
            overlayopacity: .5,
            closeoverlay: true,
            closebutton: true,
            title: "My Dialog",
            content: "This is a content.",
            width: 400,
            height: 300,
            radius: 5,
            draggable: true,
            buttons: null
        };
        var e = $.extend(t, e);
        this.exit = function() {
            $("#" + e.modulid).remove();
            $(".DialogModalControllerOverlay").remove()
        };
        return this.each(function() {
            function a() {
                n = ($(window).width() - $("#" + e.modulid).width()) / 2;
                r = ($(window).height() - $("#" + e.modulid).height()) / 2;
                if ($(window).width() <= $("#" + e.modulid).width()) i = $(window).width();
                else i = e.width;
                $("#" + e.modulid).css({
                    left: n,
                    top: r,
                    width: i
                })
            }
            var t = (new Date).getTime();
            e.modulid = "popup_window_" + t;
            var n = ($(window).width() - e.width) / 2;
            var r = ($(window).height() - e.height) / 2;
            var i = e.width;
            var s = 1e4;
            var o = "";
            radius = "-webkit-border-radius: " + e.radius + "px; -moz-border-radius: " + e.radius + "px; border-radius: " + e.radius + "px;";
            $.each(e.buttons, function(t, entry){
                o = o + '<button id="dmc-button_' + t + '" class="' + entry.class + '" style="' + radius + '">' + entry.label + "</button>"
            });
            if (e.closebutton == true) closebtn = "<a></a>";
            else closebtn = "";
            if (e.overlay == true) {
                isoverlay = '<div class="DialogModalControllerOverlay" style="opacity:' + e.overlayopacity + "; background:" + e.overlaycolor + ';"></div>'
            } else isoverlay = "";
            var u = isoverlay + '<div class="DialogModalController easing shadow dmc-style-' + e.theme + " dmc-" + e.style + '" id="' + e.modulid + '" style="width:' + e.width + "px; " + radius + " left:" + n + "px; top:" + r + 'px"> <div class="dmc-header">' + e.title + closebtn + '</div> <div class="dmc-container"> <div> ' + e.content + ' </div> </div>	<div class="dmc-footer"> <div> <span class="result"></span>' + o + "</div> </div>	</div>";
            $("body").append(u);
            $("#" + e.modulid + " .dmc-header>a").click(function(e) {
                var t = "#" + $(this).parent().parent().attr("id");
                $(t).remove();
                $(".DialogModalControllerOverlay").remove()
            });
            if (e.overlay == true) {
                if (e.closeoverlay == true) {
                    $(".DialogModalControllerOverlay").click(function(t) {
                        $("#" + e.modulid).remove();
                        $(".DialogModalControllerOverlay").remove()
                    })
                }
            }
            $("#" + e.modulid + " .dmc-footer").find("button").click(function(t) {
                var n = $(this).attr("id").split("_");
                e.buttons[n[1]].action.call()
            });
            if (e.draggable == true) {
                $("#" + e.modulid).draggable({
                    handle: ".dmc-header",
                    cursor: 'move'
                })
            }
            a();
            $(window).resize(function(e) {
                a()
            });
            $("#" + e.modulid).click(function(e) {
                $(this).css({
                    "z-index": s + 1
                });
                s++
            })
        })
    }
})(jQuery)