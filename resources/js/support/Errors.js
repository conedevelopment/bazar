export default class Errors
{
    /**
     * Create a new errors instance.
     *
     * @param  {object}  errors
     * @return {void}
     */
    constructor(errors = {})
    {
        this.errors = errors;
    }

    /**
     * Get all the errors.
     *
     * @return {object}
     */
    all()
    {
        return this.errors;
    }

    /**
     * Determine if an errors exists for the given field.
     *
     * @param  {string}  field
     * @return {bool}
     */
    has(field)
    {
        return this.errors.hasOwnProperty(field);
    }

    /**
     * Determine if we have any errors.
     *
     * @return {bool}
     */
    any()
    {
        return Object.keys(this.errors).length > 0;
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param  {string}  field
     * @return {mixed}
     */
    get(field)
    {
        if (this.errors[field]) {
            return this.errors[field];
        }
    }

    /**
     * Record the new errors.
     *
     * @param  {object}  errors
     * @return {void}
     */
    set(errors = {})
    {
        this.errors = errors;
    }

    /**
     * Clear one or all error fields.
     *
     * @param  {string}  field
     * @return {void}
     */
    clear(field)
    {
        if (field) {
            delete this.errors[field];

            this.errors = Object.assign({}, this.errors);
        } else {
            this.errors = {};
        }
    }
}
