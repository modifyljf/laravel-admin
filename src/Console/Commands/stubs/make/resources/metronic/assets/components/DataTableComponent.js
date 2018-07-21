/**
 * Data table component.
 */

import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';
import * as App from '../config/app';

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

    destroy(rowId) {
        const {destroyHandler} = this.props;

        if (destroyHandler) {
            destroyHandler(rowId, this.dataTable);
        } else {
            //axiso.delete();
        }
    }

    actionTemplate(t, e, a) {
        return (
            <span>
                <a href={`${App.APP_URL}DummyEditUrl`}
                   className='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'
                   title='Edit details'
                >
                    <i className='la la-edit'/>
                </a>

                <a data-row-id={t.RecordID}
                   className='btn-delete m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill delete-btn'
                   title='Delete record'
                >
                    <i className='la la-trash'/>
                </a>
            </span>
        );
    }

    initDataTable() {
        let defColumns = this.initColumns();

        this.dataTable = $(this.dataTableNode).mDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'https://keenthemes.com/metronic/preview/inc/api/datatables/demos/default.php',
                        map: function (t) {
                            let e = t;
                            return void 0 !== t.data && (e = t.data), e
                        }
                    }
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            layout: {scroll: false, footer: true},
            sortable: true,
            pagination: true,
            toolbar: {items: {pagination: {pageSizeSelect: [10, 20, 30, 50, 100]}}},
            search: {input: $('#generalSearch')},
            columns: defColumns,
        });

        $('#m_form_status').on('change', function () {
            t.search($(this).val(), 'Status');
        });

        $('#m_form_type').on('change', function () {
            t.search($(this).val(), 'Type');
        });

        $('#m_form_status, #m_form_type').selectpicker();
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
    defColumns: PropTypes.array.required,
};

DataTableComponent.defaultProps = {
    deletable: true,
    editable: true,
};

export default DataTableComponent;
