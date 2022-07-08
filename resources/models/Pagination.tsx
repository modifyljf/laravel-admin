import {PaginationMeta} from './PaginationMeta';

/**
 *
 * @export
 * @interface Pagination
 */
export interface Pagination {
    /**
     *
     * @type {Array<object>}
     * @memberof Pagination
     */
    data?: Array<object>;
    /**
     *
     * @type {PaginationMeta}
     * @memberof Pagination
     */
    meta?: PaginationMeta;
}
