export default interface DatatablePagination {
    page?: number;
    pages?: number;
    perpage?: number;
    total?: number;
    meta?: Array<any>;
    data?: Array<any>;
}
