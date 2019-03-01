/**
 * A factory method for creating a form
 */
const Form = (fields) => {
  const clearErrors = () => {
    Object.entries(fields).forEach((entry) => {
      const [, field] = entry;
      field.errors = [];
    });
  };
  const $valid = false;
  return { ...fields, $valid, clearErrors };
};

export default Form;
