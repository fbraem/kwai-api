import moment from 'moment';

export default time => {
  return moment(time, 'HH:mm', true).isValid();
};
