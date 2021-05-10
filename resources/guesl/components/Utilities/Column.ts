import * as _ from "lodash";
import {Column} from "../../types";

export const getColumns = (defColumns: Array<Column>) => {
    let cols = [] as Array<string>;

    _.forEach(defColumns, (defColumn, index) => {
        cols.push(defColumn.field + "");
    });

    return cols;
};

export const getSearchColumns = (defColumns: Array<Column>): Array<string> => {
    let searchColumns: Array<string> = [];

    _.forEach(defColumns, (defColumn: Column) => {
        if (defColumn.searchable && !_.isNil(defColumn.field)) {
            searchColumns.push(defColumn.field);
        }
    });

    return searchColumns;
};
