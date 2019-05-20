import { Ability } from '@casl/ability';

/**
 * Function to get the type of the model
 */
function subjectName(item) {
  if (!item || typeof item === 'string') {
    return item;
  }
  return item.constructor.type();
}

export const ability = new Ability([], { subjectName });

// TODO: Also used in stores/auth ... Search for a better integration
import Lockr from 'lockr';
const USER_RULES_KEY = 'rules';

export const abilityPlugin = (store) => {
  ability.update(Lockr.get(USER_RULES_KEY, []));
  return store.subscribe((mutation, state) => {
    switch (mutation.type) {
    case 'auth/login':
      store.dispatch('auth/user');
      break;
    case 'auth/user':
      ability.update(state.auth.rules);
      break;
    case 'auth/logout':
      ability.update([]);
      break;
    }
  });
};
