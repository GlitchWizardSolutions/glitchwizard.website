<?php
/* 
 * File: submit-signature-handler.php
 * Description: Handles applying a client's drawn signature onto a document.
 * Requirements: FPDI (via mPDF), Bootstrap layout, valid session.
 */

require_once '../../private/gws-universal-config.php';
headerBlock("Submit Signature");

function friendlyError($message, $backLink = 'sign-document.php') {
    echo "<div class='container mt-5 alert alert-warning'>";
    echo "<h4 class='mb-3'>Oops, something went wrong.</h4>";
    echo "<p>" . htmlspecialchars($message) . "</p>";
    echo "<a href='{$backLink}' class='btn btn-outline-secondary mt-3'>Go Back</a>";
    echo "</div>";
    footerBlock();
    exit;
}

// 1. Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    friendlyError("This page only accepts form submissions.");
}

// 2. Extract and validate POST values
$clientId        = $_POST['client_id']     ?? null;
$documentPathRaw = $_POST['document']      ?? null;
$signatureBase64 = $_POST['signature']     ?? '';

if (!$clientId || !$documentPathRaw || !$signatureBase64) {
    friendlyError("Missing required information. Please try again.");
}

$documentPath = "../../public_html/pdf-system/" . $documentPathRaw;

if (!file_exists($documentPath)) {
    friendlyError("The selected document could not be found.");
}

// 3. Parse and decode base64 signature
if (!str_starts_with($signatureBase64, 'data:image/png;base64,')) {
    friendlyError("Invalid signature format received.");
}

$signatureData = explode(',', $signatureBase64, 2);
$decodedImage  = base64_decode($signatureData[1] ?? '');

if (!$decodedImage) {
    friendlyError("Unable to decode the signature image.");
}

// 4. Save signature image to temp file
$tempImagePath = tempnam(sys_get_temp_dir(), 'sig_') . '.png';
file_put_contents($tempImagePath, $decodedImage);

// 5. Load PDF and embed signature
try {
    // Make sure we're using the FPDF implementation
    require_once(vendor_path . '/setasign/fpdf/fpdf.php');
    require_once(vendor_path . '/setasign/fpdi/src/autoload.php');

    // Create PDF instance using FPDF implementation
    $pdf = new \setasign\Fpdi\Fpdi();
    
    // Set document properties
    $pdf->SetAuthor('GWS Universal Hybrid App');
    $pdf->SetTitle('Signed Document');
    
    // Get the number of pages and import the first page
    $pageCount = $pdf->setSourceFile($documentPath);
    $tplIdx = $pdf->importPage(1);
    
    // Get the size of the imported page
    $size = $pdf->getTemplateSize($tplIdx);
    
    // Add a page (automatically uses the correct size)
    $pdf->addPage();
    
    // Use the imported page
    $pdf->useTemplate($tplIdx);
    
    // Convert PNG to JPG (since FPDI/FPDF has better JPG support)
    $jpgPath = tempnam(sys_get_temp_dir(), 'sig_') . '.jpg';
    $image = imagecreatefrompng($tempImagePath);
    imagejpeg($image, $jpgPath, 95);
    imagedestroy($image);
    
    // Calculate signature position (bottom right)
    $marginX = $size['width'] - 60;
    $marginY = $size['height'] - 40;
    
    // Add the signature image
    $pdf->addJpegFromFile($jpgPath, $marginX, $marginY, 50, 20);
    
    // Save the signed PDF
    $signedName = 'signed-' . basename($documentPath);
    $signedPath = dirname($documentPath) . '/' . $signedName;
    $pdf->Output('F', $signedPath);
    
    // Clean up temporary files
    if (file_exists($jpgPath)) unlink($jpgPath);

    // Cleanup temp image
    if (file_exists($tempImagePath)) unlink($tempImagePath);

    echo "<div class='container mt-5'>";
    echo "<h2 class='text-success mb-3'>Signature Saved Successfully</h2>";
    echo "<p>Your signed document is ready:</p>";
    echo "<p><code>" . htmlspecialchars($signedName) . "</code></p>";
    echo "<a href='view-signed-documents.php' class='btn btn-primary mt-3'>View Signed Documents</a>";
    echo "</div>";

} catch (Exception $e) {
    // Cleanup temp image on failure
    if (file_exists($tempImagePath)) unlink($tempImagePath);

    error_log("Signature processing error: " . $e->getMessage());
    friendlyError("We couldn't apply your signature. Please try again, or contact support if the issue persists.");
}

footerBlock();
?>
