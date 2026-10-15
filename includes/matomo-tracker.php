<!-- Matomo -->
<script type="text/javascript">
  var _paq = window._paq || [];
  _paq.push(['enableLinkTracking']);
  _paq.push(['setRequestMethod', 'POST']);
  _paq.push(['trackPageView']);

  (function() {
    _paq.push(['setTrackerUrl', '/matomo/matomo.php']);
    _paq.push(['setSiteId', '17']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true;
    g.src='/matomo/js/matomo.js';
    s.parentNode.insertBefore(g,s);

    var match = location.search.match(/[?&]motCle=([^&]+)/);
    if (match && match[1]) {
        var keyword = decodeURIComponent(match[1].replace(/\+/g, ' '));
        _paq.push(['trackSiteSearch', keyword]);
    }
  })();

  (function() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'https://ipwho.is/?output=json', true);
    xhr.timeout = 2500;
    xhr.onload = function() {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          if (data && data.ip) {
            _paq.push(['appendToTrackingUrl', 'cip=' + encodeURIComponent(data.ip)]);
          }
        } catch (e) {}
      }
    };
    xhr.onerror = function() {};
    xhr.ontimeout = function() {};
    xhr.send();
  })();
</script>
<!-- End Matomo Code -->
