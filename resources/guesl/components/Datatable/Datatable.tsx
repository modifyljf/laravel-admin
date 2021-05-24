import React, {useEffect, useRef, useState} from 'react';
import ReactDOMServer from 'react-dom/server';
import {Column, RowActionButton, ActionButton} from '../../types';
import DatatableObject from '../../classes/Datatable';
import * as Axios from '../Utilities/Axios';
import * as _ from 'lodash';
import Swal from 'sweetalert2';
import Toast from '../Utilities/Toast';
import Criterion from "../../types/Criterion";
import RowClass from '../../types/RowClass';

interface DatatableProps {
    baseUri: string;
    token: string;
    deletable?: boolean;
    editable?: boolean;
    multiple?: boolean;
    rowClass?: RowClass;
    defColumns: Array<Column>;
    filters?: Array<Criterion>,
    restful?: boolean,
    resource?: string,
    indexUrl?: string,
    createUrl?: string,
    editUrl?: string,
    showUrl?: string,

    onInit?: (table: any) => void,
    editHandler?: (rowId: string, dataTable: any) => void,
    deleteHandler?: (rowId: string, dataTable: any) => void,
    afterDeleted?: (response: any) => void,
    deleteErrorHandler?: (error: any) => void,
    confirmButtonColor?: string;
    cancelButtonColor?: string;

    actions?: Array<RowActionButton>,
    toolActions?: Array<ActionButton>,
    showTimestamp?: boolean,
}

const Datatable = (props: DatatableProps) => {
    const {baseUri, token, restful, resource, indexUrl, editUrl, showUrl, showTimestamp} = props;
    const {filters, multiple, rowClass} = props;
    const {onInit} = props;
    const {deletable, editable, actions, defColumns} = props;
    const {deleteHandler, afterDeleted, deleteErrorHandler} = props;
    const {editHandler} = props;
    const {confirmButtonColor, cancelButtonColor} = props;

    const datatableRef = useRef(null);
    const [datatable, setDatatable]: [any, any] = useState(null);
    useEffect(() => {
        initDataTable();
    }, []);

    const getIndexUrl = () => {
        let url = indexUrl + '';
        let resourceUrl = resource + '';

        if (_.isEmpty(url) && restful) {
            url = baseUri + '/' + _.toLower(resourceUrl);
        }

        return url;
    };

    const getEditUrl = (rowId: string) => {
        if (editHandler) {
            return 'javascript:void(0);';
        }

        let url = editUrl + '';
        let resourceUrl = resource + '';

        if (_.isEmpty(url) && restful) {
            url = baseUri + '/' + _.toLower(resourceUrl) + '/' + rowId + '/edit';
        } else if (resourceUrl.indexOf('{id}')) {
            url = _.replace(url, '{id}', rowId);
        }

        return url;
    };

    const getShowUrl = (rowId: string) => {
        let url = showUrl + '';
        let resourceUrl = resource + '';

        if (_.isEmpty(url) && restful) {
            url = baseUri + '/' + _.toLower(resourceUrl) + '/' + rowId;

        } else if (url.indexOf('{id}')) {
            url = _.replace(url, '{id}', rowId);
        }

        return url;
    };

    const getActionColumnWidth = () => {
        let initWidth = 50;
        if (deletable) initWidth += 30;
        if (editable) initWidth += 30;
        if (!_.isEmpty(actions)) initWidth += 30;

        return initWidth;
    };

    const getExtraActions = (row: any, index: number, datatable: any) => {
        //create extra actions
        if (actions && !_.isEmpty(actions)) {
            if (_.size(actions) > 1) {
                let actionList = actions.map((action, index) => {
                    let itemClassName = row.status ? action.enableClassName : action.className;
                    let itemIconClassName = row.status ? action.enableIconClass : action.iconClass;
                    let itemTitle = row.status ? action.enableTitle : action.title;

                    return (
                        <a key={index}
                           className={`dropdown-item ${itemClassName}`}
                           data-row-id={row.id}
                        >
                            <i className={itemIconClassName}/>
                            &nbsp;&nbsp;{itemTitle}
                        </a>
                    );
                });

                let dropClass = 'dropdown ' + (datatable.getPageSize() - index <= 4 ? 'dropup' : '');
                return (
                    <div key='extraActions' className={dropClass}>
                        <a className='btn btn-hover-brand btn-icon btn-pill'
                           data-toggle='dropdown'
                           data-row-id={row.id}
                        >
                            <i className='la la-ellipsis-h'/>
                        </a>
                        <div className='dropdown-menu dropdown-menu-right'>
                            {actionList}
                        </div>
                    </div>
                );

            } else {
                const action: RowActionButton = _.first(actions) as RowActionButton;
                let itemClassName = row.status ? action.enableClassName : action.className;
                let itemIconClassName = row.status ? action.enableIconClass : action.iconClass;
                let itemTitle = row.status ? action.enableTitle : action.title;

                return (
                    <a className={`btn btn-hover-success btn-icon btn-pill ${itemClassName}`}
                       title={itemTitle}
                       key='extra'
                       data-row-id={row.id}
                    >
                        <i className={itemIconClassName}/>
                    </a>
                );
            }
        }

        return null;
    };

    const getActionTemplate = (row: any, index: number, datatable: any) => {
        let editUrl = getEditUrl(row.id);
        let actionsDiv = [];

        let extraActions = getExtraActions(row, index, datatable);
        if (extraActions && !_.isEmpty(extraActions)) {
            actionsDiv.push(extraActions);
        }

        if (editable) {
            actionsDiv.push(
                <a href={editUrl}
                   className='btn btn-sm btn-clean btn-icon btn-hover-primary btn-edit'
                   title='Edit details'
                   key='edit'
                   data-row-id={row.id}
                >
                    <i className='la la-edit'/>
                </a>
            );
        }

        if (deletable) {
            actionsDiv.push(
                <a key='delete'
                   className='btn btn-sm btn-clean btn-icon btn-hover-danger btn-remove'
                   title='Delete record'
                   data-row-id={row.id}
                >
                    <i className='la la-trash'/>
                </a>
            );
        }

        return (
            <span>
                {actionsDiv}
            </span>
        );
    };

    const initColumns = () => {
        if (deletable || editable || !_.isEmpty(actions)) {
            const initWidth = getActionColumnWidth();

            defColumns.push({
                field: 'Actions',
                title: 'Actions',
                sortable: false,
                width: initWidth,
                overflow: 'visible',
                textAlign: 'center',
                locked: {right: 'md'},
                template: (row, index, datatable) => {
                    let actionTemplate = getActionTemplate(row, index, datatable);
                    return ReactDOMServer.renderToString(actionTemplate);
                }
            })
        }

        return defColumns;
    };

    const getSearchColumns = (): Array<string> => {
        let searchColumns: Array<string> = [];

        _.forEach(defColumns, (defColumn: Column) => {
            if (defColumn.searchable && !_.isNil(defColumn.field)) {
                searchColumns.push(defColumn.field);
            }
        });

        return searchColumns;
    };

    const edit = (dataTable: any, rowId: string) => {
        if (editHandler) {
            editHandler(rowId, dataTable);
        }
    };

    const destroy = (dataTable: any, rowId: string) => {
        if (deleteHandler) {
            deleteHandler(rowId, dataTable);
            return;
        }
        const axios = Axios.createAxiosInstance(baseUri, token);
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            cancelButtonColor: cancelButtonColor,
            confirmButtonText: 'Yes, delete it!',
            confirmButtonColor: confirmButtonColor,
            showLoaderOnConfirm: true,
            heightAuto: false,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm:
                () => {
                    return new Promise(function (resolve, reject) {
                        axios.delete(`${_.toLower(resource)}/${rowId}`).then((response) => {
                            dataTable.reload();
                            resolve(response);
                            if (afterDeleted) {
                                afterDeleted(response);
                            }
                        }).catch(error => {
                            dataTable.reload();
                            Swal.close();

                            if (deleteErrorHandler) {
                                deleteErrorHandler(error);
                            }
                            reject(error);
                        });
                    })
                }
        }).then((result: any) => {
            if (result.value) {
                return Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'The record has been deleted.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: confirmButtonColor,
                    heightAuto: false,
                });
            } else {
                Swal.close();
            }
        }).catch((e: any) => {
            console.warn(e);
        });
    };

    const initDataTable = () => {
        let columns = initColumns();
        let searchColumns = getSearchColumns();
        let indexUrl = getIndexUrl();

        // @ts-ignore
        let dt = $(datatableRef.current).KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: indexUrl,
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        params: {
                            search_columns: searchColumns,
                            filters: filters,
                        },
                        map: function (raw: any) {
                            // sample data mapping
                            let dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        }
                    }
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
                saveState: {
                    cookie: false,
                    webstorage: false,
                }
            },
            layout: {scroll: true, footer: true},
            sortable: true,
            pagination: true,
            toolbar: {items: {pagination: {pageSizeSelect: [10, 20, 30, 50, 100]}}},
            search: {
                input: $('#kt_datatable_search_query'),
                key: 'generalSearch'
            },
            columns: columns,
        });

        if (onInit) {
            onInit(dt);
        }

        addTableListener(dt);

        // Set datatable status after the datatable was initialized.
        setDatatable(new DatatableObject(dt));
    };

    const addTableListener = (datatable: any) => {
        datatable.on('datatable-on-ajax-fail', function () {
            Toast.show(Toast.TYPE_ERROR, 'Something went wrong!');
        });

        datatable.on('datatable-on-layout-updated', function () {
            $('.btn-edit').on('click', function () {
                let rowId = this.dataset.rowId + '';
                edit(datatable, rowId);
            });

            $('.btn-remove').on('click', function () {
                let rowId = this.dataset.rowId + '';
                destroy(datatable, rowId);
            });

            if (!_.isEmpty(actions)) {
                _.forEach(actions, function (action) {
                    let className = action.enableClassName ? action.enableClassName : action.className;
                    let callback = action.enableClassName ? action.enableHandler : action.handler;

                    $(`.${className}`).on('click', function () {
                        if (!_.isNil(callback)) {
                            let rowId = this.dataset.rowId + '';
                            callback(rowId, datatable);
                        }
                    });
                })
            }
        });
    };

    return (
        <div className='datatable datatable-bordered datatable-head-custom' ref={datatableRef}/>
    );
};

Datatable.defaultProps = {
    deletable: true,
    editable: true,
    restful: true,
    filters: [],
    multiple: false,
    showTimestamp: true,
    rowClass: {},
    resource: '',
    indexUrl: '',
    createUrl: '',
    editUrl: '',
    showUrl: '',
    actions: [],
    confirmButtonColor: '#5d78ff',
    cancelButtonColor: '#ccc',
};

export default Datatable;
