import Model from './model';

function JSONAPI() {
    var _cache = Object.create(null);

    function initModel(type, id) {
        _cache[type] = _cache[type] || Object.create(null);
        _cache[type][id] = _cache[type][id] || new Model(type, id);

        return _cache[type][id];
    }

    function createObject(data) {
        var o = initModel(data.type, data.id);

        for(var a in data.attributes) {
            o.addAttribute(a, data.attributes[a]);
        }
        for(var r in data.relationships) {
            if (data.relationships[r].data == null) {
                // An empty one-to-one relationship for example can be null
                o.addRelation(r, null);
            } else if (Array.isArray(data.relationships[r].data)) {
                var relations = [];
                for(var d in data.relationships[r].data) {
                    relations.push(initModel(data.relationships[r].data[d].type, data.relationships[r].data[d].id));
                }
                o.addRelation(r, relations);
            } else {
                o.addRelation(r, initModel(data.relationships[r].data.type, data.relationships[r].data.id));
            }
        }
        return o;
    }

    function parse(json) {
        var result = {
            meta : json.meta
        };

        for(var i in json.included) {
            createObject(json.included[i]);
        }

        if ( Array.isArray(json.data) ) {
            result.data = [];

            for(var n in json.data) {
                result.data.push(createObject(json.data[n]));
            }
        } else {
            result.data = createObject(json.data);
        }
        return result;
    }

    return {
        parse(json) {
            return parse(json);
        }
    }
}

export default JSONAPI;
