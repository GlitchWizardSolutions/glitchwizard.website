<?php
// Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Note: Database connection ($pdo) is now provided by shop_load.php via main.php
// No need for separate pdo_connect_mysql() function

// Shop System Functions - Integrated Version
// Core shop functionality without conflicting template functions

// Helper function to get cart count for shop pages
function get_cart_count() {
    return isset($_SESSION['cart']) && $_SESSION['cart'] ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
}

// Helper function to get shop-specific JavaScript
function get_shop_javascript() {
    return '<script>
        const currency_code = "' . currency_code . '";
        const base_url = "' . base_url . '";
    </script>';
}

// Shop-specific template functions removed to avoid conflicts with main admin template
// The main admin template system will be used instead

// Function to retrieve a product from cart by the ID and options string
function &get_cart_product($id, $options) {
    $p = null;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$product) {
            if ($product['id'] == $id && $product['options'] == $options) {
                $p = &$product;
                return $p;
            }
        }
    }
    return $p;
}
// Function to get the total quantity of a product option in the cart
function get_cart_option_quantity($id, $option) {
    $quantity = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product) {
            if ($product['id'] == $id && strpos($product['options'], $option) !== false) {
                $quantity += $product['quantity'];
            }
        }
    }
    return $quantity;
}
// Function to get the total quantity of a product in the cart
function get_cart_product_quantity($id) {
    $quantity = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product) {
            if ($product['id'] == $id) {
                $quantity += $product['quantity'];
            }
        }
    }
    return $quantity;
}
// Populate categories function
function populate_categories($categories, $category_list, $selected = 0, $parent_id = 0, $n = 0) {
    $html = '';
    foreach ($categories as $i => $c) {
        if ($parent_id == $c['parent_id']) {
            $padding = $n < 2 ? $n * 10 : 20;
            $html .='
            <label class="checkbox' . ($i > 4 ? ' hidden' : '') . '"' . ($padding  ? ' style="padding-left:' . $padding . 'px;"' : '') . '>
                <input type="checkbox" name="category[]" value="' . $c['id'] . '"' . (in_array($c['id'], $category_list) ? ' checked' : '') . '>
                ' . $c['title'] . '
            </label>
            ';
            $html .= populate_categories($categories, $category_list, $selected, $c['id'], $n+1);
        }
    }
    return $html;
}

// Helper function to check an individual rule
function check_rule($field_value, $operator, $expected_value) {
    // Normalize values for case-insensitivity if required
    $field_value = strtolower($field_value);
    $expected_value = strtolower($expected_value);
    switch ($operator) {
        case 'includes':
            return (strpos($field_value, $expected_value) !== false);
        case 'excludes':
            return (strpos($field_value, $expected_value) === false);
        case 'equals':
            return ($field_value === $expected_value);
        case 'not_equals':
            return ($field_value !== $expected_value);
        case 'starts_with':
            return (substr($field_value, 0, strlen($expected_value)) === $expected_value);
        case 'ends_with':
            return (substr($field_value, -strlen($expected_value)) === $expected_value);
        default:
            // Unknown operator, return false or handle error
            return false;
    }
}
// Helper function to check all rules for a given tax entry
function check_rules($rules, $fields) {
    foreach ($rules as $rule) {
        $field = $rule['field'];
        $operator = $rule['operator'];
        $value = $rule['value'];
        // If any rule fails, return false immediately
        if (!isset($fields[$field]) || !check_rule($fields[$field], $operator, $value)) {
            return false;
        }
    }
    // If we reach here, all rules passed
    return true;
}
// Send order details email function
function send_order_details_email($email, $products, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $subtotal, $order_id) {
	if (!mail_enabled) return;
    // Escapte variables
    $first_name = htmlspecialchars($first_name, ENT_QUOTES);
    $last_name = htmlspecialchars($last_name, ENT_QUOTES);
    $address_street = htmlspecialchars($address_street, ENT_QUOTES);
    $address_city = htmlspecialchars($address_city, ENT_QUOTES);
    $address_state = htmlspecialchars($address_state, ENT_QUOTES);
    $address_zip = htmlspecialchars($address_zip, ENT_QUOTES);
    $address_country = htmlspecialchars($address_country, ENT_QUOTES);
	// Include PHPMailer library
	include_once __DIR__ . '/lib/phpmailer/Exception.php';
	include_once __DIR__ . '/lib/phpmailer/PHPMailer.php';
	include_once __DIR__ . '/lib/phpmailer/SMTP.php';
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
		$mail->addAddress($email);
		$mail->addReplyTo(mail_from, mail_name);
		// Content
		$mail->isHTML(true);
        // Set UTF-8 charset
        $mail->CharSet = 'UTF-8';
        // Set email subject
		$mail->Subject = 'Order Details (#' . $order_id . ')';
        // Products template
        $products_template = '';
        foreach($products as $product) {
            $products_template .= '<tr>
                <td style="padding:25px 0;">' . htmlspecialchars($product['meta']['title'], ENT_QUOTES) . '<div style="color:#989b9e">' . htmlspecialchars($product['options'], ENT_QUOTES) . '</div></td>
                <td>' . num_format($product['final_price'],2) . '</td>
                <td>' . $product['quantity'] . '</td>
                <td style="text-align:right;">' . num_format($product['final_price'] * $product['quantity'],2) . '</td>
            </tr>';
        }
        $products_template = '
        <table style="border-collapse:collapse;width:100%;">
            <thead style="border-bottom:1px solid #eee;">
                <tr>
                    <td style="padding:25px 0;font-weight:500;font-size:14px;">Product</td>
                    <td style="font-weight:500;font-size:14px;">Price</td>
                    <td style="font-weight:500;font-size:14px;">Quantity</td>
                    <td style="text-align:right;font-weight:500;font-size:14px;">Total</td>
                </tr>
            </thead>
            <tbody>' . $products_template . '</tbody>
        </table>';
		// Read the template contents and replace the placeholders with the variables
		$email_template = str_replace(
            ['%order_id%', '%first_name%', '%last_name%', '%address_street%', '%address_city%', '%address_state%', '%address_zip%', '%address_country%', '%subtotal%', '%products_template%'], 
            [$order_id, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, num_format($subtotal, 2), $products_template],
            file_get_contents(__DIR__ . '/order-details-template.html')
        );
        // Add main tags to html
        $email_template = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,minimum-scale=1"><title>Order Details (#' . $order_id . ')</title></head><body style="margin:0;padding:0">' . $email_template . '</body></html>';
		// Set email body
		$mail->Body = $email_template;
		$mail->AltBody = strip_tags($email_template);
		// Send mail
		$response = $mail->send();
        // Send notification email
        if ($response) {
            send_order_details_notification_email($products, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $subtotal, $order_id);
        }
	} catch (Exception $e) {
		// Output error message
		exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
	}
}
// Send notification email function
function send_order_details_notification_email($products, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $subtotal, $order_id) {
	if (!mail_enabled || !notifications_enabled) return;
	// Include PHPMailer library
	include_once __DIR__ . '/lib/phpmailer/Exception.php';
	include_once __DIR__ . '/lib/phpmailer/PHPMailer.php';
	include_once __DIR__ . '/lib/phpmailer/SMTP.php';
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
		$mail->Subject = 'New Order Received (#' . $order_id . ')';
        // Products template
        $products_template = '';
        foreach($products as $product) {
            $products_template .= '<tr>
                <td style="padding:25px 0;">' . htmlspecialchars($product['meta']['title'], ENT_QUOTES) . '<div style="color:#989b9e">' . htmlspecialchars($product['options'], ENT_QUOTES) . '</div></td>
                <td>' . num_format($product['final_price'],2) . '</td>
                <td>' . $product['quantity'] . '</td>
                <td style="text-align:right;">' . num_format($product['final_price'] * $product['quantity'],2) . '</td>
            </tr>';
        }
        $products_template = '
        <table style="border-collapse:collapse;width:100%;">
            <thead style="border-bottom:1px solid #eee;">
                <tr>
                    <td style="padding:25px 0;font-weight:500;font-size:14px;">Product</td>
                    <td style="font-weight:500;font-size:14px;">Price</td>
                    <td style="font-weight:500;font-size:14px;">Quantity</td>
                    <td style="text-align:right;font-weight:500;font-size:14px;">Total</td>
                </tr>
            </thead>
            <tbody>' . $products_template . '</tbody>
        </table>';
		// Read the template contents and replace the placeholders with the variables
		$email_template = str_replace(
            ['%order_id%', '%first_name%', '%last_name%', '%address_street%', '%address_city%', '%address_state%', '%address_zip%', '%address_country%', '%subtotal%', '%products_template%'], 
            [$order_id, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, num_format($subtotal, 2), $products_template],
            file_get_contents(__DIR__ . '/order-notification-template.html')
        );
        // Add main tags to html
        $email_template = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,minimum-scale=1"><title>New Order Received (#' . $order_id . ')</title></head><body style="margin:0;padding:0">' . $email_template . '</body></html>';
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
// Routeing function
function routes($urls) {
    foreach ($urls as $url => $file_path) {
        $url = '/' . ltrim($url, '/');
        $prefix = dirname($_SERVER['PHP_SELF']);
        $uri = $_SERVER['REQUEST_URI'];
        if (substr($uri, 0, strlen($prefix)) == $prefix) {
            $uri = substr($uri, strlen($prefix));
        }
        $uri = '/' . ltrim($uri, '/');
        $path = explode('/', parse_url($uri)['path']);
        $routes = explode('/', $url);
        $values = [];
        foreach ($path as $pk => $pv) {
            if (isset($routes[$pk]) && preg_match('/{(.*?)}/', $routes[$pk])) {
                $var = str_replace(['{','}'], '', $routes[$pk]);
                $routes[$pk] = preg_replace('/{(.*?)}/', $pv, $routes[$pk]);
                $values[$var] = $pv;
            }
        }
        if ($routes === $path && rewrite_url) {
            parse_str(parse_url($file_path)['query'], $params);
            foreach ($values as $k => $v) {
                $_GET[$k] = $v;
            }
            foreach ($params as $k => $v) {
                if (!isset($_GET[$k]) && $k != 'page') {
                    $_GET[$k] = $v;
                }
            }
            return isset($params['page']) && file_exists($params['page'] . '.php') ? $params['page'] . '.php' : 'home.php';
        }
    }
    if (rewrite_url) {
        header('Location: ' . url('index.php'));
        exit;
    }
    return null;
}
// Format bytes to human-readable format
function format_bytes($bytes) {
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','KB','MB','GB','TB'][$i];
}

// Stripe update webhook function
function stripe_update_webhook($stripe) {
    if (empty(stripe_webhook_secret)) {
        // Get the config.php file contents
        $contents = file_get_contents('config.php');
        if ($contents) {
            // Define the required events
            $required_events = [ 'checkout.session.completed', 'customer.subscription.deleted', 'invoice.payment_failed', 'charge.refunded' ];
            // Try to find an existing webhook endpoint with the desired URL
            $existing = $stripe->webhookEndpoints->all(['limit' => 100]);
            $webhook = null;
            foreach ($existing->data as $endpoint) {
                if ($endpoint->url === stripe_ipn_url) {
                    $webhook = $endpoint;
                    break;
                }
            }
            if (!$webhook) {
                // No matching endpoint exists; create one with all required events.
                $webhook = $stripe->webhookEndpoints->create([
                    'url' => stripe_ipn_url,
                    'description' => 'shoppingcart',
                    'enabled_events' => $required_events
                ]);
            } else {
                // Get current enabled events from the endpoint.
                $current_events = (array)$webhook->enabled_events;
                // Merge current events with required events.
                $new_events = array_unique(array_merge($current_events, $required_events));
                // Update the webhook only if the events list has changed.
                if (count($new_events) !== count($current_events)) {
                    $webhook = $stripe->webhookEndpoints->update(
                        $webhook->id,
                        ['enabled_events' => $new_events]
                    );
                }
            }
            // Update the "stripe_webhook_secret" constant in the config.php file with the new secret
            $contents = preg_replace('/define\(\'stripe_webhook_secret\'\, ?(.*?)\)/s', 'define(\'stripe_webhook_secret\',\'' . $webhook->secret . '\')', $contents);
            if (!file_put_contents('config.php', $contents)) {
                // Could not write to config.php file
                exit('Failed to automatically assign the Stripe webhook secret! Please set it manually in the config.php file.');
            }
        } else {
            exit('Failed to automatically assign the Stripe webhook secret! Please set it manually in the config.php file.');
        }
    }
}
?>