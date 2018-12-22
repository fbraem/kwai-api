/**
 * Defines a function that can be used to create a Vue mixin for registering
 * Vuex modules.
 * @param { ...object} module - A object representing a module. Property order
 * is important!
 * @returns A mixin object for Vue.
 *
 * The following example will register training/coach module if it is not yet
 * registered.
 * @example
 * import registerModule from 'mixin';
 * registerModule({
 *  training: trainingStore,
 *  coach: coachStore
 * });
  */
export default function(...modules) {
  return {
    beforeCreate() {
      modules.forEach((module) => {
        var namespaces = [];
        var state = this.$store.state;
        Reflect.ownKeys(module).forEach((namespace) => {
          namespaces.push(namespace);
          if (!state[namespace]) {
            this.$store.registerModule(namespaces, module[namespace]);
          }
          state = state[namespace];
        });
      });
    }
  };
};
