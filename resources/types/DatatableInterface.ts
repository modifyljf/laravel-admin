//https://keenthemes.com/metronic/?page=docs&section=datatable
export default interface DatatableInterface {
    load(): void;

    reload(): void;

    destroy(): void;

    getRecord(id: string): any;

    getColumn(columnName: string): any;

    getValue(): any;

    setActive(cell: string): void;

    setInactive(cell: string): void;

    setActiveAll(active: boolean): void;

    getSelectedRecords(): any;

    getDataSourceParam(param: string): any;

    getDataSourceQuery(): any;

    getCurrentPage(): number;

    getPageSize(): number;

    getTotalRows(): number;

    hideColumn(columnName: string): void;

    table(): any;

    rows(selector: string): DatatableInterface;

    columns(selector: string): DatatableInterface;

    column(index: number): DatatableInterface;

    remove(): void;

    visible(visibility: boolean): void;

    nodes(): any;
}

