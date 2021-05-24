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
    };
    editable?: boolean;
    onChange?: (file: any) => void;
    onCancel?: () => void;
}

export default (props: ImageInputProps) => {
    const {
        fileName,
        assetUri,
        placeholder = {url: "", width: 288, height: 192},
        origin = {url: "", width: 288, height: 192},
        editable = true,
        onChange,
        onCancel
    } = props;

    const imageInput = useRef(null);
    useEffect(() => {
        if (!_.isNil(imageInput)) {
            // @ts-ignore
            const id = imageInput.current.id;
            // @ts-ignore
            let kt = new KTImageInput(id);
            let placeHolderImage = placeholder;

            //show place holder image when file is canceled or removed
            kt.on('cancel', function (imageInput: any) {
                imageInput.src = `url(${placeHolderImage})`;
            });

            //change hidden input to "1" to remove image
            if (kt.cancel) {
                kt.cancel.addEventListener('click', function () {
                    kt.hidden.value = "1";

                    if (onCancel) {
                        onCancel();
                    }
                }, false);
            }
        }
    }, []);

    return (
        <div id={_.uniqueId(fileName + "_")}
             ref={imageInput}
             className={!_.isEmpty(origin.url) ? "image-input image-input-outline image-input-changed" : "image-input image-input-outline"}
        >
            <div className="image-input-wrapper"
                 style={{
                     width: origin.width ? origin.width : 288,
                     height: origin.height ? origin.height : 192,
                     backgroundImage: !_.isEmpty(origin.url) ? `url(${assetUri + origin.url})` : `url(${assetUri + placeholder.url})`
                 }}
            />
            {
                editable ?
                    <label className="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                           data-action="change" data-toggle="tooltip" title="Change"
                           data-original-title="Change"
                    >
                        <i className="fa fa-pen icon-sm text-muted"/>
                        <input type="file"
                               name={fileName}
                               accept=".png, .jpg, .jpeg"
                               onChange={(e) => {
                                   if (onChange) {
                                       // @ts-ignore
                                       onChange(e.target.files[0]);
                                   }
                               }}
                        />
                        <input type="hidden" name={fileName + "_remove"} value={0}/>
                    </label>
                    : null
            }
            <span className="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                  data-action="cancel" data-toggle="tooltip" title="Cancel"
            >
                <i className="ki ki-bold-close icon-xs text-muted"/>
            </span>
        </div>
    );
};
