import moment from 'moment';

export default date => {
  return moment(date, 'L', true).isValid();
};
