<?php
// Initialize arrays for data
$addressesWithDetails = [
    // Mock data can go here if needed for fallback or default visualization
    ['address' => '1600 Amphitheatre Parkway, Mountain View, CA', 'country' => 'USA', 'title' => 'Google']
];
$countryCounts = ["USA" => 10]; // Default or fallback country counts

// Check if a CSV file is uploaded and process it
if (isset($_FILES['addressFile']) && is_uploaded_file($_FILES['addressFile']['tmp_name'])) {
    $filename = $_FILES['addressFile']['tmp_name'];
    $addressesWithDetails = []; // Reset to empty if processing new uploaded data
    $countryCounts = []; // Reset country counts

    if (($handle = fopen($filename, "r")) !== FALSE) {
        fgetcsv($handle); // Skip the header row

        while (($data = fgetcsv($handle)) !== FALSE) {
            $address = trim($data[4]);
            $country = trim($data[8]);
            $title = trim($data[9]);

            if (!empty($address)) {
                $addressesWithDetails[] = ['address' => $address, 'country' => $country, 'title' => $title];
            } else {
                $countryCounts[$country] = isset($countryCounts[$country]) ? $countryCounts[$country] + 1 : 1;
            }
        }
        fclose($handle);
    }
}

// Prepare data for JavaScript
$jsonAddresses = json_encode($addressesWithDetails);
$jsonCountryCounts = json_encode($countryCounts);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Map Visualization</title>
    <link rel="stylesheet" type="text/css" href="Door2Door.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
      <a href="index.php">Home</a>
      <a href="processedAddresses.php" class="active">HeatMap</a>
      <!-- Add other navigation links as needed -->
    </div>
    <div id="map" style="height: 400px; width: 100%;"></div>
    
    <!-- Address List Container -->
    <div class="list-container">
        <h2>Address List</h2>
        <ul id="addressList" class="address-list"></ul>
    </div>
    
    <!-- Country Counts Container -->
    <div class="list-container">
        <h2>Country Counts</h2>
        <ul id="countryList" class="country-list"></ul>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const addressesWithDetails = <?php echo $jsonAddresses; ?>;
        const countryCounts = <?php echo $jsonCountryCounts; ?>;
        let map;
        let geocoder;

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
    </script>
</body>
</html>

<script>
const addressesWithDetails = <?php echo $jsonAddresses; ?>;
const countryCounts = <?php echo $jsonCountryCounts; ?>;
let map, heatmap, geocoder;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: { lat: 37.7749, lng: -122.4194 },
    });

    geocoder = new google.maps.Geocoder();

    let heatmapData = [];

    addressesWithDetails.forEach(addr => {
        // If address includes .lat and .lng properties, use them for the heatmap
        // Otherwise, attempt to geocode the address
        if (addr.lat && addr.lng) {
            let latLng = new google.maps.LatLng(addr.lat, addr.lng);
            heatmapData.push(latLng);
            addAddressToList(addr.title, addr.address); // Add directly listed addresses to the list
        } else if (addr.address) {
            geocodeAddressAndAddMarker(addr);
        }
    });

    // Initialize the heatmap after processing all addresses
    heatmap = new google.maps.visualization.HeatmapLayer({
        data: heatmapData,
        map: map,
    });

    displayCountryCounts();
}

function geocodeAddressAndAddMarker(addr) {
    geocoder.geocode({ 'address': addr.address }, (results, status) => {
        if (status === 'OK') {
            let location = results[0].geometry.location;
            new google.maps.Marker({
                map: map,
                position: location,
                title: addr.title,
            });
            addAddressToList(addr.title, addr.address); // Add geocoded addresses to the list
            heatmap.getData().push(location); // Optionally, add geocoded points to the heatmap
        } else {
            console.error(`Geocoding failed for ${addr.address} with status: ${status}`);
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
    Object.entries(countryCounts).forEach(([country, count]) => {
        let listItem = document.createElement('li');
        listItem.textContent = `${country}: ${count}`;
        countryListEl.appendChild(listItem);
    });
}

function loadGoogleMapsAPI() {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
</script>

