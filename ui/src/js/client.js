import Lockr from 'lockr';
import axios from 'axios';

import OAuth from './oauth';

// Based on https://syropia.net/journal/how-i-make-api-requests-with-vuex

function client() {

    var client = {
        auth : false,
    };

    var proxy = {
        withAuth () {
          client.auth = true;
          return client;
        },
        withoutAuth () {
          client.auth = false;
          return client;
        }
    };

    ['get', 'post', 'patch', 'put', 'delete'].forEach((verb) => {
        let access_token = Lockr.get('access_token', '');
        client[verb] = (url, options) => {
          var opts = options || Object.create(null);
          opts.headers = opts.headers || {
            'Accept' : 'application/vnd.api+json',
            'Content-Type' : 'application/vnd.api+json'
          };
          opts.method = verb;
          opts.withCredentials = true;
          if ( client.auth ) {
            opts.headers['Authorization'] = `Bearer ${access_token}`
          }
          opts.url = url;
          return axios.request(opts)
            .then((response) => {
              return response;
            }).catch((error) => {
              if (error.response.status == 401) {
                let refresh_token = Lockr.get('refresh_token', null);
                if (refresh_token) {
                    var oAuth = new OAuth();
                    oAuth.refreshToken().then((response) => {
                        var access_token = Lockr.get('access_token', null);
                        opts.headers['Authorization'] = `Bearer ${access_token}`
                        return axios.request(opts)
                            .then((response) => {
                                return response;
                            }).catch((error) => {
                                console.log(error.response);
                            });
                    }).catch((error) => {
                        console.log(error.response);
                    })
                }
              }
              throw(error);
            })
        };
    });

    return proxy;
}

export default client;
