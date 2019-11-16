/**
 * An axios version with an automatic refresh token call.
 * Used https://github.com/Flyrell/axios-auth-refresh as input.
 */
import axios from 'axios';

import config from 'config';
axios.defaults.baseURL = config.api;

import tokenStore from './TokenStore';

import store from '@/stores/root';
/**
 * Refresh a new token
 */
const refreshAuthLogic = async failedRequest => {
  await store.dispatch('auth/refresh', failedRequest);
  failedRequest.response.config.headers['Authentication'] =
    'Bearer ' + store.state.auth.tokenStore.access_token;
};

/**
 * Interceptor for handling 401 Not Authorized. For more info see
 * https://github.com/Flyrell/axios-auth-refresh
 */
function createAuthRefreshInterceptor(axios, refreshTokenCall, options = {}) {
  const id = axios.interceptors.response.use(res => res, error => {
    if (!error.response ||
        (error.response.status && error.response.status !== 401)) {
      return Promise.reject(error);
    }

    // Remove the interceptor to prevent a loop
    // in case token refresh also causes the 401
    axios.interceptors.response.eject(id);

    const refreshCall = refreshTokenCall(error);

    // Create interceptor that will bind all the others requests
    // until refreshTokenCall is resolved
    const requestQueueInterceptorId = axios.interceptors.request.use(
      request => refreshCall.then(() => request)
    );

    // When response code is 401 (Unauthorized), try to refresh the token.
    return refreshCall.then(() => {
      axios.interceptors.request.eject(requestQueueInterceptorId);
      return axios(error.response.config);
    }).catch(error => {
      axios.interceptors.request.eject(requestQueueInterceptorId);
      return Promise.reject(error);
    }).finally(
      () => createAuthRefreshInterceptor(axios, refreshTokenCall, options));
  });
  return axios;
}

/**
 * Interceptor for setting headers.
 */
axios.interceptors.request.use(request => {
  var token = tokenStore.access_token;
  if (token) {
    request.headers['Authorization'] = `Bearer ${token}`;
  }
  request.headers['Accept'] = 'application/vnd.api+json';
  request.headers['Content-Type'] = 'application/vnd.api+json';
  return request;
});

createAuthRefreshInterceptor(axios, refreshAuthLogic);

export default axios;
