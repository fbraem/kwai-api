import Model from './Model';
import axios from 'axios';
import OAuth from '@/js/oauth';

export default class BaseModel extends Model {
  baseURL() {
    return 'api';
  }

  request(config) {
      var oauth = new OAuth();
      config.headers = config.headers || {
          'Accept' : 'application/vnd.api+json',
          'Content-Type' : 'application/vnd.api+json'
      };
      if (oauth.isAuthenticated()) {
          config.withCredentials = true;
          var token = oauth.getAccessToken();
          if ( token ) {
              config.headers['Authorization'] = `Bearer ${token}`
          }
      }
      return axios.request(config);
  }

  async call(method, retry) {
      if (!retry) retry = true;
      let result = null;
      try {
          let result = await method();
      } catch(error) {
          if (error.response) { // axios problem
              if (error.response.status == 401 && retry) { // Unauthorized
                  var oauth = new OAuth();
                  await oauth.refreshToken();
                  return this.call(method, false);
              }
              throw error;
          }
      };
      return result;
  }
};
