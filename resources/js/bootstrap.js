import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// ============================================================================
// CSRF TOKEN HANDLING
// ============================================================================

function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function updateMetaToken(token) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        meta.setAttribute('content', token);
    }
}

// Set initial token
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCSRFToken();

// ============================================================================
// REQUEST INTERCEPTOR
// ============================================================================
window.axios.interceptors.request.use((config) => {
    const token = getCSRFToken();
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

// ============================================================================
// RESPONSE INTERCEPTOR - Handle 419 dengan Auto-Retry
// ============================================================================
let isRefreshingToken = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) {
            prom.reject(error);
        } else {
            prom.resolve(token);
        }
    });
    failedQueue = [];
};

window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response?.status === 419 && !originalRequest._retry) {

            if (isRefreshingToken) {
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                }).then(token => {
                    originalRequest.headers['X-CSRF-TOKEN'] = token;
                    return window.axios(originalRequest);
                }).catch(err => {
                    return Promise.reject(err);
                });
            }

            originalRequest._retry = true;
            isRefreshingToken = true;

            try {
                // Ambil token baru dari custom endpoint
                const response = await window.axios.get('/csrf-token', {
                    withCredentials: true,
                    _retry: true // Prevent infinite loop
                });

                const newToken = response.data.csrf_token;

                if (!newToken) {
                    throw new Error('No CSRF token in response');
                }

                // Update meta tag dan axios header
                updateMetaToken(newToken);
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;

                processQueue(null, newToken);

                // Retry dengan token baru
                originalRequest.headers['X-CSRF-TOKEN'] = newToken;
                return window.axios(originalRequest);

            } catch (refreshError) {
                processQueue(refreshError, null);
                console.error('CSRF refresh failed:', refreshError);

                // Last resort: reload page
                window.location.reload();
                return Promise.reject(refreshError);

            } finally {
                isRefreshingToken = false;
            }
        }

        return Promise.reject(error);
    }
);

window.getCSRFToken = getCSRFToken;
