import {Address} from "../request/Address";

const gecodeAddress = (
    address: Address,
    fulfill: ({latitude, longitude}: { latitude: number; longitude: number }) => void,
    rejected?: (status: string) => void,
) => {
    // @ts-ignore
    const geocoder = new google.maps.Geocoder();
    let position;

    const addressInput = `${address.address_line1}, ${address.address_city}, ${address.address_state}, ${address.address_zip}`;

    geocoder.geocode({address: addressInput}, (results: any, status: string) => {
        if (status === "OK") {
            position = results[0].geometry.location;
            const latitude = position.lat();
            const longitude = position.lng();

            if (fulfill) {
                fulfill({latitude, longitude});
            }

        } else {
            if (rejected) {
                rejected(status);
            }
        }
    });

    return position;
};

export {gecodeAddress};
