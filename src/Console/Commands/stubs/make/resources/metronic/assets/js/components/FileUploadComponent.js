/**
 * Data table component.
 */

import React from 'react';
import ReactDOMServer from 'react-dom/server';
import PropTypes from 'prop-types';
import * as App from '../config/app';
import _ from 'lodash';
import axios from '../helpers/axios';
import DataTableComponent from "./DataTableComponent";

class FileUploadComponent extends React.PureComponent {
    constructor(props) {
        super(props);

        this.state = {
            error: '',
            file: null,
        };
    }

    showRemoveBtn() {
        this.setState({});
    }

    validateFileType(file) {
        const {accept, imageOnly} = this.props;

        let defaultFileTypes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png'
        ];

        let fileTypes = _.isEmpty(accept) ? defaultFileTypes : accept;

        if (imageOnly) {
            fileTypes = defaultFileTypes;
        }

        for (let i = 0; i < fileTypes.length; i++) {
            if (file.type === fileTypes[i]) {
                return true;
            }
        }

        return false;
    }

    validateFileSize(file) {
        const {size} = this.props;
        let limitedSize = size * 1000;

        let fileSize = file.size;

        return fileSize <= limitedSize;
    }

    handleFileChange(files) {
        let file = files[0];
        if (!_.isNil(file)) {
            let isTypeMatch = this.validateFileType(file);
            if (!isTypeMatch) {
                this.setState({
                    error: 'File format is wrong',
                    file: null,
                });

                return false;
            }


            let isFileSizeLimited = this.validateFileSize(file);
            if (!isFileSizeLimited) {
                this.setState({
                    error: 'File size exceed limit',
                    file: null,
                });

                return false;
            }


            // Set up the image preview.
            let reader = new FileReader();
            reader.onload = (e) => {
                this.preview.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(file);

            // Show remove button and hide upload label.
            this.setState({
                file: file,
            });

        }
    }

    handleFilesRemove() {
        this.fileInput.value = null;
        this.setState({
            file: null
        });
    }

    render() {
        const {id, name, title, placeHolder} = this.props;
        const {file, error} = this.state;

        return (
            <div>
                {
                    _.isNil(file) ?
                        null :
                        <img className="mb-3 shadow form__input-file--preview img-fluid"
                             alt={`${id}Preview`}
                             ref={(preview) => {
                                 this.preview = preview;
                             }}
                        />
                }

                <input className="form-control m-input form__input-file"
                       id={id}
                       name={name}
                       aria-describedby={`${id}Help`}
                       placeholder={placeHolder}
                       autoComplete="off"
                       type="file"
                       ref={(fileInput) => {
                           this.fileInput = fileInput;
                       }}
                       onChange={(event) => {
                           this.handleFileChange(event.target.files);
                       }}
                />
                {
                    _.isNil(file) ?
                        <label htmlFor={id}
                               className="w-100 text-center col-form-label shadow form__input__icon--positive text-info"
                        >
                            {title}
                        </label> :
                        null
                }

                {
                    _.isNil(file) ?
                        null :
                        <button type="button"
                                className="btn btn-primary m-btn--pill m-btn--air"
                                onClick={() => {
                                    this.handleFilesRemove()
                                }}
                        >
                            <i className="flaticon-close"></i>&nbsp;&nbsp;
                            Remove
                        </button>
                }
                {
                    _.isEmpty(error) ? null :
                        <span className="form-control-feedback text-danger">
                            &nbsp; {error}
                        </span>
                }
            </div>
        );
    }
}

FileUploadComponent.propTypes = {
    id: PropTypes.string,
    name: PropTypes.string,
    title: PropTypes.string,
    placeHolder: PropTypes.string,
    accept: PropTypes.array,
    imageOnly: PropTypes.bool,
    size: PropTypes.number,
    multiple: PropTypes.bool,
};

FileUploadComponent.defaultProps = {
    id: 'uploadedImage',
    name: 'file',
    placeHolder: '',
    accept: [],
    imageOnly: true,
    size: 3000, // in KB
    multiple: false
    title: 'Upload Image',
};

export default FileUploadComponent;
