export default interface RowActionButton {
    className?: string;
    enableClassName?: string;
    title?: string;
    enableTitle?: string;
    iconClass?: string;
    enableIconClass?: string;
    handler?: (rowId: string, datatable: any) => any;
    enableHandler?: (rowId: string, datatable: any) => any;
}
