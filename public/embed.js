/**
 * BPS AI Assistant — Standalone Embeddable Widget
 * 
 * Works on ANY website:
 * <script src="https://bps-chatbot-v2.pinnhost.my.id/embed.js" defer></script>
 */
(function () {
  if (window.BPS_AI_WIDGET_INITIALIZED) return;
  window.BPS_AI_WIDGET_INITIALIZED = true;

  const BPS_BASE_URL = 'https://bps-chatbot-v2.pinnhost.my.id';

  // Inject Styles
  const style = document.createElement('style');
  style.innerHTML = `
    #bps-ai-bubble-btn {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 2147483647;
      display: flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, #0077A6 0%, #00ADEF 100%);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 9999px;
      box-shadow: 0 10px 25px -5px rgba(0, 119, 166, 0.4), 0 8px 10px -6px rgba(0, 119, 166, 0.3);
      cursor: pointer;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 14px;
      font-weight: 600;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      user-select: none;
      box-sizing: border-box;
    }
    #bps-ai-bubble-btn:hover {
      transform: translateY(-2px) scale(1.03);
      box-shadow: 0 15px 30px -5px rgba(0, 119, 166, 0.5), 0 10px 12px -5px rgba(0, 119, 166, 0.3);
    }
    #bps-ai-bubble-btn svg {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
    }
    #bps-ai-frame-container {
      position: fixed;
      bottom: 85px;
      right: 24px;
      width: 420px;
      height: 640px;
      max-width: calc(100vw - 32px);
      max-height: calc(100vh - 110px);
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.08);
      z-index: 2147483646;
      overflow: hidden;
      display: none;
      flex-direction: column;
      transform-origin: bottom right;
      transition: opacity 0.2s ease, transform 0.2s ease;
      box-sizing: border-box;
    }
    #bps-ai-frame-container.active {
      display: flex;
      animation: bps-fade-in 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes bps-fade-in {
      from { opacity: 0; transform: scale(0.92) translateY(15px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    #bps-ai-iframe {
      width: 100%;
      height: 100%;
      border: none;
      background: transparent;
    }
    @media (max-width: 480px) {
      #bps-ai-frame-container {
        bottom: 0;
        right: 0;
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        border-radius: 0;
      }
      #bps-ai-bubble-btn {
        bottom: 16px;
        right: 16px;
        padding: 10px 16px;
        font-size: 13px;
      }
    }
  `;
  document.head.appendChild(style);

  // 1. Create Frame Container
  const frameContainer = document.createElement('div');
  frameContainer.id = 'bps-ai-frame-container';

  const iframe = document.createElement('iframe');
  iframe.id = 'bps-ai-iframe';
  iframe.src = BPS_BASE_URL;
  iframe.allow = 'clipboard-write';
  frameContainer.appendChild(iframe);
  document.body.appendChild(frameContainer);

  // 2. Create Floating Button
  const btn = document.createElement('div');
  btn.id = 'bps-ai-bubble-btn';
  btn.innerHTML = `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
    </svg>
    <span id="bps-ai-btn-text">Tanya BPS</span>
  `;
  document.body.appendChild(btn);

  let isOpen = false;

  btn.addEventListener('click', function () {
    isOpen = !isOpen;
    if (isOpen) {
      frameContainer.classList.add('active');
      btn.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <span id="bps-ai-btn-text">Tutup</span>
      `;
    } else {
      frameContainer.classList.remove('active');
      btn.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span id="bps-ai-btn-text">Tanya BPS</span>
      `;
    }
  });
})();
