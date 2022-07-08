export default interface Column {
    field?: string;
    title?: string;
    sortable?: boolean;
    width?: number;
    overflow?: string;
    selector?: boolean;
    textAlign?: 'center' | 'right' | 'left';
    searchable?: boolean;
    locked?: any;
    template?: (row: any, index: number, dataTable: any) => any;
    autoHide?: boolean;
}

