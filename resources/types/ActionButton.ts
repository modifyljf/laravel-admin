export default interface ActionButton {
    tooltipTitle?: string;
    actionClass?: string;
    handleClick?: (datatable: any) => void;
}
