import { Attribute } from './Attribute';

const TYPE = Symbol('TYPE');

/**
 * Class which keeps all created model objects in a cache. When the model
 * is not yet available in the cache, it will be created and stored.
 */
class Cache {
  /**
   * Creates the cache
   */
  constructor() {
    this.cache = Object.create(null);
  }

  /**
   * Searches the cache for the model with the given constructor and id. When
   * it doesn't find it, it will create it.
   * @param {function} ctor The constructor of the Model class.
   * @param {string} id The id of the model.
   * @return {object} The cached or a new model
   */
  getModel(ctor, id) {
    this.cache[ctor.name] = this.cache[ctor.name] || Object.create(null);
    /*eslint new-cap: ["error", { "newIsCapExceptions": ["ctor"] }]*/
    this.cache[ctor.name][id] = this.cache[ctor.name][id] || new ctor(id);
    return this.cache[ctor.name][id];
  }
}

var cache = new Cache();

/**
 * Our private function to create a model from JSONAPI data. Before calling this
 * function it must be bind against the constructor of the model.
 * @param {object} data
 * @param {array} included
 * @return {object} An instance of the model
 * @see deserialize
 */
function createModel(data, included) {
  var me = cache.getModel(this, data.id);

  // Set all fields
  Object.entries(this.fields()).forEach((entry) => {
    const [key, attr] = entry;
    if (data.attributes[key]) {
      if (attr instanceof Attribute) {
        me[key] = attr.from(data.attributes[key]);
      } else {
        me[key] = data.attributes[key];
      }
    }
  });

  // Set all relations
  var relationships = this.relationships();
  Object.keys(relationships).forEach((key) => {
    if (data.relationships && data.relationships[key]) {
      if (Array.isArray(data.relationships[key].data)) {
        me[key] = [];
        data.relationships[key].data.forEach((relation) => {
          var includedData = included.find((o, i) => {
            return o.type === relationships[key].type() && o.id === relation.id;
          });
          if (includedData === null) {
            console.log('No included data found for relation ', key);
          }
          var model =
            relationships[key].deserialize(includedData, included);
          me[key].push(model);
        });
      } else if (data.relationships[key].data !== null) {
        var includedData = included.find((o, i) => {
          return o.type === relationships[key].type()
            && o.id === data.relationships[key].data.id;
        });
        if (includedData === null) {
          console.log('No included data found for relation ', key);
        }
        me[key] =
          relationships[key].deserialize(includedData, included);
      } else {
        me[key] = null;
      }
    }
  });

  // Handle all computed values
  var computed = this.computed();
  Object.entries(computed).forEach((entry) => {
    const [key, fn] = entry;
    me[key] = fn(me);
  });

  return me;
};

/**
 * Base Model class
 */
class Model {
  /**
   * Creates a new model and will set all internal properties.
   * @param {string} id The id of the model.
   */
  constructor(id) {
    this[TYPE] = this.constructor.type();
    this.id = id;
  }

  /**
   * Default implementation. Returns an empty object.
   */
  static fields() {
    return Object.create(null);
  }

  /**
   * Default implementation. Returns an empty object.
   */
  static relationships() {
    return Object.create(null);
  }

  /**
   * Default implementation. Returns an empty object.
   */
  static computed() {
    return Object.create(null);
  }

  /**
   * Default implementation. Returns an empty array
   */
  static namespace() {
    return [];
  }

  /**
   * Class method that will start creating all models from a JSONAPI structure
   * @param {object|array} data The data of the JSONAPI response
   * @param {array} included The included data of the JSONAPI response
   * @return {object|array} An instance of the model or an array with instances.
   */
  static deserialize(data, included) {
    included = included || [];
    if (Array.isArray(data)) {
      return data.map(element => createModel.bind(this)(element, included));
    }
    return createModel.bind(this)(data, included);
  }

  /**
   * Method to serialize a model to a JSONAPI structure.
   * @return {object} An object which contains a JSONAPI structure.
   */
  serialize() {
    var json = Object.create(null);
    json.type = this[TYPE];
    if (this.id) json.id = this.id;

    // Handle all fields
    json.attributes = Object.create(null);
    Object.entries(this.constructor.fields()).forEach((entry) => {
      const [key, attr] = entry;
      if (!attr.isReadonly()) {
        if (key in this) {
          if (this[key] && attr instanceof Attribute) {
            json.attributes[key] = attr.to(this[key]);
          } else {
            json.attributes[key] = this[key];
          }
        }
      }
    });

    // Handle all relations
    var relationships = Object.create(null);
    Object.keys(this.constructor.relationships()).forEach((relation) => {
      relationships[relation] = Object.create(null);
      if (Array.isArray(this[relation])) {
        relationships[relation].data = this[relation].map((element) => {
          var relatedObject = Object.create(null);
          relatedObject.type = element[TYPE];
          relatedObject.id = element.id;
          return relatedObject;
        });
      } else {
        if (this[relation]) {
          relationships[relation].data = Object.create(null);
          relationships[relation].data.type = this[relation][TYPE];
          relationships[relation].data.id = this[relation].id;
        }
      }
    });
    json.relationships = relationships;
    return json;
  }
}

export default Model;
