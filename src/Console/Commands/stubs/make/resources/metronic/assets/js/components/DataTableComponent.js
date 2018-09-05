/**
 * Data table component.
 */

import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';
import * as App from '../config/app';
import _ from 'lodash';
import axios from '../helpers/axios';
import swal from 'sweetalert2';

class DataTableComponent extends React.PureComponent {
    componentDidMount() {
        const {actions} = this.props;

        this.initDataTable();

        let that = this;
        let dataTable = this.dataTable;
        dataTable.on('m-datatable--on-layout-updated', function () {
            $('.btn-delete').on('click', function () {
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
                })
            }
        });
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

    indexUrl() {
        const {restful, resource} = this.props;
        let {indexUrl} = this.props;

        if (_.isNil(indexUrl) && restful) {
            indexUrl = App.APP_URL + '/' + resource.toLowerCase();
        }

        return indexUrl;
    }

    createUrl() {
        const {restful, resource} = this.props;
        let {createUrl} = this.props;

        if (_.isNil(createUrl) && restful) {
            createUrl = App.APP_URL + '/' + resource.toLowerCase();
        }

        return createUrl;
    }

    editUrl(selectedRowId) {
        const {restful, resource} = this.props;
        let {editUrl} = this.props;

        if (_.isNil(editUrl) && restful) {
            editUrl = App.APP_URL + '/' + resource.toLowerCase() + '/' + selectedRowId + '/edit';
        } else if (editUrl.indexOf('{id}')) {
            editUrl.replace('{id}', selectedRowId);
        }

        return editUrl;
    }

    showUrl(selectedRowId) {
        const {restful, resource} = this.props;
        let {showUrl} = this.props;

        if (_.isNil(showUrl) && restful) {
            showUrl = App.APP_URL + '/' + resource.toLowerCase() + '/' + selectedRowId;
        } else if (showUrl.indexOf('{id}')) {
            showUrl.replace('{id}', selectedRowId);
        }

        return showUrl;
    }

    destroy(rowId) {
        const {deleteHandler, afterDeleted, deleteErrorHandler, resource} = this.props;

        let dataTable = this.dataTable;

        if (deleteHandler) {
            deleteHandler(rowId, dataTable);

        } else {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-secondary m-btn m-btn--pill m-btn--icon',
                confirmButtonText: 'Yes, delete it!',
                confirmButtonClass: 'btn btn-focus m-btn m-btn--pill m-btn--air',
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !swal.isLoading(),
                preConfirm: function () {
                    return new Promise(function (resolve, reject) {
                        axios.delete(`${App.APP_URL}/${resource.toLowerCase()}/${rowId}`).then((response) => {
                            resolve(response);
                            if (afterDeleted) {
                                afterDeleted(response);
                            }
                        }).catch(error => {
                            swal.close(() => {
                            });

                            if (deleteErrorHandler) {
                                deleteErrorHandler(error);
                            }
                            reject(response);
                        });
                    })
                }
            }).then((result) => {
                if (result.value) {
                    swal({
                        title: 'Deleted!',
                        text: 'The record has been deleted.',
                        type: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-focus m-btn m-btn--pill m-btn--air',
                    });
                    dataTable.reload();

                } else {
                    "cancel" === result.dismiss &&
                    swal({
                        title: 'Cancelled',
                        text: 'Your record is safe :)',
                        type: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonClass: 'btn btn-focus m-btn m-btn--pill m-btn--air',
                    });
                }
            }).catch((e) => {
            });
        }
    }

    actionTemplate(t, e, a) {
        const {deletable, editable} = this.props;
        const {actions} = this.props;

        let editUrl = this.editUrl(t.id);

        let actionsDiv = [];
        if (!_.isEmpty(actions)) {
            let actionList = actions.map((action, index) => {
                return (
                    <a key={index} className={"dropdown-item " + action.className} href="#"
                       data-row-id={t.id}
                    >
                        <i className={action.iconClass}/>
                        {action.title}
                    </a>
                );
            });

            let dropClass = "dropdown" + (a.getPageSize() - e <= 4 ? " dropup" : "");
            actionsDiv.push(
                <div key="extraActions" className={dropClass}>
                    <a href="#"
                       className="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"
                       data-toggle="dropdown"
                       data-row-id={t.id}
                    >
                        <i className="la la-ellipsis-h"/>
                    </a>
                    <div className="dropdown-menu dropdown-menu-right">
                        {actionList}
                    </div>
                </div>
            );
        }

        if (editable) {
            actionsDiv.push(
                <a key="edit" href={editUrl}
                   className='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'
                   title='Edit details'
                >
                    <i className='la la-edit'/>
                </a>
            );
        }

        if (deletable) {
            actionsDiv.push(
                <a key="delete" data-row-id={t.id}
                   className='btn-delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill delete-btn'
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

    initDataTable() {
        const {onInit} = this.props;

        let defColumns = this.initColumns();
        let searchColumns = this.getSearchColumns();
        let extra = this.getExtra();

        let indexUrl = this.indexUrl();

        this.dataTable = $(this.dataTableNode).mDatatable({
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

        // $('#m_form_status').on('change', function () {
        //     t.search($(this).val(), 'Status');
        // });
        //
        // $('#m_form_type').on('change', function () {
        //     t.search($(this).val(), 'Type');
        // });
        //
        // $('#m_form_status, #m_form_type').selectpicker();

        if (onInit) {
            onInit(this.dataTable);
        }
    }

    initColumns() {
        const {deletable, editable} = this.props;
        const {actions} = this.props;
        const {defColumns} = this.props;

        if ((deletable || editable || !_.isEmpty(actions))) {
            defColumns.push({
                field: 'Actions',
                width: 110,
                title: 'Actions',
                sortable: false,
                overflow: 'visible',
                textAlign: 'center',
                locked: {right: "md"},
                template: (t, e, a) => {
                    let actions = this.actionTemplate(t, e, a);
                    return ReactDOMServer.renderToString(actions);
                }
            });
        }

        return defColumns;
    }

    render() {
        return (
            <div className='m_datatable' ref={(dataTableNode) => {
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
