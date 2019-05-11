import { AbilityBuilder } from '@casl/ability';

import store from '@/stores/root';

/**
 * Function to get the type of the model
 */
function subjectName(item) {
  if (!item || typeof item === 'string') {
    return item;
  }
  return item.constructor.type();
}

/**
 * Returns a function that defines the abilities
 */
export default () => {
  return AbilityBuilder.define({ subjectName }, (can, cannot) => {
    if (store.state.auth.user) {
      can('manage', 'all');
    } else {
      can('read', 'stories');
    }
  });
};
