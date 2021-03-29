export default class Errors
{
    /**
     * Create a new errors instance.
     *
     * @param  {object}  items
     * @return {void}
     */
    constructor(items = {})
    {
        this.fill(items);
    }

    /**
     * Get all the errors.
     *
     * @return {object}
     */
    all()
    {
        return this.items;
    }

    /**
     * Determine if we have any errors.
     *
     * @return {bool}
     */
    any()
    {
        return Object.keys(this.items).length > 0;
    }

    /**
     * Determine if an errors exists for the given field.
     *
     * @param  {string}  field
     * @return {bool}
     */
    has(field)
    {
        return this.items.hasOwnProperty(field);
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param  {string}  field
     * @return {mixed}
     */
    get(field)
    {
        if (this.has(field)) {
            return this.items[field];
        }
    }

    /**
     * Set the errors.
     *
     * @param  {string}  field
     * @param  {string}  message
     * @return {void}
     */
    set(field, message)
    {
        Object.assign(this.items, { [field]: message });
    }

    /**
     * Clear the given errors.
     *
     * @param  {string|null}  field
     * @return {void}
     */
    clear(field = null)
    {
        if (field) {
            delete this.items[field];
        } else {
            this.items = {};
        }
    }

    /**
     * Fill the errors by the given values.
     *
     * @param  {object}  items
     * @return {void}
     */
    fill(items)
    {
        this.items = JSON.parse(JSON.stringify(items));
    }
}
