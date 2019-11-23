/**
 * Creates a ky instance for calling the api's
 */
import wretch from 'wretch';

import config from 'config';

import tokenStore from './TokenStore';
import store from './store';

export const http = wretch(config.api);

export const http_auth = http.defer((w, url, options) => {
  const token = tokenStore.access_token;
  if (token) {
    return w.auth(`Bearer ${token}`);
  }
  return w;
});

export const http_api = http_auth
  .accept('application/vnd.api+json')
  .content('application/vnd.api+json')
  .catcher(401, async(_, request) => {
    await store.dispatch('auth/refresh');
    const token = tokenStore.access_token;
    return request.auth(token).replay();
  })
;
