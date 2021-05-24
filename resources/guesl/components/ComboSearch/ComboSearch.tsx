import React, {useEffect, useRef, useState} from "react";
import {Column} from "../../types";
import * as _ from "lodash";
import Pagination from "../../classes/Pagination";
import * as Axios from "../Utilities/Axios";
import clsx from "clsx";

interface ComboSearchProps {
    baseUri?: string;
    remoteUri?: string;
    id?: string;
    idKey?: string;
    name?: string;
    title?: string;
    containerClass?: string;
    size?: number;
    pageSize?: number;
    initValue?: object;
    modelClass: string;
    multiple?: boolean;
    showColumns: Array<Column>;
    defColumns: Array<Column>;
    filter?: object;
    onChange?: (currentValue: any, previousValue: any) => void;
}

const ComboSearch = (props: ComboSearchProps) => {
    const {baseUri, remoteUri} = props;
    const {id, idKey, name, title, containerClass, initValue, size, multiple} = props;
    const {pageSize, modelClass, showColumns, defColumns, filter, onChange} = props;
    const [options, setOptions] = useState([]);
    const [selectedValue, setSelectedValue] = useState(null);
    const pickerRef = useRef(null);

    useEffect(() => {
        initSelector();
        fetchData();
    }, []);

    const initSelector = () => {
        let picker = pickerRef.current;
        if (!picker) {
            return null;
        }

        const size = getSize();
        // @ts-ignore
        $(picker).selectpicker({
            actionsBox: false,
            style: clsx("btn btn-primary btn-round", containerClass),
            size: size,
            liveSearch: true,
            title: title,
            width: "100%",
        }).on("loaded.bs.select", () => {
            // @ts-ignore
            let searchBox = $(picker).parent().find(".bs-searchbox").find("input");
            searchBox.on("input", _.debounce(
                (e: any) => {
                    e.preventDefault();
                    fetchData(e.target.value)
                }, 250
            ));

        }).on("changed.bs.select",
            // @ts-ignore
            function (e, clickedIndex, isSelected, previousValue) {
                // Save selectValue to state.
                // @ts-ignore
                setSelectedValue($(this).val());
                if (onChange) {
                    // @ts-ignore
                    onChange($(this).val(), previousValue);
                }
            });

        if (initValue && !_.isEmpty(initValue)) {
            // @ts-ignore
            $(picker).selectpicker("val", initValue[idKey]);
        }
    };

    const getSearchColumns = (): Array<String> => {
        let searchColumns: Array<String> = [];
        _.forEach(defColumns, (defColumn: Column, index) => {
            if (defColumn.searchable) {
                searchColumns.push(defColumn.field + "");
            }
        });

        return searchColumns;
    };

    const refreshPicker = () => {
        let picker = pickerRef.current;
        if (!picker) {
            return null;
        }

        if (initValue && !_.isEmpty(initValue)) {
            // @ts-ignore
            $(picker).selectpicker("val", initValue[idKey]);
        }

        // @ts-ignore
        $(picker).selectpicker("refresh");
    };

    const fetchData = (search?: string) => {
        let url = `combosearch`;
        if (!_.isEmpty(remoteUri)) {
            url = remoteUri + '';
        }

        const axios = Axios.createAxiosInstance(baseUri as string);
        let searchColumn = getSearchColumns();
        axios.get(url, {
            params: {
                model_class: modelClass,
                pagination: {
                    page: 1,
                    perpage: getPageSize()
                },
                query: {
                    generalSearch: _.isNil(search) ? "" : search,
                    ...filter
                },
                search_columns: searchColumn
            },
        }).then(response => {
            const pagination = new Pagination(response.data);
            const optionList = getOptionList(pagination);
            setOptions(optionList);
            refreshPicker();

        }).catch(error => {
            console.error(error);
        });
    };

    const getOptionText = (rowData: any): string => {
        if (_.isEmpty(rowData) && !multiple) {
            return "None";
        } else if (_.isEmpty(rowData) && multiple) {
            return "All";
        }

        let optionTextList = showColumns.map((column) => {
            return (column && column.field && rowData[column.field]) ? rowData[column.field] : "";
        });

        return _.join(optionTextList, " | ");
    };

    const getOptionValue = (rowData: any): string => {
        if (_.isEmpty(rowData)) {
            return "";
        } else {
            return rowData[idKey + ""]
        }
    };

    const getOptionList = (pagination: Pagination): any => {
        let optionValues = [];
        // init the null item.
        optionValues.push({});

        if (pagination && !_.isEmpty(pagination)) {
            optionValues = optionValues.concat(pagination.getCurrentPageData());
        }

        if (!_.isEmpty(initValue)) {
            let exists = _.find(optionValues, function (option) {
                if (idKey && initValue) {
                    // @ts-ignore
                    return option[idKey] == initValue[idKey];
                } else {
                    return false;
                }
            });

            if ("undefined" == exists) {
                optionValues.push(initValue as {});
            }
        }

        return optionValues.map((rowData: any, index) => {
            let optionText = getOptionText(rowData);
            let optionValue = getOptionValue(rowData);

            return (
                <option key={index} value={optionValue}>
                    {optionText}
                </option>
            );
        });
    };

    const getPageSize = () => {
        if (_.isNil(pageSize) || pageSize <= 0) {
            return 8;
        }
        return pageSize;
    };

    const getSize = () => {
        return _.isEmpty(size) ? 5 : size;
    };

    return (
        <select ref={pickerRef}
                id={id}
                name={name}
                multiple={multiple}
        >
            {options}
        </select>
    );
};

ComboSearch.defaultProps = {
    pageSize: 8,
    idKey: "id",
    multiple: true,
};

export default ComboSearch;
