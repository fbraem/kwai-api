/**
 * Creates a ky instance for calling the api's
 */
import ky from 'ky';
import config from 'config';

import tokenStore from './TokenStore';
import store from './store';

const defaultOptions = {
  prefixUrl: config.api,
  method: 'GET',
  headers: {
    Accept: 'application/vnd.api+json',
    'Content-Type': 'application/vnd.api+json'
  },
  hooks: {
    beforeRequest: [
      request => {
        const token = tokenStore.access_token;
        if (token) {
          request.headers.set('Authorization', `Bearer ${token}`);
        }
      },
    ],
    afterResponse: [
      async(request, options, response) => {
        if (response.status === 401) {
          console.log('401!!!');
          console.log(request, options);
          await store.dispatch('auth/refresh');
          const token = tokenStore.access_token;
          request.headers.set('Authorization', `Bearer ${token}`);
          return ky(request);
        }
      },
    ]
  }
};

export const http = ky.create(defaultOptions);
