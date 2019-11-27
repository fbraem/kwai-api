/**
 * State of the store
 */
import tokenStore from '@/js/TokenStore';
import localStorage from './local';

export const state = () => {
  return {
    localStorage,
    tokenStore,
    error: null,
  };
};
