import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useCsrf() {
  const page = usePage();
  
  const token = computed(() => page.props.csrf_token);
  
  return {
    token,
  };
}

export function refreshCsrfToken() {
  const page = usePage();
  return page.props.csrf_token;
}
