<?php
/*
Database-driven Public Settings Loader
Replaces hardcoded public_settings.php with database queries
Maps setting_content_* tables to variables used throughout the site
*/

// Ensure database connection is available
if (!isset($pdo)) {
    die("Database connection not available. Include gws-universal-config.php first.");
}

// Initialize all variables with debug info as fallbacks - will be overwritten if DB loads successfully
// Debug variables showing what should be loaded from database
$business_name = '[DB: setting_business_identity.business_name_medium → $business_name]';
$contact_email = '[DB: setting_business_contact.primary_email → $contact_email]';
$contact_phone = '[DB: setting_business_contact.primary_phone → $contact_phone]';
$contact_address = '[DB: setting_business_contact.primary_address → $contact_address]';
$contact_city = '[DB: setting_business_contact.city → $contact_city]';
$contact_state = '[DB: setting_business_contact.state → $contact_state]';
$contact_zipcode = '[DB: setting_business_contact.zipcode → $contact_zipcode]';

// Footer branding variables
$footer_business_name_type = 'medium';
$footer_logo_enabled = true;
$footer_logo_position = 'left';
$footer_logo_file = '';

$hero_content = [
    'headline' => '[DB: setting_content_homepage.hero_headline → $hero_content[headline]]',
    'subheadline' => '[DB: setting_content_homepage.hero_subheadline → $hero_content[subheadline]]',
    'bg_image' => '[DB: setting_content_homepage.hero_background_image → $hero_content[bg_image]]'
];
$services_title = '[DB: setting_content_homepage.services_section_title → $services_title]';
$services_paragraph = '[DB: setting_content_homepage.services_section_description → $services_paragraph]';
$hero_button_text = '[DB: setting_content_homepage.hero_button_text → $hero_button_text]';
$hero_button_link = '[DB: setting_content_homepage.hero_button_link → $hero_button_link]';
$contact_title = '[DB: setting_content_homepage.contact_section_title → $contact_title]';
$contact_paragraph = '[DB: setting_content_homepage.contact_section_description → $contact_paragraph]';
$portfolio_title = '[DB: setting_content_homepage.portfolio_section_title → $portfolio_title]';
$portfolio_paragraph = '[DB: setting_content_homepage.portfolio_section_description → $portfolio_paragraph]';
$pricing_title = '[DB: setting_content_homepage.pricing_section_title → $pricing_title]';
$pricing_paragraph = '[DB: setting_content_homepage.pricing_section_description → $pricing_paragraph]';
$team_title = '[DB: setting_content_homepage.team_section_title → $team_title]';
$team_paragraph = '[DB: setting_content_homepage.team_section_description → $team_paragraph]';
$testimonials_title = '[DB: setting_content_homepage.testimonials_section_title → $testimonials_title]';
$testimonials_paragraph = '[DB: setting_content_homepage.testimonials_section_description → $testimonials_paragraph]';

$about_alt_paragraph = '[DB: setting_content_homepage.about_section_description → $about_alt_paragraph]';
$about_alt_heading = '[DB: setting_content_homepage.about_section_title → $about_alt_heading]';
$about_alt_italic = '[DB: setting_content_homepage.about_section_subtitle → $about_alt_italic]';
$about_alt_list = ['[DB: setting_content_homepage.about_section_list → $about_alt_list]'];
$about_content = [
    'title' => '[DB: setting_content_homepage.about_section_title → $about_content[title]]',
    'text' => '[DB: setting_content_homepage.about_section_description → $about_content[text]]',
    'side_text' => '[DB: setting_content_homepage.about_section_subtitle → $about_content[side_text]]'
];

// Load homepage content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_homepage WHERE id = 1 LIMIT 1");
    $homepage_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($homepage_data) {
        // Map homepage database fields to variables - LOADED SUCCESSFULLY
        $hero_content = [
            'headline' => $homepage_data['hero_headline'] ?? '[MISSING: setting_content_homepage.hero_headline]',
            'subheadline' => $homepage_data['hero_subheadline'] ?? '[MISSING: setting_content_homepage.hero_subheadline]',
            // Do NOT force a default public image; leave empty if none chosen
            'bg_image' => !empty($homepage_data['hero_background_image']) ? $homepage_data['hero_background_image'] : ''
        ];
        
        $hero_button_text = $homepage_data['hero_button_text'] ?? '[MISSING: setting_content_homepage.hero_button_text]';
        $hero_button_link = $homepage_data['hero_button_link'] ?? '[MISSING: setting_content_homepage.hero_button_link]';
        $hero_form_top_text = $homepage_data['hero_form_top_text'] ?? 'No obligation. No spam. Just a real offer from your local neighbor.';
        $hero_form_side_text = $homepage_data['hero_form_side_text'] ?? 'GET STARTED!';
        $hero_form_button_text = $homepage_data['hero_form_button_text'] ?? 'Get My Offer';
        $services_title = $homepage_data['services_section_title'] ?? '[MISSING: setting_content_homepage.services_section_title]';
        $services_paragraph = $homepage_data['services_section_description'] ?? '[MISSING: setting_content_homepage.services_section_description]';
        $portfolio_title = $homepage_data['portfolio_section_title'] ?? '[MISSING: setting_content_homepage.portfolio_section_title]';
        $portfolio_paragraph = $homepage_data['portfolio_section_description'] ?? '[MISSING: setting_content_homepage.portfolio_section_description]';
        $team_title = $homepage_data['team_section_title'] ?? '[MISSING: setting_content_homepage.team_section_title]';
        $team_paragraph = $homepage_data['team_section_description'] ?? '[MISSING: setting_content_homepage.team_section_description]';
        $testimonials_title = $homepage_data['testimonials_section_title'] ?? '[MISSING: setting_content_homepage.testimonials_section_title]';
        $testimonials_paragraph = $homepage_data['testimonials_section_description'] ?? '[MISSING: setting_content_homepage.testimonials_section_description]';
        $team_title = $homepage_data['team_section_title'] ?? '[MISSING: setting_content_homepage.team_section_title]';
        $team_paragraph = $homepage_data['team_section_description'] ?? '[MISSING: setting_content_homepage.team_section_description]';
        $contact_title = $homepage_data['contact_section_title'] ?? '[MISSING: setting_content_homepage.contact_section_title]';
        $contact_paragraph = $homepage_data['contact_section_description'] ?? '[MISSING: setting_content_homepage.contact_section_description]';
        $pricing_title = $homepage_data['pricing_section_title'] ?? '[MISSING: setting_content_homepage.pricing_section_title]';
        $pricing_paragraph = $homepage_data['pricing_section_description'] ?? '[MISSING: setting_content_homepage.pricing_section_description]';
        $about_content = [
            'title' => $homepage_data['about_section_title'] ?? '[MISSING: setting_content_homepage.about_section_title]',
            'text' => $homepage_data['about_section_description'] ?? '[MISSING: setting_content_homepage.about_section_description]',
            'side_text' => $homepage_data['about_section_subtitle'] ?? '[MISSING: setting_content_homepage.about_section_subtitle]'
        ];
        
        // Map individual about variables for alt.php compatibility
        $about_alt_heading = $homepage_data['about_section_title'] ?? 'About Our Company';
        $about_alt_italic = $homepage_data['about_section_subtitle'] ?? 'Our mission and values';
        $about_alt_paragraph = $homepage_data['about_section_description'] ?? 'Learn more about our company and what we do.';
        
        // Handle about_section_list - assume it's stored as JSON or comma-separated
        $about_alt_list = [];
        if (isset($homepage_data['about_section_list']) && !empty($homepage_data['about_section_list'])) {
            // Try to decode as JSON first, then fall back to comma-separated
            $list_data = json_decode($homepage_data['about_section_list'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($list_data)) {
                $about_alt_list = $list_data;
            } else {
                // Fall back to comma-separated values
                $about_alt_list = array_map('trim', explode(',', $homepage_data['about_section_list']));
            }
        }
        // Default list if empty
        if (empty($about_alt_list)) {
            $about_alt_list = ['Professional service', 'Quality results', 'Customer satisfaction'];
        }
        
        // Add debug info to show what was loaded
        $debug_homepage_loaded = "✅ LOADED: setting_content_homepage (ID: {$homepage_data['id']})";
        
    } else {
        $debug_homepage_loaded = "❌ NO DATA: setting_content_homepage table exists but no row with id=1";
    }
} catch (Exception $e) {
    error_log("Error loading homepage content: " . $e->getMessage());
    $debug_homepage_loaded = "❌ ERROR: setting_content_homepage - " . $e->getMessage();
}

// Load business info from database using normalized tables
try {
    $stmt = $pdo->query("
        SELECT 
            bi.business_name_short,
            bi.business_name_medium,
            bi.business_name_long,
            bi.business_tagline_medium,
            bi.legal_business_name,
            bi.footer_business_name_type,
            bi.footer_logo_enabled,
            bi.footer_logo_position,
            bi.footer_logo_file,
            bc.primary_email,
            bc.primary_phone,
            bc.primary_address,
            bc.city,
            bc.state,
            bc.zipcode,
            bc.website_url
        FROM setting_business_identity bi 
        LEFT JOIN setting_business_contact bc ON bi.id = bc.business_identity_id 
        WHERE bi.id = 1 
        LIMIT 1
    ");
    $business_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($business_data) {
        // Map business database fields to variables using normalized tables
        $business_name = $business_data['business_name_medium'] ?? '[MISSING: setting_business_identity.business_name_medium]';
        $business_name_short = $business_data['business_name_short'] ?? '[MISSING: setting_business_identity.business_name_short]';
        $business_name_long = $business_data['business_name_long'] ?? '[MISSING: setting_business_identity.business_name_long]';
        $business_tagline = $business_data['business_tagline_medium'] ?? '[MISSING: setting_business_identity.business_tagline_medium]';
        $legal_business_name = $business_data['legal_business_name'] ?? '[MISSING: setting_business_identity.legal_business_name]';
        
        // Footer branding settings
        $footer_business_name_type = $business_data['footer_business_name_type'] ?? 'medium';
        $footer_logo_enabled = (bool)($business_data['footer_logo_enabled'] ?? true);
        $footer_logo_position = $business_data['footer_logo_position'] ?? 'left';
        $footer_logo_file = $business_data['footer_logo_file'] ?? '';
        
        $contact_email = $business_data['primary_email'] ?? '[MISSING: setting_business_contact.primary_email]';
        $contact_phone = $business_data['primary_phone'] ?? '[MISSING: setting_business_contact.primary_phone]';
        $contact_address = $business_data['primary_address'] ?? '[MISSING: setting_business_contact.primary_address]';
        $contact_city = $business_data['city'] ?? '[MISSING: setting_business_contact.city]';
        $contact_state = $business_data['state'] ?? '[MISSING: setting_business_contact.state]';
        $contact_zipcode = $business_data['zipcode'] ?? '[MISSING: setting_business_contact.zipcode]';
        $business_website = $business_data['website_url'] ?? '[MISSING: setting_business_contact.website_url]';
        
        $debug_business_loaded = "✅ LOADED: setting_business_identity + setting_business_contact ({$business_data['business_name_medium']})";
    } else {
        // Set default values if no data found
        $footer_business_name_type = 'medium';
        $footer_logo_enabled = true;
        $footer_logo_position = 'left';
        $footer_logo_file = '';
        $debug_business_loaded = "❌ NO DATA: Business tables exist but no joined data found with id=1";
    }
} catch (Exception $e) {
    error_log("Error loading business info: " . $e->getMessage());
    // Set default values if database error occurs
    $footer_business_name_type = 'medium';
    $footer_logo_enabled = true;
    $footer_logo_position = 'left';
    $footer_logo_file = '';
    $debug_business_loaded = "❌ ERROR: setting_business_identity/contact - " . $e->getMessage();
}

// Hero background image now sourced directly from setting_content_homepage (removed dependency on setting_branding_assets)

// Load services content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_services WHERE is_active = 1 ORDER BY service_order ASC");
    $services_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_services_loaded = "✅ LOADED: setting_content_services (" . count($services_data) . " active services)";
    
    // Map database services to expected format for services.php
    $services = [];
    foreach ($services_data as $service_row) {
        $services[] = [
            'title' => $service_row['service_title'] ?? 'Service Title',
            'desc' => $service_row['service_description'] ?? 'Service description',
            'url' => $service_row['service_link'] ?? '#'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading services content: " . $e->getMessage());
    $services_data = [];
    $services = [];
    $debug_services_loaded = "❌ ERROR: setting_content_services - " . $e->getMessage();
}

// Load stats content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_stats WHERE is_active = 1 ORDER BY stat_order ASC");
    $stats_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_stats_loaded = "✅ LOADED: setting_content_stats (" . count($stats_data) . " active stats)";
    
    // Map database stats to expected format for stats.php
    $stats = [];
    foreach ($stats_data as $stat_row) {
        $stats[] = [
            'value' => $stat_row['stat_value'] ?? '0',
            'label' => $stat_row['stat_label'] ?? 'Statistic'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading stats content: " . $e->getMessage());
    $stats_data = [];
    $stats = [];
    $debug_stats_loaded = "❌ ERROR: setting_content_stats - " . $e->getMessage();
}

// Load testimonials content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_testimonials WHERE is_active = 1 ORDER BY testimonial_order ASC");
    $testimonials_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_testimonials_loaded = "✅ LOADED: setting_content_testimonials (" . count($testimonials_data) . " active testimonials)";
    
    // Map database testimonials to expected format for testimonials.php
    $testimonials = [];
    foreach ($testimonials_data as $testimonial_row) {
        $testimonials[] = [
            'name' => $testimonial_row['client_name'] ?? 'Client Name',
            'role' => $testimonial_row['client_role'] ?? 'Position',
            'text' => $testimonial_row['testimonial_text'] ?? 'Great service and professional results.',
            'image' => $testimonial_row['client_image'] ?? 'assets/img/testimonials/default-person.jpg'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading testimonials content: " . $e->getMessage());
    $testimonials_data = [];
    $testimonials = [];
    $debug_testimonials_loaded = "❌ ERROR: setting_content_testimonials - " . $e->getMessage();
}

// Load clients content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_clients WHERE is_active = 1 ORDER BY client_order ASC");
    $clients_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_clients_loaded = "✅ LOADED: setting_content_clients (" . count($clients_data) . " active clients)";
    
    // Map database clients to expected format for clients.php
    $clients = [];
    foreach ($clients_data as $client_row) {
        $clients[] = [
            'name' => $client_row['client_name'] ?? 'Client Name',
            'logo' => $client_row['client_logo'] ?? 'assets/img/clients/default-client.png',
            'website' => $client_row['client_website'] ?? '#',
            'alt_text' => $client_row['client_name'] ?? 'Client Logo'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading clients content: " . $e->getMessage());
    $clients_data = [];
    $clients = [];
    $debug_clients_loaded = "❌ ERROR: setting_content_clients - " . $e->getMessage();
}

// Load portfolio content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_portfolio WHERE is_active = 1 ORDER BY portfolio_order ASC");
    $portfolio_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_portfolio_loaded = "✅ LOADED: setting_content_portfolio (" . count($portfolio_data) . " active portfolio items)";
    
    // Map database portfolio to expected format for portfolio.php
    $portfolio_items = [];
    foreach ($portfolio_data as $portfolio_row) {
        $portfolio_items[] = [
            'title' => $portfolio_row['project_title'] ?? 'Project Title',
            'description' => $portfolio_row['project_description'] ?? 'Project description',
            'category' => $portfolio_row['project_category'] ?? 'all',
            'image' => $portfolio_row['project_image'] ?? 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg',
            'large_image' => $portfolio_row['project_large_image'] ?? $portfolio_row['project_image'],
            'url' => $portfolio_row['project_url'] ?? '#',
            'filter_class' => 'filter-' . ($portfolio_row['project_category'] ?? 'all'),
            'gallery_name' => 'portfolio-gallery-' . ($portfolio_row['project_category'] ?? 'all')
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading portfolio content: " . $e->getMessage());
    $portfolio_data = [];
    $portfolio_items = [];
    $debug_portfolio_loaded = "❌ ERROR: setting_content_portfolio - " . $e->getMessage();
}

// Load team content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_team WHERE is_active = 1 ORDER BY display_order ASC");
    $team_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug_team_loaded = "✅ LOADED: setting_content_team (" . count($team_data) . " active team members)";
    
    // Map database team to expected format for team.php
    $team_members = [];
    foreach ($team_data as $team_row) {
        $team_members[] = [
            'name' => $team_row['member_name'] ?? 'Team Member',
            'title' => $team_row['member_role'] ?? 'Position',
            'bio' => $team_row['member_bio'] ?? 'Team member biography',
            'image' => $team_row['member_image'] ?? 'assets/img/team/team-1.jpg'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading team content: " . $e->getMessage());
    $team_data = [];
    $team_members = [];
    $debug_team_loaded = "❌ ERROR: setting_content_team - " . $e->getMessage();
}

// Load pricing content from database
try {
    $stmt = $pdo->query("SELECT * FROM setting_content_pricing WHERE is_active = 1 ORDER BY plan_order ASC");
    $pricing_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert to the format expected by pricing.php
    $pricing_plans = [];
    foreach ($pricing_data as $plan) {
        $pricing_plans[] = [
            'name' => $plan['plan_name'],
            'price' => $plan['plan_price'],
            'description' => $plan['plan_short_desc'],
            'features' => json_decode($plan['plan_features'], true) ?? [],
            'button_text' => $plan['plan_button_text'] ?? 'Get Started',
            'button_link' => $plan['plan_button_link'] ?? '#contact',
            'featured' => $plan['is_featured'] ?? false,
            'popular' => $plan['is_popular'] ?? false
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading pricing content: " . $e->getMessage());
    $pricing_data = [];
    $pricing_plans = [];
}

// Contact form labels and other static content
$contact_form_labels = [
    'name' => 'Your Name',
    'email' => 'Your Email', 
    'subject' => 'Subject',
    'message' => 'Message',
    'send_button' => 'Send Message',
    'loading' => 'Loading',
    'success' => 'Your message has been sent. Thank you!'
];

$portfolio_filters = ['All', 'Apps', 'Products', 'Websites'];

// Load footer useful links from database
try {
    $stmt = $pdo->query("SELECT title, url FROM setting_footer_useful_links WHERE is_active = 1 ORDER BY display_order ASC");
    $footer_links_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert database results to the array format expected by footer
    $footer_links = [];
    foreach ($footer_links_result as $link) {
        $footer_links[$link['title']] = $link['url'];
    }
    
    // If no database results, provide fallback links (these are different from special links)
    if (empty($footer_links)) {
        $footer_links = [
            'About Us' => '/about',
            'Reviews' => '/reviews',
            'FAQs' => '/faq', 
            'Resources' => '/resources',
            'Contact' => '/contact'
        ];
    }
    
} catch (Exception $e) {
    error_log("Error loading footer useful links: " . $e->getMessage());
    // Fallback footer useful links (different from special links at bottom)
    $footer_links = [
        'About Us' => '/about',
        'Reviews' => '/reviews',
        'FAQs' => '/faq',
        'Resources' => '/resources', 
        'Contact' => '/contact'
    ];
}

$social_links = [
    'facebook' => '#',
    'twitter' => '#', 
    'linkedin' => '#',
    'instagram' => '#'
];

$faqs = [
    [
        'question' => 'Database loading enabled',
        'answer' => 'All content now loads from database tables'
    ]
];

// Load business contact info from branding settings
try {
    $stmt = $pdo->query("SELECT * FROM setting_business_info WHERE id = 1 LIMIT 1");
    $business_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($business_info) {
        $business_name = $business_info['business_name'] ?? '';
        $contact_email = $business_info['business_email'] ?? '';
        $contact_phone = $business_info['business_phone'] ?? '';
        $contact_address = $business_info['business_address'] ?? '';
        $contact_city = $business_info['business_city'] ?? '';
        $contact_state = $business_info['business_state'] ?? '';
        $contact_zipcode = $business_info['business_zipcode'] ?? '';
    }
    
} catch (Exception $e) {
    error_log("Error loading business info: " . $e->getMessage());
    // Contact variables remain empty, showing what needs to be set up
}

// Default arrays for compatibility
$contact_form_labels = [
    'name' => 'Your Name',
    'email' => 'Your Email', 
    'subject' => 'Subject',
    'message' => 'Message',
    'send_button' => 'Send Message',
    'loading' => 'Loading',
    'success' => 'Your message has been sent. Thank you!'
];

$portfolio_filters = ['All', 'Apps', 'Products', 'Websites'];

$social_links = [
    'facebook' => '#',
    'twitter' => '#', 
    'linkedin' => '#',
    'instagram' => '#'
];

$about_content = [
    'title' => 'About Us',
    'text' => 'Content will be loaded from database',
    'side_text' => 'Database integration in progress'
];

$faqs = [
    [
        'question' => 'Database loading enabled',
        'answer' => 'All content now loads from database tables'
    ]
];

// Debug info for development - ALWAYS SHOW TO TRACK DATABASE LOADING
echo "\n<!-- DATABASE LOADING DEBUG INFO:\n";
echo isset($debug_homepage_loaded) ? $debug_homepage_loaded . "\n" : "❌ Homepage loading not attempted\n";
echo isset($debug_business_loaded) ? $debug_business_loaded . "\n" : "❌ Business info loading not attempted\n";
echo isset($debug_hero_bg_loaded) ? $debug_hero_bg_loaded . "\n" : "❌ Hero background loading not attempted\n";
echo isset($debug_services_loaded) ? $debug_services_loaded . "\n" : "❌ Services loading not attempted\n";
echo isset($debug_stats_loaded) ? $debug_stats_loaded . "\n" : "❌ Stats loading not attempted\n";
echo isset($debug_testimonials_loaded) ? $debug_testimonials_loaded . "\n" : "❌ Testimonials loading not attempted\n";
echo isset($debug_clients_loaded) ? $debug_clients_loaded . "\n" : "❌ Clients loading not attempted\n";
echo isset($debug_portfolio_loaded) ? $debug_portfolio_loaded . "\n" : "❌ Portfolio loading not attempted\n";
echo isset($debug_team_loaded) ? $debug_team_loaded . "\n" : "❌ Team loading not attempted\n";
echo isset($debug_pricing_loaded) ? $debug_pricing_loaded . "\n" : "❌ Pricing loading not attempted\n";
echo "\nVARIABLE SAMPLES:\n";
echo "business_name = " . ($business_name ?? 'UNDEFINED') . "\n";
echo "contact_email = " . ($contact_email ?? 'UNDEFINED') . "\n";
echo "contact_phone = " . ($contact_phone ?? 'UNDEFINED') . "\n";
echo "hero_content[headline] = " . ($hero_content['headline'] ?? 'UNDEFINED') . "\n";
echo "hero_content[bg_image] = " . ($hero_content['bg_image'] ?? 'UNDEFINED') . "\n";
echo "services_title = " . ($services_title ?? 'UNDEFINED') . "\n";
echo "testimonials_title = " . ($testimonials_title ?? 'UNDEFINED') . "\n";
echo "-->\n\n";
?>
