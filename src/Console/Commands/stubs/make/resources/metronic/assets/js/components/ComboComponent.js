import React from 'react';
import PropTypes from 'prop-types';
import * as App from '../config/app';
import _ from 'lodash';

class ComboComponent extends React.PureComponent {
    constructor(props) {
        super(props);
        this.state = {
            optionPageObject: {},
        };
    }

    componentDidMount() {
        this.initSelector();
        this.query();
    }

    componentDidUpdate() {
        this.updateSelector();
    }

    query(search) {
        const {modelClass} = this.props;

        let searchColumn = this.getSearchColumns();
        let pageSize = this.getPageSize();

        axios.get(`${App.APP_URL}/combosearch`, {
            params: {
                model_class: modelClass,
                pagination: {
                    page: 1,
                    perpage: pageSize
                },
                query: {
                    generalSearch: search,
                },
                search_columns: searchColumn
            },
        }).then(response => {
            let optionPageObject = response.data;
            this.setState({
                optionPageObject: optionPageObject,
            });

            this.updateSelector();

        }).catch(error => {
            console.error(error);
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

    getPageSize() {
        const {size} = this.props;
        if (_.isNil(size) || size <= 0) {
            return 9;
        }
        return size;
    }

    getInitValue() {
        const {initValue} = this.props;
        if (_.isNil(initValue) || _.trim(initValue) === '') {
            return {};
        }
        return JSON.parse(initValue);
    }

    getIdKey() {
        const {idKey} = this.props;
        if (_.isNil(idKey)) {
            return 'id';
        }
        return idKey;
    }

    getOptionList() {
        const {optionPageObject} = this.state;
        const initValue = this.getInitValue();

        let options = [];
        if (!_.isEmpty(initValue)) {
            options.push(initValue);
        }

        if (!_.isEmpty(optionPageObject)) {
            options = options.concat(optionPageObject.data);
        }

        const idKey = this.getIdKey();
        return options.map((option, index) => {
            let optionText = this.getOptionText(option);
            return (
                <option key={index} value={option[idKey]}>
                    {optionText}
                </option>
            );
        });
    }

    getOptionText(option) {
        const {showColumns} = this.props;
        let optionTextList = showColumns.map((key) => {
            return !_.isNil(option[key]) ? option[key] : '';
        });

        return _.join(optionTextList, ' | ');
    }


    initSelector() {
        const {size, title} = this.props;
        const initValue = this.getInitValue();
        const idKey = this.getIdKey();

        let thisClass = this;
        $(this.selector).selectpicker({
            style: 'btn btn-primary btn-round',
            size: size,
            liveSearch: true,
            title: title,
        }).on('loaded.bs.select', (e) => {
            let searchBox = $(this.selector).parent().find('.bs-searchbox').find('input');
            searchBox.on('input', (e) => {
                thisClass.query(e.target.value);
            });
        });

        if (!_.isNil(initValue)) {
            console.log(initValue[idKey]);
            $(this.selector).selectpicker('val', initValue[idKey]);
            console.log($(this.selector).val());
        }
    }

    updateSelector() {
        $(this.selector).selectpicker('refresh');
    }

    render() {
        const {id, name} = this.props;

        let optionList = this.getOptionList();

        return (
            <select ref={(selector) => {
                this.selector = selector;
            }}
                    id={id}
                    name={name}
            >
                {optionList}
            </select>
        );
    }
}

ComboComponent.propTypes = {
    id: PropTypes.string,
    idKey: PropTypes.string.isRequired,
    modelClass: PropTypes.string.isRequired,
    name: PropTypes.string,
    title: PropTypes.string,
    size: PropTypes.number,
    showColumns: PropTypes.array.isRequired,
    defColumns: PropTypes.array.isRequired,
    initValue: PropTypes.string,
};

ComboComponent.defaultProps = {
    idKey: 'id',
    size: 9,
    initValue: '',
};

export default ComboComponent;