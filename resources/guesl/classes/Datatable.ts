//https://keenthemes.com/metronic/?page=docs&section=datatable
import DatatableInterface from "../types/DatatableInterface";

export default class Datatable implements DatatableInterface {
    private readonly datatable: any;

    constructor(datatable: any) {
        this.datatable = datatable;
    }

    column(index: number): DatatableInterface {
        this.datatable.column(index);

        return this;
    }

    columns(selector: string): DatatableInterface {
        this.datatable.column(selector);

        return this;
    }

    destroy(): void {
        this.datatable.destroy();
    }

    getColumn(columnName: string) {
        return this.datatable.getColumn(columnName);
    }

    getCurrentPage(): number {
        return this.datatable.getCurrentPage();
    }

    getDataSourceParam(param: string): any {
        return this.datatable.getDataSourceParam(param);
    }

    getDataSourceQuery(): any {
        return this.datatable.getDataSourceQuery();
    }

    getPageSize(): number {
        return this.datatable.getPageSize();
    }

    getRecord(id: string): any {
        return this.datatable.getRecord(id);
    }

    getSelectedRecords(): any {
        return this.datatable.getSelectedRecords();
    }

    getTotalRows(): number {
        return this.datatable.getTotalRows();
    }

    getValue(): any {
        return this.datatable.getValue();
    }

    hideColumn(columnName: string): void {
        return this.datatable.hideColumn(columnName);
    }

    load(): void {
        return this.datatable.load();
    }

    nodes(): any {
        return this.datatable.nodes();
    }

    reload(): void {
        return this.datatable.reload();
    }

    remove(): void {
        return this.datatable.remove();
    }

    rows(selector: string): DatatableInterface {
        this.datatable.rows(selector);
        return this;
    }

    setActive(cell: string): void {
        this.datatable.setActive(cell);
    }

    setActiveAll(active: boolean): void {
        this.datatable.setActiveAll(active);
    }

    setInactive(cell: string): void {
        this.datatable.setInactive(cell);
    }

    table(): any {
        this.datatable.table();
    }

    visible(visibility: boolean): void {
        this.datatable.visible(visibility);
    }

}

