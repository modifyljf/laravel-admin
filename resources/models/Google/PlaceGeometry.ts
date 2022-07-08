import {LatLng} from "./LatLng";
import {LatLngBounds} from "./LatLngBounds";

interface PlaceGeometry {
    location?: LatLng;
    viewport?: LatLngBounds;
}

export {PlaceGeometry};
