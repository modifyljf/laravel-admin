import React, {useEffect, useRef} from "react";
import {PlaceResult} from "./models/PlaceResult";

interface AutoCompleteInputTextProps {
    onFulfilled?: (place: PlaceResult) => void;
    onRejected?: (err: any) => void;
}

const AutoCompleteInputText = (props: AutoCompleteInputTextProps) => {
    const {onFulfilled} = props;

    const addressAutocomplete = useRef(null);
    useEffect(() => {
        // @ts-ignore
        if (google) {
            // @ts-ignore
            let autocomplete: google.maps.places.Autocomplete;
            // @ts-ignore
            autocomplete = new google.maps.places.Autocomplete(addressAutocomplete.current, {
                componentRestrictions: {country: ["us"]},
                fields: ["address_components", "formatted_address", "geometry", "icon", "name", "place_id"],
                types: ["address"],
            });

            autocomplete.addListener("place_changed", () => {
                const place: PlaceResult = autocomplete.getPlace();

                if (onFulfilled) {
                    onFulfilled(place);
                }
            });
        }
    }, []);

    return (
        <>
            <input type="text"
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
