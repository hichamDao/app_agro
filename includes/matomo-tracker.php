<?php
/**
 * Matomo tracking code for FoodMax Group.
 * Inclure avant la balise </head> dans les pages publiques.
 */
?>
<!-- Matomo -->
<script>
    var _paq = window._paq = window._paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
        var u="//192.168.11.172:9094/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', '4']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true;
        g.src=u+'matomo.js';
        s.parentNode.insertBefore(g,s);
    })();

    // Detect site search via motCle URL parameter
    (function() {
        var match = location.search.match(/[?&]motCle=([^&]+)/);
        if (match && match[1]) {
            var keyword = decodeURIComponent(match[1].replace(/\+/g, ' '));
            _paq.push(['trackSiteSearch', keyword]);
        }
    })();
</script>
<!-- End Matomo Code -->