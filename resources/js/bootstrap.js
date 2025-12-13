import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Function untuk get token
function getCSRFToken() {
    // Priority 1: Dari Inertia props (paling fresh)
    if (window.__props && window.__props.csrf_token) {
        return window.__props.csrf_token;
    }

    // Priority 2: Dari meta tag
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (metaToken) {
        return metaToken;
    }

    return '';
}

// Set initial token
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCSRFToken();

// Track pending requests saat refresh token
let tokenRefreshPromise = null;

// Interceptor - Refresh token sebelum request
window.axios.interceptors.request.use((config) => {
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

// Response interceptor - Handle 419 (CSRF token expired)
window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Jika error 419 (Csrf token mismatch) dan belum retry
        if (error.response?.status === 419 && !originalRequest._retry) {
            originalRequest._retry = true;

            // Jika sudah ada refresh yang pending, tunggu
            if (tokenRefreshPromise) {
                await tokenRefreshPromise;
            } else {
                // Fetch token baru dari meta tag (Laravel auto-update)
                // atau hit endpoint untuk refresh token
                tokenRefreshPromise = new Promise((resolve) => {
                    setTimeout(() => {
                        const newToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (newToken) {
                            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                        }
                        tokenRefreshPromise = null;
                        resolve();
                    }, 100);
                });

                await tokenRefreshPromise;
            }

            // Retry request dengan token baru
            originalRequest.headers['X-CSRF-TOKEN'] = getCSRFToken();
            return window.axios(originalRequest);
        }

        return Promise.reject(error);
    }
);
