import React, {useEffect, useRef} from "react";
import {PlaceResult} from "../../models/Google/PlaceResult";

interface AutoCompleteInputTextProps {
    name?: string;
    onFulfilled?: (place: PlaceResult) => void;
    onRejected?: (err: any) => void;
}

const AutoCompleteInputText = (props: AutoCompleteInputTextProps) => {
    const {name, onFulfilled} = props;

    const addressAutocomplete = useRef(null);
    useEffect(() => {
        // @ts-ignore
        if (google) {
            // @ts-ignore
            let autocomplete: google.maps.places.Autocomplete;
            const inputField: any = addressAutocomplete.current;
            // @ts-ignore
            autocomplete = new google.maps.places.Autocomplete(
                inputField,
                {
                    componentRestrictions: {country: ["us"]},
                    fields: ["address_components", "formatted_address", "geometry", "icon", "name", "place_id"],
                    types: ["address"],
                }
            );

            autocomplete.addListener("place_changed", () => {
                const place: PlaceResult = autocomplete.getPlace() as PlaceResult;

                if (onFulfilled) {
                    onFulfilled(place);
                }
            });
        }
    }, []);

    return (
        <>
            <input name={name}
                   type="text"
                   className="form-control"
                   placeholder="Address Line 1, Address Line 2, City, State, Zip Code..."
                   aria-label="autoCompleteLabel"
                   autoComplete="off"
                   ref={addressAutocomplete}
            />
        </>
    );
}

export default AutoCompleteInputText;
