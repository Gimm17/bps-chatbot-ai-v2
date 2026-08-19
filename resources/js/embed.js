/**
 * BPS AI Assistant — Embeddable Widget Script
 *
 * Usage on any BPS Official Website:
 * <script src="https://bps-chatbot-v2.pinnhost.my.id/embed.js" defer></script>
 */
(function () {
  if (window.BPS_AI_WIDGET_LOADED) return;
  window.BPS_AI_WIDGET_LOADED = true;

  const scriptTag = document.currentScript;
  const serverUrl = (scriptTag && scriptTag.src) 
    ? new URL(scriptTag.src).origin 
    : window.location.origin;

  // Create Container
  const container = document.createElement('div');
  container.id = 'bps-ai-widget-root';
  container.style.position = 'fixed';
  container.style.bottom = '20px';
  container.style.right = '20px';
  container.style.zIndex = '999999';
  container.style.fontFamily = "'Inter', system-ui, -apple-system, sans-serif";
  document.body.appendChild(container);

  // Inject Iframe for Isolated CSS & Security
  const iframe = document.createElement('iframe');
  iframe.src = serverUrl + '/widget';
  iframe.style.border = 'none';
  iframe.style.width = '70px';
  iframe.style.height = '70px';
  iframe.style.borderRadius = '35px';
  iframe.style.boxShadow = '0 10px 30px rgba(0,0,0,0.15)';
  iframe.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
  iframe.style.overflow = 'hidden';
  iframe.style.background = 'transparent';
  iframe.allow = 'clipboard-write';

  container.appendChild(iframe);

  // Listen to expand/collapse events from widget iframe
  window.addEventListener('message', (event) => {
    if (event.data === 'bps-widget:expand') {
      if (window.innerWidth < 480) {
        iframe.style.width = '100vw';
        iframe.style.height = '100vh';
        iframe.style.position = 'fixed';
        iframe.style.inset = '0';
        iframe.style.borderRadius = '0';
      } else {
        iframe.style.width = '400px';
        iframe.style.height = '600px';
        iframe.style.borderRadius = '16px';
      }
    } else if (event.data === 'bps-widget:collapse') {
      iframe.style.width = '70px';
      iframe.style.height = '70px';
      iframe.style.borderRadius = '35px';
      iframe.style.position = 'static';
    }
  });
})();
