/**
 * Created by kbordyug on 3/24/2015.
 */

$jq(document).ready(function () {
    setNavigationParams();
});

$jq( document ).ajaxSuccess(function( event, xhr, settings ) {
   if(settings.data === 'layer_action=1'){
       setNavigationParams();
   }
});

window.onresize = function(event) {
    setNavigationParams();
};

function setNavigationParams(minWidth){
    var minWidth = 976;

    var titles = $jq('.block.block-layered-nav #narrow-by-list dt');
    var attrLists = $jq('.block.block-layered-nav #narrow-by-list dd');

    if ($jq(document).width() < minWidth) {
        attrLists.addClass('hidden');
        titles.removeClass('active');

        titles.off('click').click(function () {
            $jq(this).toggleClass('active');
            $jq(this).next('').toggleClass('hidden');
        })
    }
    else
    {
        attrLists.removeClass('hidden');
        titles.removeClass('active');
    }
}

/* Google Analytics */
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-1741624-1', 'auto');
ga('send', 'pageview');

/* Google Analytics */

/* Bing */
(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"4046618"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");
/* Bing */
