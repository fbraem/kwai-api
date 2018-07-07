import URI from 'urijs';
import moment from 'moment';

class Cache {
  constructor() {
    this._cache = Object.create(null);
  }

  getModel(ctor, id, data, includes) {
    this._cache[ctor.name] = this._cache[ctor.name] || Object.create(null);
    this._cache[ctor.name][id] = this._cache[ctor.name][id] || this.createModel(ctor, id, data, includes);
    return this._cache[ctor.name][id];
  }

  createModel(ctor, id, data, includes) {
    var model = new ctor();
    model.id = id;

    if (!data) return model;

    model.fields().forEach((field) => {
      model[field] = data.attributes[field];
    });

    var dates = model.dates();
    Object.keys(dates).forEach((key) => {
      var format = dates[key];
      if (data.attributes[key]) {
          model[key] = moment(data.attributes[key], format);
      }
    });

    Object.keys(model.relationships()).forEach((relationName) => {
      model[relationName] = null;
      if (data.relationships) {
          if (data.relationships[relationName]) {
            var relatedModel = model.relationships()[relationName];
            var relatedData = data.relationships[relationName].data;
            if (Array.isArray(relatedData)) {
              model[relationName] = [];
              relatedData.forEach((r) => {
                var included = includes[relatedModel.resourceName()][r.id];
                model[relationName].push(this.getModel(relatedModel.constructor, r.id, included, includes));
              });
            } else {
              var included = includes[relatedModel.resourceName()][relatedData.id];
              model[relationName] = this.getModel(relatedModel.constructor, relatedData.id, included, includes);
            }
          }
      }
    });

    var computed = model.computed();
    Object.keys(computed).forEach((key) => {
      model[key] = computed[key](model);
    });

    return model;
  }
}

export default class Model {
  constructor() {
    this._type = this.resourceName();
    this.reset();
  }

  baseURL() {
    return null;
  }

  namespace() {
      return null;
  }

  bulk() {
      return null;
  }

  fields() {
    return [];
  }

  dates() {
    return [];
  }

  relationships() {
    return {};
  }

  computed() {
    return [];
  }

  resourceName() {
    return null;
  }

  addPath(path) {
    this._uri.segment(path);
    return this;
  }

  resourceUrl() {
      var uri = new URI('');
      var segment = [this.baseURL()];
      var ns = this.namespace();
      if (ns) segment.push(ns);
      segment.push(this._type);
      uri.segment(segment);
      return uri;
  }

  reset() {
    this._uri = this.resourceUrl();
  }

  async request(config) {
    return {};
  }

  all() {
      return this.get();
  }

  where(filter, value) {
      let q = this._uri.search(true);
      q['filter[' + filter + ']'] = value;
      this._uri.search(q);
      return this;
  }

  async get() {
    const config = {
      method: 'GET',
      url : this._uri.href()
    };
    this.reset();
    let response = await this.request(config);
    return this.respond(response);
  }

  async delete() {
      var segments = this._uri.segment();
      segments.push(this.id);
      this._uri.segment(segments);
      const config = {
          method : 'DELETE',
          url : this._uri.href()
      }
      this.reset();

      let response = await this.request(config);
      return this.respond(respond);
  }

  async find(id) {
      var segments = this._uri.segment();
      segments.push(id);
      this._uri.segment(segments);
      const config = {
        method: 'GET',
        url : this._uri.href()
      };
      this.reset();
      let response = await this.request(config);
      return this.respond(response);
  }

  async save() {
      this.reset();
      if (this.id) {
          var segments = this._uri.segment();
          segments.push(this.id);
          this._uri.segment(segments);
      }

      var ourData = this.serialize();
      var bulk = this.bulk();
      var data;
      if (bulk) {
          data = [ ourData ];
          bulk.forEach((b) => {
             if (this[b]) {
                 if (Array.isArray(this[b])) {
                     this[b].forEach((e) => {
                        data.push(e.serialize());
                     });
                 } else {
                     data.push(this[b].serialize());
                 }
             }
          });
      } else {
          data = ourData;
      }
      let config = {
          url : this._uri.href(),
          data : data,
         method : this.id ? 'PATCH' : 'POST'
     };
      let response = await this.request(config);
      return this.respond(response);
  }

  async attach(model) {
      this.reset();

      var url = this._uri.href() + '/' + this.id + '/' + model._type;
      if ( model.id ) {
          url += '/' + model.id;
      }

      let config = {
          url : url,
          data : model.serialize(),
          method : 'POST'
      };
      let response = await this.request(config);
      return this.respond(response);
  }

  with(resourceName) {
    var query = this._uri.query(true);
    if (query.include) {
      query.include += ',' + resourceName;
    } else {
      query.include = resourceName;
    }
    this._uri.query(query);
    return this;
  }

  respond(response) {
    let json = response.data;
    if (json) {
      // Make it easy to lookup included models
      let included = {};
      if (json.included) {
        json.included.forEach((data) => {
          var includedModel = data;
          if (!included[includedModel.type]) {
            included[includedModel.type] = {};
          }
          included[includedModel.type][includedModel.id] = includedModel;
        });
      }

      var cache = new Cache();
      let result = null;
      if (Array.isArray(json.data)) {
        result = [];
        json.data.forEach((element) => {
          var model = cache.getModel(this.constructor, element.id, element, included);
          result.push(model);
        });
      } else {
          result = cache.getModel(this.constructor, json.data.id, json.data, included);
      }
      return result;
    }
    return null;
  }

 serialize(opts) {
    var json = {
        data : {
            type : this._type
        }
    };

    if ( this.id ) json.data.id = this.id;

    json.data.attributes = Object.create(null);
    this.fields().forEach((field) => {
        json.data.attributes[field] = this[field];
    });

    Object.keys(this.dates()).forEach((key) => {
      var format = this.dates()[key];
      if (this[key]) {
          json.data.attributes[key] = this[key].format(format);
      }
    });

    var relations = Object.create(null);
    Object.keys(this.relationships()).forEach((relationName) => {
        if (this[relationName]) {
            relations[relationName] = Object.create(null);
            if (Array.isArray(this[relationName])) {
                var data = [];
                this[relationName].forEach((model) => {
                    data.push({
                        id : model.id,
                        type : model._type
                    })
                });
                relations[relationName].data = data;
            } else {
                data = Object.create(null);
                data.id = this[relationName].id;
                data.type = this[relationName]._type;
                relations[relationName].data = data;
            }
        }
    });
    if (Object.keys(relations).length > 0) {
        json.data.relationships = relations;
    }
    return json;
  }
}
