import {GeocoderAddressComponent} from "./GeocoderAddressComponent";
import {PlaceGeometry} from "./PlaceGeometry";

interface PlaceResult {
    address_components?: Array<GeocoderAddressComponent>;
    formatted_address?: string;
    geometry?: PlaceGeometry;
    icon?: string;
    name?: string;
    place_id?: string;
}

export {PlaceResult};
