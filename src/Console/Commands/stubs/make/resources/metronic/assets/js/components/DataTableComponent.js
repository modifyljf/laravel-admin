/**
 * Data table component.
 */

import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';
import * as Config from '../config/app';
import _ from 'lodash';
import axios from '../helpers/axios';
import Swal from 'sweetalert2';

class DataTableComponent extends React.PureComponent {
    componentDidMount() {
        const {actions} = this.props;

        this.initDataTable();

        let that = this;
        let dataTable = this.dataTable;
        dataTable.on('kt-datatable--on-layout-updated', function () {
            $('.btn-hover-danger').on('click', function () {
                let rowId = this.dataset.rowId;
                that.destroy(rowId);
            });

            if (!_.isEmpty(actions)) {
                _.forEach(actions, function (action, index) {
                    $('.' + action['className']).on('click', function () {
                        let callback = action.handler;
                        if (!_.isNil(callback)) {
                            let rowId = this.dataset.rowId;
                            callback(rowId, dataTable);
                        }
                    });

                    if (action['enableClassName']) {
                        $('.' + action['enableClassName']).on('click', function () {
                            let callback = action.enableHandler;
                            if (!_.isNil(callback)) {
                                let rowId = this.dataset.rowId;
                                callback(rowId, dataTable);
                            }
                        });
                    }
                })
            }
        });
    }

    indexUrl() {
        const {restful, resource} = this.props;
        let {indexUrl} = this.props;

        if (_.isNil(indexUrl) && restful) {
            indexUrl = Config.APP_URL + '/' + resource.toLowerCase();
        }

        return indexUrl;
    }

    editUrl(rowId) {
        const {restful, resource} = this.props;
        let {editUrl} = this.props;

        if (_.isNil(editUrl) && restful) {
            editUrl = Config.APP_URL + '/' + resource.toLowerCase() + '/' + rowId + '/edit';
        } else if (editUrl.indexOf('{id}')) {
            editUrl.replace('{id}', rowId);
        }

        return editUrl;
    }

    showUrl(rowId) {
        const {restful, resource} = this.props;
        let {showUrl} = this.props;

        if (_.isNil(showUrl) && restful) {
            showUrl = Config.APP_URL + '/' + resource.toLowerCase() + '/' + rowId;
        } else if (showUrl.indexOf('{id}')) {
            showUrl.replace('{id}', rowId);
        }

        return showUrl;
    }


    destroy(rowId) {
        const {deleteHandler, afterDeleted, deleteErrorHandler, resource} = this.props;

        let dataTable = this.dataTable;

        if (deleteHandler) {
            deleteHandler(rowId, dataTable);

        } else {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-secondary btn-elevate btn-elevate-air',
                confirmButtonText: 'Yes, delete it!',
                confirmButtonClass: 'btn btn-primary btn-elevate btn-elevate-air',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: function () {
                    return new Promise(function (resolve, reject) {
                        axios.delete(`${Config.APP_URL}/${resource.toLowerCase()}/${rowId}`).then((response) => {
                            resolve(response);
                            if (afterDeleted) {
                                afterDeleted(response);
                            }
                        }).catch(error => {
                            Swal.close(() => {
                            });

                            if (deleteErrorHandler) {
                                deleteErrorHandler(error);
                            }
                            reject(error);
                        });
                    })
                }
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The record has been deleted.',
                        type: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-primary btn-elevate btn-elevate-air',
                    }).then(response => {
                        dataTable.reload();
                    }).catch(e => {

                    });
                } else {
                    "cancel" === result.dismiss &&
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your record is safe :)',
                        type: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-focus btn-elevate btn-elevate-air',
                    });
                }
            }).catch((e) => {
            });
        }
    }

    actionTemplate(row, index, dataTable) {
        const {deletable, editable} = this.props;
        const {actions} = this.props;

        let editUrl = this.editUrl(row.id);
        let actionsDiv = [];

        //create extra actions
        if (!_.isEmpty(actions)) {
            let actionList = actions.map((action, index) => {
                return (
                    <a key={index}
                       className={`dropdown-item ${!action.enableClassName ? action.className : (row.status ? action.className : action.enableClassName)}`}
                       href="#"
                       data-row-id={row.id}
                    >
                        <i className={!action.enableIconClass ? action.iconClass : (row.status ? action.iconClass : action.enableIconClass)}/>
                        {
                            !action.enableTitle ? action.title : (row.status ? action.title : action.enableTitle)
                        }
                    </a>
                );
            });

            let dropClass = "dropdown " + (dataTable.getPageSize() - index <= 4 ? "dropup" : "");

            actionsDiv.push(
                <div key="extraActions" className={dropClass}>
                    <a href="#"
                       className="btn btn-hover-brand btn-icon btn-pill"
                       data-toggle="dropdown"
                       data-row-id={row.id}
                    >
                        <i className="la la-ellipsis-h"/>
                    </a>
                    <div className="dropdown-menu dropdown-menu-right">
                        {actionList}
                    </div>
                </div>
            )
        }

        if (editable) {
            actionsDiv.push(
                <a href={editUrl}
                   className='btn btn-hover-brand btn-icon btn-pill'
                   title='Edit details'
                   key="edit"
                >
                    <i className='la la-edit'/>
                </a>
            )
        }

        if (deletable) {
            actionsDiv.push(
                <a key="delete" data-row-id={row.id}
                   className='btn btn-hover-danger btn-icon btn-pill'
                   title='Delete record'
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
    }

    initColumns() {
        const {deletable, editable, actions, defColumns} = this.props;

        if (deletable || editable || !_.isEmpty(actions)) {
            defColumns.push({
                field: 'Actions',
                title: 'Actions',
                sortable: false,
                width: 110,
                overflow: 'visible',
                textAlign: 'center',
                locked: {right: 'md'},
                template: (row, index, dataTable) => {
                    let actionTemplate = this.actionTemplate(row, index, dataTable);
                    return ReactDOMServer.renderToString(actionTemplate);
                }
            })
        }

        return defColumns;
    }

    getSearchColumns() {
        const {defColumns} = this.props;
        let searchColumns = [];

        _.forEach(defColumns, (defColumn, index) => {
            if (defColumn.searchable) {
                searchColumns.push(defColumn.field);
            }
        });

        return searchColumns;
    }

    getExtra() {
        const {extra} = this.props;

        return extra;
    }

    initDataTable() {
        const {onInit} = this.props;

        let defColumns = this.initColumns();
        let searchColumns = this.getSearchColumns();
        let extra = this.getExtra();
        let indexUrl = this.indexUrl();

        this.dataTable = $(this.dataTableNode).KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: indexUrl,
                        params: {
                            search_columns: searchColumns,
                            extra: extra,
                        },
                        map: function (raw) {
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
            search: {input: $('#generalSearch')},
            columns: defColumns,
        });

        if (onInit) {
            onInit(this.dataTable);
        }
    }

    render() {
        return (
            <div className='kt_datatable' ref={(dataTableNode) => {
                this.dataTableNode = dataTableNode;
            }}/>
        );
    }
}

DataTableComponent.propTypes = {
    deletable: PropTypes.bool,
    editable: PropTypes.bool,
    defColumns: PropTypes.array.isRequired,
    restful: PropTypes.bool.isRequired,
    resource: PropTypes.string.isRequired,
    indexUrl: PropTypes.string,
    createUrl: PropTypes.string,
    editUrl: PropTypes.string,
    showUrl: PropTypes.string,

    onInit: PropTypes.func,

    deleteHandler: PropTypes.func,
    afterDeleted: PropTypes.func,
    deleteErrorHandler: PropTypes.func,

    actions: PropTypes.array,
    extra: PropTypes.any,
};

DataTableComponent.defaultProps = {
    deletable: true,
    editable: true,
    restful: true,
    resource: '',
    actions: [],
};

export default DataTableComponent;
