/**
 *
 * @export
 * @interface PaginationMeta
 */
export interface PaginationMeta {
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    total?: number;
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    per_page?: number;
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    current_page?: number;
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    last_page?: number;
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    from?: number;
    /**
     *
     * @type {number}
     * @memberof PaginationMeta
     */
    to?: number;
}