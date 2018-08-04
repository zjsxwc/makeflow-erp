/**
 * Created by wangchao on 6/10/17.
 */
define(function(){
    var PrintDom = function (domId) {
        var $target = $("#"+domId);
        if (!$target.length) {
            console.log("#"+domId+" not exist.");
            return;
        }
        var htmlContent = $target.html();

        var iframeId = "__iframe_for_print_dom__" + domId;
        var $mayExistIframe = $("#"+iframeId);
        if (!$mayExistIframe.length) {
            var $iframe = $("<iframe id='"+iframeId+"' />");
            $iframe.css({position: "absolute", width: "0px", height: "0px", left: "-600px", top: "-600px"});
            $("body").append($iframe);
        }

        setTimeout(function () {
            var $iframes = $("#"+iframeId);
            if (!$iframes.length) {
                console.log("#"+iframeId+" not exist.");
                return;
            }
            var iframe = $iframes[0];
            var doc  = iframe.contentWindow.document;
            var body = doc.getElementsByTagName("body")[0];
            body.innerHTML = htmlContent;
            iframe.contentWindow.print();
        }, 0);
    };

    return PrintDom;
});