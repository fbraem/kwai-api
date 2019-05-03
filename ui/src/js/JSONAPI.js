import URI from 'urijs';
import config from 'config';
import axios from '@/js/http';

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
   * Add a filter
   * @param {string} filter The name of the field to filter
   * @param {*} value The value used to filter
   */
  where(filter, value) {
    var query = this.uri.query(true);
    query['filter[' + filter + ']'] = value;
    this.uri.query(query);
    return this;
  }

  /**
   * Add a path
   */
  path(p) {
    this.uri.segment(p);
  }

  /**
   * Add a page filter
   * @param {object} page
   * @param {int} page.offset The page offset.
   * @param {int} page.limit The max. number of records to return.
   */
  paginate({ offset = 0, limit = 10 }) {
    var query = this.uri.query(true);
    query['page[offset]'] = offset;
    query['page[limit]'] = limit;
    this.uri.query(query);
    return this;
  }

  /**
   * Add a sort
   * @param {string} field
   *        The name of the field to use for sorting.
   */
  sort(field) {
    var query = this.uri.query(true);
    query.sort = query.sort || '';
    if (query.sort.length > 0) {
      query.sort += ',';
    }
    query.sort += field;
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
    uri.segment(this.source.type());
    if (id) uri.segment(id);
    const config = {
      method: 'GET',
      url: uri.href(),
    };
    let response = await axios.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }

  async custom({id, path}) {
    var uri = this.uri.clone();
    uri.segment(this.source.type());
    if (id) uri.segment(id);
    if (path) uri.segment(path);
    const config = {
      method: 'GET',
      url: uri.href(),
    };
    let response = await axios.request(config);
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
    uri.segment(this.source.type());
    if (model.id) uri.segment(model.id);

    var data = Array.isArray(model) ?
      model.map(element => element.serialize()) : model.serialize();

    const config = {
      method: model.id ? 'PATCH' : 'POST',
      url: uri.href(),
      data: {
        data: data
      }
    };
    let response = await axios.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }

  /**
   * Sends a DELETE to the API.
   * @param {Model} model The model to delete.
   */
  async delete(model) {
    var uri = this.uri.clone();
    uri.segment(this.source.type());
    if (model.id) uri.segment(model.id);

    const config = {
      method: 'DELETE',
      url: uri.href()
    };
    await axios(config);
  }

  /**
   * Saves a relation of a model. The model needs an id.
   * @param {Model} model The model with the relationship
   * @param {Model} relation The related model
   */
  async attach(model, relation) {
    var uri = this.uri.clone();
    uri.segment(this.source.type());
    if (!model.id) throw new Error('Model needs an id!');

    uri.segment(model.id);
    if (Array.isArray(relation)) {
      uri.segment(relation[0].constructor.type());
    } else {
      uri.segment(relation.constructor.type());
      if (relation.id) {
        uri.segment(relation.id);
      }
    }

    var data = Array.isArray(relation) ?
      relation.map(element => element.serialize()) : relation.serialize();

    const config = {
      method: relation.id ? 'PATCH' : 'POST',
      url: uri.href(),
      data: {
        data: data
      }
    };
    let response = await axios.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }

  /**
   * Removes a relation (or relations) of a model. The model needs an id.
   * @param {Model} model The model with the relationship
   * @param {Model} relation The related model(s)
   */
  async detach(model, relation) {
    var uri = this.uri.clone();
    uri.segment(this.source.type());
    if (!model.id) throw new Error('Model needs an id!');

    uri.segment(model.id);
    if (Array.isArray(relation)) {
      uri.segment(relation[0].constructor.type());
    } else {
      uri.segment(relation.constructor.type());
      if (relation.id) {
        uri.segment(relation.id);
      }
    }

    var data = Array.isArray(relation) ?
      relation.map(element => element.serialize()) : relation.serialize();

    const config = {
      method: 'DELETE',
      url: uri.href(),
      data: {
        data: data
      }
    };
    let response = await axios.request(config);
    return {
      meta: response.data.meta,
      data: this.target.deserialize(response.data.data, response.data.included)
    };
  }
}

export default JSONAPI;
