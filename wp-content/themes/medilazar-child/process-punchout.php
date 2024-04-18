<?php
require_once('../../../wp-load.php'); 


// Assuming user is redirected here with a valid session
$punchout_data = get_complete_punchout_order_cxml();

if (isset($punchout_data['error'])) {
    // Handle error, e.g., redirect or display an error message
    echo "Error: " . $punchout_data['error'];
    exit;
}

// If data is successfully retrieved
$cxml_data = $punchout_data['cxmlData'];
$encoded_cxml_data = urlencode($punchout_data['cxmlData']); // Safely encode for HTML display

// $order_url = 'https://commercialmedica.requestcatcher.com/test';
$order_url = esc_url($punchout_data['orderUrl']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1">
    <title>Punchout Order</title>
</head>
<body onload="document.forms['orderForm'].submit()">
    <form name="orderForm" action="<?php echo $order_url; ?>" method="post">
        <input type="hidden" name="oracleCart" value="<?php echo htmlspecialchars($cxml_data); ?>">
    </form>
</body>
</html>
