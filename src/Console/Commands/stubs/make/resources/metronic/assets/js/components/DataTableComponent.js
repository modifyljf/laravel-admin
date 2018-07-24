/**
 * Data table component.
 */

import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';
import * as App from '../config/app';
import _ from 'lodash';
import '../helpers/interceptors';

class DataTableComponent extends React.PureComponent {
    componentDidMount() {
        this.initDataTable();
        let that = this;
        this.dataTable.on('m-datatable--on-layout-updated', function () {
            $('.btn-delete').on('click', function () {
                let rowId = this.dataset.rowId;
                that.destroy(rowId);
            });
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
        const {deleteHandler, afterDeleted, deleteErrorHandler} = this.props;

        if (deleteHandler) {
            deleteHandler(rowId, this.dataTable);

        } else {
            axiso.delete(`${App.APP_URL}/${rowId}`).then((response) => {
                if (afterDeleted) {
                    afterDeleted(response);
                }
            }).catch(error => {
                if (deleteErrorHandler) {
                    deleteErrorHandler(error);
                }
            });
        }
    }


    actionTemplate(t, e, a) {
        let editUrl = this.editUrl(t.id);

        return (
            <span>
                <a href={editUrl}
                   className='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'
                   title='Edit details'
                >
                    <i className='la la-edit'/>
                </a>

                <a data-row-id={t.id}
                   className='btn-delete m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill delete-btn'
                   title='Delete record'
                >
                    <i className='la la-trash'/>
                </a>
            </span>
        );
    }

    initDataTable() {
        const {onInit} = this.props;

        let defColumns = this.initColumns();
        let searchColumns = this.getSearchColumns();

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
            layout: {scroll: false, footer: true},
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
        const {defColumns} = this.props;

        if ((deletable || editable)) {
            defColumns.push({
                field: 'Actions',
                width: 110,
                title: 'Actions',
                sortable: false,
                overflow: 'visible',
                textAlign: 'center',
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
};

DataTableComponent.defaultProps = {
    deletable: true,
    editable: true,
    restful: true,
    resource: '',
};

export default DataTableComponent;
