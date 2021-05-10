import React from "react";
import {DatatableInterface, ActionButton} from "../../types";
import clsx from "clsx";
import * as _ from "lodash";

interface ToolbarProps {
    datatable: DatatableInterface,
    actions: Array<ActionButton>;
}

const Toolbar = (props: ToolbarProps) => {
    const {actions, datatable} = props;

    if (_.isEmpty(actions)) {
        return null;
    }

    let toolbarActions = actions.map((action, index) => {
        return (
            <a key={index}
               className="btn btn-md btn-icon btn-clean btn-icon-md"
               data-toggle="kt-tooltip"
               data-placement="top"
               title={action.tooltipTitle}
               onClick={() => {
                   if (!_.isNil(action.handleClick)) {
                       action.handleClick(datatable);
                   }
               }}
            >
                <i className={action.actionClass}/>
            </a>
        );
    });

    return (
        <div className={clsx([
            "d-flex justify-content-between align-items-center",
            "px-4 py-2",
            "border-bottom"
        ])}>
            <h5 className="d-flex justify-content-center align-items-center">
                <i className="kt-font-brand fas fa-tools pr-2"/>
                Toolbars
            </h5>

            <div className="d-flex justify-content-center align-items-center">
                {toolbarActions}
            </div>
        </div>
    );
};

Toolbar.defaultProps = {
    actions: []
};

export default Toolbar;
