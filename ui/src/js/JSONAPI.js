import axios from 'axios';
import URI from 'urijs';
import OAuth from '@/js/oauth';
import config from 'config';

/**
 * Helper class for sending a JSONAPI request
 */
class JSONAPI {
  /**
   * Constructor.
   * @constructs
   * @param {object} config The configuration for this JSONAPI instance.
   * @param {Model} config.source The model which type is used in the URL.
   * @param {Model} [config.target]
   *                The model which is used to return the data. When omitted, it
   *                is set to config.source.
   */
  constructor({source, target = source}) {
    this.source = source;
    this.target = target;
    this.base_uri = new URI('');
    var segments = [config.api];
    segments = segments.concat(source.namespace());
    this.base_uri.segment(segments);
    this.reset();
  }

  /**
   * Reset the URI to what it was at the beginning.
   */
  reset() {
    this.uri = this.base_uri.clone();
  }

  /**
   * Add inclusion of related reources.
   * @param {string|array} relation
   *        The name of the relation to include or an array with relations.
   */
  with(relation) {
    var query = this.uri.query(true);
    query.include = query.include || [];
    if (Array.isArray(relation)) {
      query.include = query.include.concat(relation);
    } else {
      query.include.push(relation);
    }
    this.uri.query(query);
    return this;
  }

  /**
  * @typedef {Object} Data
  * @property {object} meta The meta information of the request.
  * @property {object|array} data The returned data as object or array.
  */

  /**
   * Sends a request with the GET method.
   * @param {string} [id] An id of a model.
   * @return {Data}
   *         The data returned from the JSONAPI request.
   * @throws An exception will be throwed when the request fails. The exception
   *         is a rethrow of the Axios exception.
   */
  async get(id) {
    var uri = this.uri.clone();
    var segments = uri.segment();
    segments.push(this.source.type());
    if (id) segments.push(id);
    uri.segment(segments);
    const config = {
      method: 'GET',
      url: uri.href(),
    };
    let response = await this.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }

  /**
   * Saves a model by sending a request with a POST or PATCH method. POST is
   * used when the model doesn't have an id. Otherwise PATCH is used.
   * @param {Model} model The model to save.
   * @return {Data} The data returned from the JSONAPI request
   * @throws An exception will be throwed when the request fails. The exception
   *         is a rethrow of the axios exception.
   */
  async save(model) {
    var uri = this.uri.clone();
    var segments = uri.segment();
    segments.push(this.source.type());
    if (model.id) segments.push(model.id);
    uri.segment(segments);

    var data = Array.isArray(model) ?
      model.map(element => element.serialize()) : model.serialize();

    const config = {
      method: model.id ? 'PATCH' : 'POST',
      url: uri.href(),
      data: data
    };
    let response = await this.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }

  /**
   * Sends the request
   * @private
   * @param {object} config The axios configuration object.
   * @return {object} Returns the response data
   * @throws An exception will be throwed when the request fails. The exception
   *         is a rethrow of the Axios exception.
   */
  async request(config) {
    var oauth = new OAuth();
    config.headers = config.headers || {
      Accept: 'application/vnd.api+json',
      'Content-Type': 'application/vnd.api+json',
    };
    var token = oauth.getAccessToken();
    if (oauth.isAuthenticated()) {
      config.withCredentials = true;
      if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
      }
    }
    try {
      return await axios.request(config);
    } catch (error) {
      if (error.response.status === 401 && error.response.config && token) {
        await oauth.refreshToken();
        token = oauth.getAccessToken();
        if (token) {
          error.response.config.headers['Authorization'] = `Bearer ${token}`;
          return await axios.request(error.response.config);
        }
        throw (error);
      }
      throw (error);
    }
  }
}

export default JSONAPI;
