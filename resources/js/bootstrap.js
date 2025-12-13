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

// Interceptor - Refresh sebelum request
window.axios.interceptors.request.use((config) => {
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});
