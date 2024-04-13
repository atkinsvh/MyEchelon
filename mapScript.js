let map;
let geocoder;

// Assuming addressesWithDetails and countryCounts are defined globally, for example:
// const addressesWithDetails = [{ address: "1600 Amphitheatre Parkway, Mountain View, CA", title: "Google" }];
// const countryCounts = { "USA": 10 };

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: { lat: 37.7749, lng: -122.4194 },
    });

    geocoder = new google.maps.Geocoder();

    // Ensures geocoder and addressesWithDetails are defined before proceeding
    if (geocoder && addressesWithDetails && addressesWithDetails.length > 0) {
        addressesWithDetails.forEach(addr => {
            geocodeAddressAndAddMarker(addr);
        });
    }

    // Check if displayCountryCounts is defined and callable
    if (typeof displayCountryCounts === "function") {
        displayCountryCounts();
    }
}

function geocodeAddressAndAddMarker(addr) {
    // Ensure geocoder is defined before using it
    if (!geocoder) return;

    geocoder.geocode({ 'address': addr.address }, (results, status) => {
        if (status === 'OK') {
            map.setCenter(results[0].geometry.location);
            new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                title: addr.title,
            });
            addAddressToList(addr.title, addr.address);
        } else {
            console.error('Geocoding failed for address: ' + addr.address + ' with status: ' + status);
        }
    });
}

function addAddressToList(title, address) {
    const addressListEl = document.getElementById('addressList');
    let listItem = document.createElement('li');
    listItem.textContent = `${title}: ${address}`;
    addressListEl.appendChild(listItem);
}

function displayCountryCounts() {
    const countryListEl = document.getElementById('countryList');
    if (countryListEl && countryCounts) {
        Object.entries(countryCounts).forEach(([country, count]) => {
            let listItem = document.createElement('li');
            listItem.textContent = `${country}: ${count}`;
            countryListEl.appendChild(listItem);
        });
    }
}

// Dynamically load the Google Maps API script once the DOM is fully loaded
function loadGoogleMapsAPI() {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCcINCRlbwXBUNxCJsCJDq8we2ezfP5Dmg&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
