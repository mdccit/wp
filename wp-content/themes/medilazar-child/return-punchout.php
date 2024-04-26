<?php
require_once('../../../wp-load.php'); 


// Assuming user is redirected here with a valid session
$punchout_data = get_punchout_return_url();
if (isset($punchout_data['error'])) {
    // Handle error, e.g., redirect or display an error message
    echo "Error: " . $punchout_data['error'];
    exit;
}
// If data is successfully retrieved
$encoded_cxml_data = urlencode($punchout_data['cxmlData']); // Safely encode for HTML display

// $order_url = 'https://commercialmedica.requestcatcher.com/test';
$order_url = esc_url($punchout_data['orderUrl']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="ISO-8859-1">
    <title>Punchout Return</title>
</head>
<body onload="document.forms['orderForm'].submit()">
    <form name="orderForm" action="<?php echo $order_url; ?>" method="post">
        <input type="hidden" name="oracleCart" value="">
    </form>
</body>
</html>
