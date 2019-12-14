import config from 'config';

/**
 * State of the store
 */
export const state = () => {
  return {
    page: {
      title: config.title,
      subTitle: ''
    },
    facebook: config.facebook,
    error: null,
    mail: config.mail,
    url: config.site,
    location: config.location
  };
};
