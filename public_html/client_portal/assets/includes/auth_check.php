<?php
// assets/includes/auth_check.php
if (empty($_SESSION['loggedin'])) {
    header('Location: ../auth.php?tab=login');
    exit;
}
?>
