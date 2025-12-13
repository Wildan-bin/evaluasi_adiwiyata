# CSRF Token Management untuk SPA (Vue + Inertia)

## Masalah
Di SPA, halaman tidak di-refresh saat navigasi, jadi CSRF token tidak ter-update secara otomatis. Ini menyebabkan error **419 Csrf Token Mismatch** setelah beberapa request.

## Solusi yang Diterapkan

### 1. **Bootstrap.js - Request & Response Interceptor**
- `resources/js/bootstrap.js`
- **Request Interceptor**: Set `X-CSRF-TOKEN` header di setiap request
- **Response Interceptor**: Handle 419 error dengan auto-retry
  - Saat dapat 419, ambil token fresh dari meta tag
  - Retry request otomatis dengan token baru
  - Tidak perlu manual refresh halaman

```javascript
// Setiap request otomatis include token terbaru
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCSRFToken();

// Saat 419 error, auto-retry
if (error.response?.status === 419 && !originalRequest._retry) {
    // Refresh token dan retry otomatis
}
```

### 2. **HandleInertiaRequests Middleware**
- `app/Http/Middleware/HandleInertiaRequests.php`
- Share CSRF token ke Inertia props (`csrf_token`)
- Priority 1 untuk ambil token fresh dari props ini
- Backup: ambil dari meta tag di `<head>`

### 3. **EnsureCSRFTokenInResponse Middleware (NEW)**
- `app/Http/Middleware/EnsureCSRFTokenInResponse.php`
- Memastikan `<meta name="csrf-token">` selalu update di setiap HTML response
- Ini penting agar interceptor punya token fresh untuk retry 419

### 4. **app.blade.php**
- Meta tag di `<head>`: `<meta name="csrf-token" content="{{ csrf_token() }}">`
- Ini backup fallback untuk ambil token di client-side

## Alur Kerja

```
1. User buka halaman
   ↓
2. app.blade.php dimuat → meta tag csrf-token di-set
3. HandleInertiaRequests → csrf_token di-share ke props Inertia
4. bootstrap.js → Ambil token dari Inertia props
   ↓
5. User submit form / API call
   ↓
6. Request Interceptor → Set X-CSRF-TOKEN header
   ↓
7. Jika 419 error:
   - Response Interceptor intercept
   - Cek meta tag untuk token fresh
   - Retry request otomatis ✓
```

## Setup di Bootstrap App

```php
// bootstrap/app.php
$middleware->web(append: [
    \App\Http\Middleware\EnsureCSRFTokenInResponse::class,  // NEW
    \App\Http\Middleware\HandleInertiaRequests::class,
    \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
]);
```

## Testing

Untuk test apakah setup kerja:

```javascript
// Di browser console
// 1. Lihat token yang di-use
getCSRFToken()

// 2. Submit form, observe interceptors
// 3. Intentionally trigger 419 error (matikan server sementara)
// 4. Pastikan auto-retry jalan
```

## Best Practice

✅ **Jangan**:
- Menonaktifkan CSRF protection untuk SPA
- Mengubah token secara manual di JS

✅ **Harus**:
- Pastikan middleware order benar (EnsureCSRFToken BEFORE HandleInertia)
- Selalu set `X-Requested-With: XMLHttpRequest` (sudah ada di bootstrap.js)
- Test 419 error handling dengan intentional error untuk pastikan interceptor jalan

## References
- [Laravel CSRF Protection](https://laravel.com/docs/11.x/csrf)
- [Axios Interceptors](https://axios-http.com/docs/interceptors)
- [Inertia.js + Vue](https://inertiajs.com/)
