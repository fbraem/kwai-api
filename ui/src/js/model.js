function Model(type, id) {
    var id = id || null;
    this.id = id;
    this._type = type;
    this._attributes = Object.create(null);
    this._relationships = Object.create(null);
}

Model.prototype.addAttribute = function(attr, value) {
    this._attributes[attr] = value;
    Object.defineProperty(this, attr, {
            enumerable : true,
            configurable : true,
            get : function() { return this._attributes[attr]; },
            set : function(nv) { this._attributes[attr] = nv; }
        });
}

Model.prototype.addRelation = function(relation, model) {
    this._relationships[relation] = model;
    Object.defineProperty(this, relation, {
            enumerable : true,
            configurable : true,
            get : function() { return this._relationships[relation]; },
            set : function(nv) { this._relationships[relation] = nv; }
        });
}
    
Model.serialize = function(input) {
    var json = Object.create(null);
    if ( Array.isArray(input) ) {
        json.data = [];
        for(var i in input) {
            var jsonData = input[i].serialize();
            json.data.push(jsonData.data);
        }
    } else if ( input instanceof Model ) {
        // This must be a model
        json = input.serialize();
    }

    return json;
}

Model.prototype.serialize = function(opts) {
    var self = this;
    var json = {
        data : {
            type : this._type
        }
    };

    var opts = opts || Object.create(null);
    opts.attributes = opts.attributes || this._attributes;
    opts.relationships = opts.relationships || this._relationships;

    if ( this.id ) json.data.id = this.id;
    if ( opts.attributes.length !== 0 ) json.data.attributes = Object.create(null);
    if ( opts.relationships.length !== 0 ) json.data.relationships = Object.create(null);

    for(var k in opts.attributes) {
        json.data.attributes[k] = self[k];
    }

    for(var r in opts.relationships) {
        var model = self[r];
        if ( ! model) {
            json.data.relationships[r] = {
                data : null
            };
        } else if ( Array.isArray(model) ) {
            json.data.relationships[r] = {
                data : model.map(function(m) {
                    return {
                        type : m._type,
                        id : m.id
                    };
                })
            };
        } else {
            json.data.relationships[r] = {
                data : {
                    type : model._type,
                    id : model.id
                }
            };
        }
    }

    return json;
}

export default Model;
