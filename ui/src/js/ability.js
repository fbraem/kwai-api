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

export default AbilityBuilder.define({ subjectName }, (can, cannot) => {
  if (store.state.global.user && store.state.global.user.authenticated) {
    can('manage', 'all');
  } else {
    can('read', 'stories');
  }
});
