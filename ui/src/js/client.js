import Lockr from 'lockr';
import axios from 'axios';

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
        let user = Lockr.get('user');
        let jwt = "";
        if (user && user.meta) {
            jwt = user.meta.jwt;
        }
        client[verb] = (url, options) => {
          var opts = options || Object.create(null);
          opts.headers = opts.headers || {
            'Accept' : 'application/vnd.api+json',
            'Content-Type' : 'application/vnd.api+json'
          };
          opts.method = verb;
          opts.withCredentials = true;
          if ( client.auth ) {
            opts.headers['Authorization'] = `Bearer ${jwt}`
          }
          opts.url = url;
          return axios.request(opts)
            .then((res) => {
              return res;
            }).catch((error) => {
              throw(error);
            })
        };
    });

    return proxy;
}

export default client;
