import JSONAPI from '@/js/JSONAPI';
import Event from '@/models/Event';

async function browse({ dispatch, commit }, payload) {
  const api = new JSONAPI({ source: Event });
  if (payload.year) {
    api.where('year', payload.year);
    if (payload.month) {
      api.where('month', payload.month);
    }
  }
  dispatch('wait/start', 'event.browse', { root: true });
  try {
    commit('events', await api.get());
  } catch (error) {
    commit('error', error);
    throw error;
  } finally {
    dispatch('wait/end', 'event.browse', { root: true });
  }
}

async function save({ dispatch, commit }, event) {
  try {
    const api = new JSONAPI({ source: Event });
    const result = await api.save(event);
    commit('event', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    throw error;
  }
}

async function read({ dispatch, getters, commit }, payload) {
  var event = getters['event'](payload.id);
  if (event) { // already read
    commit('error', null); // Reset error
    return event;
  }

  dispatch('wait/start', 'event.read', { root: true });
  try {
    const api = new JSONAPI({ source: Event });
    const result = await api.get(payload.id);
    commit('event', result);
    return result.data;
  } catch (error) {
    commit('error', error);
    dispatch('wait/end', 'event.read', { root: true });
    throw error;
  } finally {
    dispatch('wait/end', 'event.read', { root: true });
  }
}

/**
 * When a story was read in another instance of this module, set can be
 * used to make it available in the current instance.
 */
function set({ commit }, event) {
  commit('event', { data: event });
}

function create({ commit}) {
  commit('active', new Event);
}

export const actions = {
  browse,
  save,
  read,
  set,
  create
};
