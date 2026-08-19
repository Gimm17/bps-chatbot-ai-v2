import { ref } from 'vue';

const deferredPrompt = ref(null);
const isInstallable = ref(false);
const isInstalled = ref(false);

export function usePwa() {
  const initPwa = () => {
    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
      isInstalled.value = true;
    }

    // Register Service Worker
    if ('serviceWorker' in navigator && window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then((reg) => {
            console.log('[PWA] Service Worker registered successfully:', reg.scope);
          })
          .catch((err) => {
            console.log('[PWA] Service Worker registration failed:', err);
          });
      });
    }

    // Listen for browser install prompt
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt.value = e;
      isInstallable.value = true;
    });

    window.addEventListener('appinstalled', () => {
      isInstalled.value = true;
      isInstallable.value = false;
      deferredPrompt.value = null;
      console.log('[PWA] App successfully installed');
    });
  };

  const installApp = async () => {
    if (!deferredPrompt.value) return;

    deferredPrompt.value.prompt();
    const { outcome } = await deferredPrompt.value.userChoice;
    console.log('[PWA] User choice:', outcome);

    if (outcome === 'accepted') {
      isInstallable.value = false;
    }
    deferredPrompt.value = null;
  };

  return {
    isInstallable,
    isInstalled,
    initPwa,
    installApp
  };
}
