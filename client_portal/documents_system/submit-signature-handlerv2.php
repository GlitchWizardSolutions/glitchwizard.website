<?php
require_once '../../private/gws-universal-config.php';

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document'], $_POST['signature'])) {
    $documentPath = $_POST['document'];
    $clientId = $_POST['client_id'] ?? null;
    $signatureData = $_POST['signature'];

    if (!file_exists($documentPath)) {
        http_response_code(404);
        echo 'Document not found.';
        exit;
    }

    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
    $signatureFile = tempnam(sys_get_temp_dir(), 'sig_') . '.png';
    file_put_contents($signatureFile, $imageData);

    $pdf = new FPDI();
    $pageCount = $pdf->setSourceFile($documentPath);
    $newFile = str_replace('.pdf', '_signed.pdf', $documentPath);

    for ($i = 1; $i <= $pageCount; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);

        if ($i === $pageCount) {
            $pdf->Image($signatureFile, 20, 250, 50); // Adjust position/size as needed
        }
    }

    $pdf->Output('F', $newFile);
    unlink($signatureFile);

    echo '<div style="padding:2rem;font-family:sans-serif;">'
        . '<h2>Document signed successfully!</h2>'
        . '<p><a href="' . htmlspecialchars($newFile) . '" target="_blank">View Signed Document</a></p>'
        . '<p><a href="sign-document.php">Sign Another</a></p>'
        . '</div>';
} else {
    http_response_code(400);
    echo 'Invalid request.';
}
