<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'); // Load WordPress environment

// Assuming user is redirected here with a valid session
$punchout_data = create_complete_punchout_order_cxml();

if (isset($punchout_data['error'])) {
    // Handle error, e.g., redirect or display an error message
    echo "Error: " . $punchout_data['error'];
    exit;
}

// If data is successfully retrieved
$cxml_data = htmlspecialchars($punchout_data['cxmlData']); // Safely encode for HTML display
$order_url = esc_url($punchout_data['orderUrl']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Punchout Order</title>
</head>
<body onload="document.forms['punchoutForm'].submit()">
    <form name="punchoutForm" action="<?php echo $order_url; ?>" method="post">
        <input type="hidden" name="oracleCart" value="<?php echo $cxml_data; ?>">
    </form>
</body>
</html>
