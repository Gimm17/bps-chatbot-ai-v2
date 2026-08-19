/**
 * BPS AI Assistant — Standalone Embeddable Widget (Cloud Bubble Edition)
 *
 * Usage on ANY website:
 * <script src="https://bps-chatbot-v2.pinnhost.my.id/build/assets/embed.js" defer></script>
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
      bottom: 20px;
      right: 20px;
      z-index: 2147483647;
      display: flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #0077A6 0%, #00ADEF 100%);
      color: #ffffff;
      padding: 10px 16px 10px 12px;
      border-radius: 9999px;
      box-shadow: 0 10px 25px -4px rgba(0, 119, 166, 0.45), 0 4px 12px -2px rgba(0, 119, 166, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.35);
      cursor: pointer;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 13.5px;
      font-weight: 600;
      letter-spacing: 0.2px;
      border: 1px solid rgba(255, 255, 255, 0.25);
      transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
      user-select: none;
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }
    #bps-ai-bubble-btn:hover {
      transform: translateY(-3px) scale(1.04);
      box-shadow: 0 16px 32px -4px rgba(0, 119, 166, 0.55), 0 6px 16px -2px rgba(0, 119, 166, 0.35);
    }
    #bps-ai-bubble-btn:active {
      transform: translateY(-1px) scale(0.98);
    }
    .bps-cloud-icon-wrapper {
      width: 26px;
      height: 26px;
      display: flex;
      align-items: center;
      justify-content: center;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
      flex-shrink: 0;
    }
    #bps-ai-frame-container {
      position: fixed;
      bottom: 78px;
      right: 20px;
      width: 390px;
      height: 570px;
      max-width: calc(100vw - 32px);
      max-height: calc(100vh - 96px);
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 50px -10px rgba(0, 15, 40, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.08);
      z-index: 2147483646;
      overflow: hidden;
      display: none;
      flex-direction: column;
      transform-origin: bottom right;
      box-sizing: border-box;
      border: 1px solid rgba(226, 232, 240, 0.9);
    }
    #bps-ai-frame-container.active {
      display: flex;
      animation: bps-popup 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes bps-popup {
      from { opacity: 0; transform: scale(0.92) translateY(18px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    #bps-ai-iframe {
      width: 100%;
      height: 100%;
      border: none;
      background: #F8FAFC;
    }
    @media (max-width: 480px) {
      #bps-ai-frame-container {
        bottom: 72px !important;
        right: 12px !important;
        left: auto !important;
        top: auto !important;
        width: calc(100vw - 24px) !important;
        max-width: 380px !important;
        height: min(520px, calc(100dvh - 86px)) !important;
        max-height: calc(100dvh - 86px) !important;
        border-radius: 18px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 16px 40px -8px rgba(0, 15, 40, 0.35) !important;
      }
      #bps-ai-bubble-btn {
        bottom: 14px;
        right: 12px;
        padding: 8px 14px 8px 10px;
        font-size: 12.5px;
      }
    }
  `;
  document.head.appendChild(style);

  // 1. Create Frame Container
  const frameContainer = document.createElement('div');
  frameContainer.id = 'bps-ai-frame-container';

  const iframe = document.createElement('iframe');
  iframe.id = 'bps-ai-iframe';
  iframe.src = BPS_BASE_URL + '/?embed=1';
  iframe.allow = 'clipboard-write';
  frameContainer.appendChild(iframe);
  document.body.appendChild(frameContainer);

  // 2. SVG Cloud Chat Icon with BPS Statistical Chart motif
  const cloudChatSvg = `
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
      <path d="M9.5 23.5H22C25.5899 23.5 28.5 20.5899 28.5 17C28.5 13.5683 25.8366 10.7573 22.4668 10.521C21.7248 6.78652 18.4414 4 14.5 4C10.0526 4 6.38676 7.23432 5.67978 11.5173C3.0189 12.3789 1 14.9455 1 18C1 21.3137 3.68629 24 7 24L5 28L10.5 24.5L9.5 23.5Z" 
            fill="white" fill-opacity="0.22" stroke="white" stroke-width="1.75" stroke-linejoin="round" />
      <rect x="9.5" y="14.5" width="2.2" height="5.5" rx="1.1" fill="#F7941D" />
      <rect x="13.5" y="11.5" width="2.2" height="8.5" rx="1.1" fill="#ffffff" />
      <rect x="17.5" y="13" width="2.2" height="7" rx="1.1" fill="#10B981" />
      <path d="M24 5L24.8 7.2L27 8L24.8 8.8L24 11L23.2 8.8L21 8L23.2 7.2L24 5Z" fill="#FDE047" />
    </svg>
  `;

  const closeSvg = `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 17px; height: 17px;">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
  `;

  // 3. Create Floating Button
  const btn = document.createElement('div');
  btn.id = 'bps-ai-bubble-btn';
  btn.innerHTML = `
    <div class="bps-cloud-icon-wrapper" id="bps-icon-box">${cloudChatSvg}</div>
    <span id="bps-ai-btn-text">Tanya BPS</span>
  `;
  document.body.appendChild(btn);

  let isOpen = false;

  btn.addEventListener('click', function () {
    isOpen = !isOpen;
    const iconBox = document.getElementById('bps-icon-box');
    const textSpan = document.getElementById('bps-ai-btn-text');

    if (isOpen) {
      frameContainer.classList.add('active');
      iconBox.innerHTML = closeSvg;
      textSpan.textContent = 'Tutup';
    } else {
      frameContainer.classList.remove('active');
      iconBox.innerHTML = cloudChatSvg;
      textSpan.textContent = 'Tanya BPS';
    }
  });
})();
