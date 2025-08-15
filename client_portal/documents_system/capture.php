<?php
/**
 * File: capture.php
 * Description: Provides the signature drawing interface with a canvas for capturing a user's signature.
 * Functions:
 *   - JavaScript canvas interaction
 *   - Converts drawing to base64 and stores in localStorage
 * Expected Outputs:
 *   - Signature saved in localStorage as base64 string
 * Related Files:
 *   - pick-signature.php
 *   - submit-signature-handler.php
 *   - sign-documents.php
 */
require_once '../../private/gws-universal-config.php';
headerBlock();
?>

<head>
  <meta charset="UTF-8">
  <title>Draw Signature</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    #signature-pad {
      border: 2px dashed #ccc;
      width: 100%;
      height: 300px;
      touch-action: none;
      background-color: #f9f9f9;
    }
  </style>
</head>

<body class="container py-5">
  <h2 class="mb-4">Draw Your Signature</h2>
  <canvas id="signature-pad"></canvas>

  <div class="mt-4">
    <button class="btn btn-danger me-2" onclick="clearPad()">Clear</button>
    <button class="btn btn-success" onclick="saveSignature()">Save Signature</button>
  </div>

  <script>
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    canvas.addEventListener('mousedown', () => drawing = true);
    canvas.addEventListener('mouseup', () => drawing = false);
    canvas.addEventListener('mouseout', () => drawing = false);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('touchstart', e => {
      drawing = true;
      drawTouch(e);
    });
    canvas.addEventListener('touchmove', e => drawTouch(e));
    canvas.addEventListener('touchend', () => drawing = false);

    function draw(e) {
      if (!drawing) return;
      const rect = canvas.getBoundingClientRect();
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#000';
      ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
      ctx.stroke();
      ctx.beginPath();
      ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function drawTouch(e) {
      if (!drawing) return;
      const rect = canvas.getBoundingClientRect();
      const touch = e.touches[0];
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#000';
      ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
      ctx.stroke();
      ctx.beginPath();
      ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
      e.preventDefault();
    }

    function clearPad() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function saveSignature() {
      const dataURL = canvas.toDataURL();
      localStorage.setItem('drawnSignature', dataURL);
      alert('Signature saved. You can now return to the signing page.');
      window.location.href = 'sign-documents.php';
    }
  </script>

<?php footerBlock(); ?>