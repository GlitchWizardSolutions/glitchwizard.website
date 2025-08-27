<?php
// Include the main file
include 'main.php';
// Ensure the cron secret reflects the one in the configuration file
if (isset($_GET['cron_secret']) && $_GET['cron_secret'] == cron_secret) {
    // Process recurring invoices
    // Get invoices where the next recurring date is 5 days from now
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE payment_status = "Paid" AND recurrence = 1 AND recurrence_period_type != ""');
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Check if there are any invoices
    foreach ($invoices as $invoice) {
        // Get the next recurring date
        $next_date = date('Y-m-d H:i:s', strtotime($invoice['due_date'] . ' +' . $invoice['recurrence_period'] . ' ' . $invoice['recurrence_period_type']));
        // Check if the next recurring date is 5 days from now
        if (date('Y-m-d', strtotime($next_date)) == date('Y-m-d', strtotime('+5 days'))) {
            // Update the invoice
            $stmt = $pdo->prepare('UPDATE invoices SET due_date = ?, viewed = 0, payment_status = "Unpaid" WHERE id = ?');
            $stmt->execute([ $next_date, $invoice['id'] ]);
            // Get client details
            $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
            $stmt->execute([ $invoice['client_id'] ]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            // Send email
            send_client_invoice_email($invoice, $client);
        }
    }
    // Send payment reminders to clients with overdue invoices
    $stmt = $pdo->prepare('SELECT * FROM invoices WHERE payment_status = "Unpaid" AND cast(due_date as DATE) < ?');
    $stmt->execute([ date('Y-m-d') ]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);    
    // Check if there are any invoices
    foreach ($invoices as $invoice) {
        // Get client details
        $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
        $stmt->execute([ $invoice['client_id'] ]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        // Send email
        send_client_invoice_email($invoice, $client, 'Payment Reminder');
    }
} else {
    exit('Invalid cron secret!');
}
?>