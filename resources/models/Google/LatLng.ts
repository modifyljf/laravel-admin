interface LatLng {
    lat: () => number;
    lng: () => number;
    toJson: () => string;
    toString: () => string;
}

export {LatLng};
