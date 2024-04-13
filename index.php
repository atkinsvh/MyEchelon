<!DOCTYPE html>
<html>
<head>
  <title>Import Addresses</title>
  <link rel="stylesheet" type="text/css" href="Door2Door.css">
</head>
<body>
    <div class="navbar">
      <a href="index.php">Home</a>
      <a href="processAddresses.php">HeatMap</a>
    </div>

    <div class="container">
        <h1>Import Addresses</h1>
        <!-- Form for CSV file upload -->
        <form method="post" action="processAddresses.php" enctype="multipart/form-data">
            <input type="file" name="addressFile" required>
            <!-- Inputs for specifying CSV column headers -->
            <!-- (The rest of the inputs for CSV headers go here) -->
            <button type="submit" name="submit-csv">Upload CSV</button>
        </form>
        
        <hr>
        
        <!-- Separate form for manual address entry -->
        <h2>Or add an address manually:</h2>
        <form method="post" action="processAddresses.php">
            <input type="text" name="manualAddress" placeholder="Full Address" required>
            <input type="text" name="companyName" placeholder="Company Name (for notes)" required>
            <button type="submit" name="submit-manual">Add Address</button>
        </form>
    </div>
</body>
</html>
