<?php
// Initialize arrays for data
$addressesWithDetails = [];
$countryCounts = [];

// Process the uploaded CSV
if (isset($_FILES['addressFile'])) {
    $filename = $_FILES['addressFile']['tmp_name'];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        fgetcsv($handle); // Assuming the first row contains headers
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            // Assuming columns: Address Street (4), Address Country (8), Company Name (9)
            $address = trim($data[4]);
            if (!empty($address)) {
                $addressesWithDetails[] = [
                    'address' => $address,
                    'country' => $data[8],
                    'title' => $data[9]
                ];
            } else {
                $countryCounts[$data[8]] = ($countryCounts[$data[8]] ?? 0) + 1;
            }
        }
        fclose($handle);
    }
}
?>

//<?php
// Dumping the contents of $addresses
//var_dump($addressesWithDetails);



<!DOCTYPE html>
<html>
<head>
    <title>CSV Map Visualization</title>
	<link rel="stylesheet" type="text/css" href="Door2Door.css">
//	<script>var addressData= <?php echo json_encode($addressData); 
</head>
<body>
    <h1>Total Countries: <span id="totalCountries">5</span></h1>
    <div class="flex-container">
        <div id="map"></div>
        <div id="countryList"></div>
    </div>
    <li id="addressList"></li>

    <script>
        const addressesWithDetails = [];
        const countryCounts = [];
		print(addressWithDetails);
        print(countryCounts);
    </script>
    
    <script src="/var/www/mapScript.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcINCRlbwXBUNxCJsCJDq8we2ezfP5Dmg&callback=initMap&libraries=visualization"></script>
    
</body>
</html>

