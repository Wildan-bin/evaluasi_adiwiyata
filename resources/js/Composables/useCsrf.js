import axios from 'axios';

/**
 * Composable untuk mengelola CSRF token di SPA
 */

let isRefreshing = false;
let refreshPromise = null;

/**
 * Update meta tag dengan token baru
 */
function updateMetaToken(token) {
    let meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        meta.setAttribute('content', token);
    } else {
        // Buat meta tag jika belum ada
        meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = token;
        document.head.appendChild(meta);
    }
}

/**
 * Mendapatkan CSRF token dari meta tag
 */
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

/**
 * Mengambil CSRF token fresh dari server via custom endpoint
 */
export async function refreshCsrfToken() {
    if (isRefreshing && refreshPromise) {
        return refreshPromise;
    }

    isRefreshing = true;

    refreshPromise = (async () => {
        try {
            // Gunakan custom endpoint yang mengembalikan token langsung
            const response = await axios.get('/csrf-token', {
                withCredentials: true
            });
            
            const newToken = response.data.csrf_token;
            
            if (newToken) {
                // Update meta tag
                updateMetaToken(newToken);
                
                // Update axios default header
                axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                
                console.log('CSRF token refreshed successfully');
            }

            return newToken;
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
            
            // Fallback: coba Sanctum endpoint
            try {
                await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
                const fallbackToken = getCSRFToken();
                return fallbackToken;
            } catch (e) {
                throw error;
            }
        } finally {
            isRefreshing = false;
            refreshPromise = null;
        }
    })();

    return refreshPromise;
}

/**
 * Wrapper untuk melakukan aksi dengan CSRF token yang fresh
 */
export async function withFreshCsrf(action) {
    await refreshCsrfToken();
    return action();
}

/**
 * Helper untuk upload file dengan CSRF fresh
 */
export async function secureUpload(url, formData, config = {}) {
    const token = await refreshCsrfToken();
    
    return axios.post(url, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': token,
        },
        withCredentials: true,
        ...config,
    });
}

/**
 * Helper untuk POST request dengan CSRF fresh
 */
export async function securePost(url, data = {}, config = {}) {
    const token = await refreshCsrfToken();
    
    return axios.post(url, data, {
        headers: {
            'X-CSRF-TOKEN': token,
        },
        withCredentials: true,
        ...config,
    });
}

// Export helper
export { getCSRFToken };