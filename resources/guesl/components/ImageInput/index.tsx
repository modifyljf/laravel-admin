import * as React from "react";
import {useEffect, useRef} from "react";
import * as _ from "lodash";

interface ImageInputProps {
    fileName: string;
    assetUri: string;
    placeholder?: {
        url?: string;
        width?: number;
        height?: number;
    };
    origin?: {
        url?: string;
        value?: string;
        height?: number;
        width?: number;
    }
}

export default (props: ImageInputProps) => {
    const {
        fileName,
        assetUri,
        placeholder = {url: "", width: 200, height: 150},
        origin = {url: "", width: 200, height: 150}
    } = props;

    const imageInput = useRef(null);

    useEffect(() => {
        if (!_.isNil(imageInput)) {
            // @ts-ignore
            const id = imageInput.current.id;
            // @ts-ignore
            new KTImageInput(id);
        }
    }, []);

    return (
        <div id={_.uniqueId(fileName + "_")}
             ref={imageInput}
             className="image-input image-input-outline kt-image"
             style={_.isEmpty(origin.url) ? {
                 width: placeholder.width ? placeholder.width : 200,
                 height: placeholder.height ? placeholder.height : 150,
                 backgroundImage: `url(${assetUri + placeholder.url})`
             } : {}}
        >
            <div className="image-input-wrapper"
                 style={!_.isEmpty(origin.url) ? {
                     width: origin.width ? origin.width : 200,
                     height: origin.height ? origin.height : 150,
                     backgroundImage: `url(${assetUri + origin.url})`
                 } : {
                     width: origin.width ? origin.width : 200,
                     height: origin.height ? origin.height : 150,
                 }}
            />

            <label className="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                   data-action="change" data-toggle="tooltip" title="Change"
                   data-original-title="Change"
            >
                <i className="fa fa-pen icon-sm text-muted"/>
                <input type="file"
                       name={fileName}
                       accept=".png, .jpg, .jpeg"
                />
                <input type="hidden" name={fileName + "_remove"} value={0}/>
            </label>

            <span className="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                  data-action="cancel" data-toggle="tooltip" title="Cancel"
            >
                <i className="ki ki-bold-close icon-xs text-muted"/>
            </span>

            <span className="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                  data-action="remove" data-toggle="tooltip" title="Remove"
            >
                <i className="ki ki-bold-close icon-xs text-muted"/>
            </span>
        </div>
    );
};
