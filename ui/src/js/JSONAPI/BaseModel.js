import Model from './Model';
import axios from 'axios';
import OAuth from '@/js/oauth';

export default class BaseModel extends Model {
  baseURL() {
    return 'api';
  }

  async request(config) {
      var oauth = new OAuth();
      config.headers = config.headers || {
          'Accept' : 'application/vnd.api+json',
          'Content-Type' : 'application/vnd.api+json'
      };
      var token = oauth.getAccessToken();
      if (oauth.isAuthenticated()) {
          config.withCredentials = true;
          if ( token ) {
              config.headers['Authorization'] = `Bearer ${token}`
          }
      }
      try {
          return await axios.request(config);
      } catch(error) {
          if (error.response.status == 401 && error.response.config && token) {
              await oauth.refreshToken();
              token = oauth.getAccessToken();
              if (token) {
                  error.response.config.headers['Authorization'] = `Bearer ${token}`;
                  return await axios.request(error.response.config);
              }
              throw(error);
          }
      }
  }
};
