export default function(modules) {
  return {
    beforeCreate() {
      modules.forEach((module) => {
        if (!this.$store.state[module.namespace]) {
          this.$store.registerModule(module.namespace, module.store);
        }
      });
    }
  };
};
