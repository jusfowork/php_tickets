<?php
// To store generated barcodes and ensure uniqueness
$generated_barcodes = [];
function generateBarcode() {
    global $generated_barcodes; // Use the global array to store generated barcodes
    do {
        // Generate a new random 8-digit barcode
        $barcode = strval(rand(10000000, 99999999));
    } while (in_array($barcode, $generated_barcodes)); // Check if it's already generated
    
    // Store the generated barcode to prevent future duplicates
    $generated_barcodes[] = $barcode;
    
    return $barcode;
}
?>
