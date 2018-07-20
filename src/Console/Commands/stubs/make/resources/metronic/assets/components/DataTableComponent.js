/**
 * Data table component.
 */

import * as App from '../config/app';
import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';

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
        alert(rowId);
    }

    actionTemplate(t, e, a) {
        console.log(t, e, a);
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

        let defColumns = [{
            field: 'RecordID',
            title: '#',
            sortable: false,
            width: 40,
            selector: false,
            textAlign: 'center'
        }, {
            field: 'OrderID',
            title: 'Order ID',
            filterable: false,
            width: 150,
            template: '{{OrderID}} - {{ShipCountry}}'
        }, {
            field: 'ShipCountry',
            title: 'Ship Country',
            attr: {nowrap: 'nowrap'},
            width: 150,
            template: function (t) {
                return t.ShipCountry + '- ' + t.ShipCity
            }
        }, {field: 'ShipCity', title: 'Ship City'}, {
            field: 'Currency',
            title: 'Currency',
            width: 100
        }, {field: 'ShipDate', title: 'Ship Date', type: 'date', format: 'MM/DD/YYYY'}, {
            field: 'Latitude',
            title: 'Latitude',
            type: 'number'
        }, {
            field: 'Status', title: 'Status', template: function (t) {
                let e = {
                    1: {title: 'Pending', class: 'm-badge--brand'},
                    2: {title: 'Delivered', class: 'm-badge--metal'},
                    3: {title: 'Canceled', class: 'm-badge--primary'},
                    4: {title: 'Success', class: 'm-badge--success'},
                    5: {title: 'Info', class: 'm-badge--info'},
                    6: {title: 'Danger', class: 'm-badge--danger'},
                    7: {title: 'Warning', class: 'm-badge--warning'}
                };

                return '<span class="m-badge ' + e[t.Status].class + ' m-badge--wide">' + e[t.Status].title + "</span>"
            }
        }, {
            field: 'Type', title: 'Type', template: function (t) {
                let e = {
                    1: {title: 'Online', state: 'danger'},
                    2: {title: 'Retail', state: 'primary'},
                    3: {title: 'Direct', state: 'accent'}
                };

                return '<span class="m-badge m-badge--' + e[t.Type].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' + e[t.Type].state + '">' + e[t.Type].title + "</span>"
            }
        }];

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
                    let str = ReactDOMServer.renderToString(actions);
                    console.log(str);
                    return str;
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
};

DataTableComponent.defaultProps = {
    deletable: true,
    editable: true,
};

export default DataTableComponent;
