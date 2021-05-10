import {GeocoderAddressComponent} from "./GeocoderAddressComponent";

interface PlaceResult {
    address_components?: Array<GeocoderAddressComponent>;
    formatted_address?: string;
    geometry?: string;
    icon?: string;
    name?: string;
    place_id?: string;
}

export {PlaceResult};
