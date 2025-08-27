<?php
// Include the config file
include 'config.php';
// Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Connect to the database using integrated configuration
try {
    $pdo = new PDO('mysql:host=' . invoice_db_host . ';dbname=' . invoice_db_name . ';charset=' . invoice_db_charset, invoice_db_user, invoice_db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}
// Create invoice PDF function
function create_invoice_pdf($invoice, $invoice_items, $client) {
    define('INVOICE', true);
    // Client address
    $client_address = [
        $client['address_street'],
        $client['address_city'],
        $client['address_state'],
        $client['address_zip'],
        $client['address_country']
    ];
    // remove any empty values
    $client_address = array_filter($client_address);
    // Get payment methods
    $payment_methods = explode(', ', $invoice['payment_methods']);
    // Include the template
    if (file_exists(base_path . 'templates/' . $invoice['invoice_template'] . '/template-pdf.php')) {
        require base_path . 'templates/' . $invoice['invoice_template'] . '/template-pdf.php';
        // Save the output to a file
        $pdf->Output(base_path . 'pdfs/' . $invoice['invoice_number'] . '.pdf', 'F');
        return true;
    } else if (file_exists(base_path . 'templates/default/template-pdf.php')) {
        require base_path . 'templates/default/template-pdf.php';
        // Save the output to a file
        $pdf->Output(base_path . 'pdfs/' . $invoice['invoice_number'] . '.pdf', 'F');
        return true;
    } 
    return false;
}
// Send notification email function
function send_client_invoice_email($invoice, $client, $subject = '') {
	if (!mail_enabled) return;
	// Include PHPMailer library
	require_once base_path . 'lib/phpmailer/Exception.php';
	require_once base_path . 'lib/phpmailer/PHPMailer.php';
	require_once base_path . 'lib/phpmailer/SMTP.php';
	// Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);
	try {
		// Server settings
		if (SMTP) {
			$mail->isSMTP();
			$mail->Host = smtp_host;
			$mail->SMTPAuth = empty(smtp_user) && empty(smtp_pass) ? false : true;
			$mail->Username = smtp_user;
			$mail->Password = smtp_pass;
			$mail->SMTPSecure = smtp_secure == 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = smtp_port;
		}
		// Recipients
		$mail->setFrom(mail_from, mail_name);
		$mail->addAddress($client['email'], rtrim($client['first_name'] . ' ' . $client['last_name'], ' '));
		$mail->addReplyTo(mail_from, mail_name);
		// Content
		$mail->isHTML(true);
        // Set UTF-8 charset
        $mail->CharSet = 'UTF-8';
        // Set email subject
		$mail->Subject = empty($subject) ? 'Invoice #' . $invoice['invoice_number'] . ' from ' . company_name : $subject;
		// Read the template contents and replace the placeholders with the variables
		$email_template = str_replace(
            ['%invoice_number%', '%first_name%', '%amount%', '%due_date%', '%link%'],
            [$invoice['invoice_number'], $client['first_name'], number_format($invoice['payment_amount']+$invoice['tax_total'], 2), $invoice['due_date'], base_url . 'invoice.php?id=' . $invoice['invoice_number']],
            file_get_contents(base_path . 'templates/client-email-template.html')
        );
        // Check if pdf atatchment is enabled
        if (pdf_attachments && file_exists(base_path . 'pdfs/' . $invoice['invoice_number'] . '.pdf') && !$subject) {
            // Include the PHPMailer class
            $mail->AddAttachment(base_path . 'pdfs/' . $invoice['invoice_number'] . '.pdf', $invoice['invoice_number'] . '.pdf');
        }
		// Set email body
		$mail->Body = $email_template;
		$mail->AltBody = strip_tags($email_template);
		// Send mail
		$mail->send();
	} catch (Exception $e) {
		// Output error message
		exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
	}
}
// Send notification email function
function send_admin_invoice_email($invoice, $client) {
    if (!notifications_enabled || !mail_enabled) return;
    // Include PHPMailer library
    require_once base_path . 'lib/phpmailer/Exception.php';
    require_once base_path . 'lib/phpmailer/PHPMailer.php';
    require_once base_path . 'lib/phpmailer/SMTP.php';
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        // Server settings
        if (SMTP) {
            $mail->isSMTP();
            $mail->Host = smtp_host;
            $mail->SMTPAuth = empty(smtp_user) && empty(smtp_pass) ? false : true;
            $mail->Username = smtp_user;
            $mail->Password = smtp_pass;
            $mail->SMTPSecure = smtp_secure == 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = smtp_port;
        }
        // Recipients
        $mail->setFrom(mail_from, mail_name);
        $mail->addAddress(notification_email);
        $mail->addReplyTo(mail_from, mail_name);
        // Content
        $mail->isHTML(true);
        // Set UTF-8 charset
        $mail->CharSet = 'UTF-8';
        // Set email subject
        if ($invoice['payment_status'] == 'Paid') {
            $mail->Subject = 'Invoice #' . $invoice['invoice_number'] . ' has been paid.';
        } else if ($invoice['payment_status'] == 'Cancelled') {
            $mail->Subject = 'Invoice #' . $invoice['invoice_number'] . ' has been cancelled.';
        } else if ($invoice['payment_status'] == 'Pending') {
            $mail->Subject = 'Invoice #' . $invoice['invoice_number'] . ' is pending payment.';
        } else {
            $mail->Subject = 'Invoice #' . $invoice['invoice_number'] . ' has been updated.';
        }
        // Read the template contents and replace the placeholders with the variables
        $email_template = str_replace(
            ['%invoice_number%', '%client%', '%amount%', '%status%', '%date%'],
            [$invoice['invoice_number'], $client['first_name'] . ' ' . $client['last_name'], number_format($invoice['payment_amount']+$invoice['tax_total'], 2), $invoice['payment_status'], date('Y-m-d H:i:s')],
            file_get_contents(base_path . 'templates/notification-email-template.html')
        );
 		// Set email body
        $mail->Body = $email_template;
        $mail->AltBody = strip_tags($email_template);
        // Send mail
        $mail->send();
    } catch (Exception $e) {
        // Output error message
        exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
}   
?>