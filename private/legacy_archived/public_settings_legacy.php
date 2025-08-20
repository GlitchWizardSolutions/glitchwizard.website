<?php
/*
Page: public_settings.php
Location: public_html/admin/settings/public_settings.php
Purpose: Admin panel to view and update all public-facing settings for the website.
*/

include_once '../assets/includes/main.php';
// Dynamically locate and include private/gws-universal-config.php
$config_found = false;
$max_levels = 5; // Maximum directory levels to traverse up
$config_path = 'private/gws-universal-config.php';
$dir = __DIR__;
for ($i = 0; $i <= $max_levels; $i++)
{
    $try_path = $dir . str_repeat('/..', $i) . '/' . $config_path;
    if (file_exists($try_path))
    {
        require_once $try_path;
        $config_found = true;
        break;
    }
}
// Initialize upload success/error variables
$hero_bg_upload_success = false;
$hero_bg_upload_error = '';
if (!$config_found)
{
    die('Critical error: Could not locate private/gws-universal-config.php');
}

// Load current settings
$settings_path = '../../assets/includes/settings/public_settings.php';
if (file_exists($settings_path))
{
    include $settings_path;
} else
{
    die('Settings file not found.');
}

// Check if there's generated code from edit_public_settings.php
if (isset($_GET['show_code']) && isset($_SESSION['generated_settings_code'])) {
    $show_generated_code = true;
    $generated_settings_code = $_SESSION['generated_settings_code'];
    unset($_SESSION['generated_settings_code']); // Clear it after use
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all posted values
    $new_settings = [];
    foreach ($_POST as $key => $value)
    {
        $new_settings[$key] = $value;
    }

    // Preserve existing services if none submitted
    $services_array = [];
    if (isset($_POST['service_count']) && (int) $_POST['service_count'] > 0)
    {
        for ($s = 0; $s < (int) $_POST['service_count']; $s++)
        {
            $title = isset($_POST['service_title_' . $s]) ? trim($_POST['service_title_' . $s]) : '';
            $desc = isset($_POST['service_desc_' . $s]) ? trim($_POST['service_desc_' . $s]) : '';
            if ($title !== '' || $desc !== '')
            {
                $services_array[] = [
                    'title' => $title,
                    'desc' => $desc,
                    'url' => ''
                ];
            }
        }
    } else
    {
        // If no service fields submitted, use existing $services
        $services_array = $services;
    }

    // Build PHP code for settings file
    $settings_code = "<?php\n/*\nPage: public_settings.php\nLocation: public_html/assets/includes/settings/public_settings.php\nPurpose: Centralized public-facing settings for business branding, content, and SEO. Edit this file to customize the entire public website for a new business/client.\n*/\n\n";

    // Business Info now managed by comprehensive branding system
    // These values preserved for backward compatibility but not editable here
    $settings_code .= "// Business Info (managed by branding system - these are fallback values)\n";
    $settings_code .= "\$business_name = '" . addslashes($business_name ?? '') . "';\n";
    $settings_code .= "\$business_logo = '" . addslashes($business_logo ?? '') . "';\n";
    $settings_code .= "\$favicon = '" . addslashes($favicon ?? '') . "';\n";
    $settings_code .= "\$apple_touch_icon = '" . addslashes($apple_touch_icon ?? '') . "';\n";
    $settings_code .= "\$author = '" . addslashes($author ?? '') . "';\n\n";

    // Contact
    $settings_code .= "// Contact Section\n";
    $settings_code .= "\$contact_email = '" . addslashes((isset($new_settings['contact_email']) && trim($new_settings['contact_email']) !== '') ? $new_settings['contact_email'] : ($contact_email ?? '')) . "';\n";
    $settings_code .= "\$contact_phone = '" . addslashes((isset($new_settings['contact_phone']) && trim($new_settings['contact_phone']) !== '') ? $new_settings['contact_phone'] : ($contact_phone ?? '')) . "';\n";
    $settings_code .= "\$contact_address = '" . addslashes((isset($new_settings['contact_address']) && trim($new_settings['contact_address']) !== '') ? $new_settings['contact_address'] : ($contact_address ?? '')) . "';\n\n";

    // Hero Section
    $settings_code .= "// Hero Section\n";
    $settings_code .= "\$hero_content = [\n";
    $settings_code .= "    'headline' => '" . addslashes((isset($new_settings['hero_headline']) && trim($new_settings['hero_headline']) !== '') ? $new_settings['hero_headline'] : ($hero_content['headline'] ?? '')) . "',\n";
    $settings_code .= "    'subheadline' => '" . addslashes((isset($new_settings['hero_subheadline']) && trim($new_settings['hero_subheadline']) !== '') ? $new_settings['hero_subheadline'] : ($hero_content['subheadline'] ?? '')) . "',\n";
    $settings_code .= "    'bg_image' => '" . addslashes((isset($new_settings['hero_bg_image']) && trim($new_settings['hero_bg_image']) !== '') ? $new_settings['hero_bg_image'] : ($hero_content['bg_image'] ?? '')) . "'\n";
    $settings_code .= "];\n\n";

    // Section titles and content that need to be configurable
    $settings_code .= "// Section Titles and Content\n";
    $settings_code .= "\$services_title = '" . addslashes((isset($new_settings['services_title']) && trim($new_settings['services_title']) !== '') ? $new_settings['services_title'] : ($services_title ?? 'Services')) . "';\n";
    $settings_code .= "\$services_paragraph = '" . addslashes((isset($new_settings['services_paragraph']) && trim($new_settings['services_paragraph']) !== '') ? $new_settings['services_paragraph'] : ($services_paragraph ?? 'Explore our creative and practical services designed to help your business thrive.')) . "';\n";
    $settings_code .= "\$hero_button_text = '" . addslashes((isset($new_settings['hero_button_text']) && trim($new_settings['hero_button_text']) !== '') ? $new_settings['hero_button_text'] : ($hero_button_text ?? 'Get Started')) . "';\n";
    $settings_code .= "\$hero_button_link = '" . addslashes((isset($new_settings['hero_button_link']) && trim($new_settings['hero_button_link']) !== '') ? $new_settings['hero_button_link'] : ($hero_button_link ?? 'about.php')) . "';\n";
    $settings_code .= "\$contact_title = '" . addslashes((isset($new_settings['contact_title']) && trim($new_settings['contact_title']) !== '') ? $new_settings['contact_title'] : ($contact_title ?? 'Contact')) . "';\n";
    $settings_code .= "\$contact_paragraph = '" . addslashes((isset($new_settings['contact_paragraph']) && trim($new_settings['contact_paragraph']) !== '') ? $new_settings['contact_paragraph'] : ($contact_paragraph ?? 'We look forward to serving you!')) . "';\n";
    $settings_code .= "\$portfolio_title = '" . addslashes((isset($new_settings['portfolio_title']) && trim($new_settings['portfolio_title']) !== '') ? $new_settings['portfolio_title'] : ($portfolio_title ?? 'Portfolio')) . "';\n";
    $settings_code .= "\$portfolio_paragraph = '" . addslashes((isset($new_settings['portfolio_paragraph']) && trim($new_settings['portfolio_paragraph']) !== '') ? $new_settings['portfolio_paragraph'] : ($portfolio_paragraph ?? 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.')) . "';\n";
    $settings_code .= "\$pricing_title = '" . addslashes((isset($new_settings['pricing_title']) && trim($new_settings['pricing_title']) !== '') ? $new_settings['pricing_title'] : ($pricing_title ?? 'Pricing')) . "';\n";
    $settings_code .= "\$pricing_paragraph = '" . addslashes((isset($new_settings['pricing_paragraph']) && trim($new_settings['pricing_paragraph']) !== '') ? $new_settings['pricing_paragraph'] : ($pricing_paragraph ?? 'Choose the plan that fits your needs. All plans are fully customizable.')) . "';\n";
    $settings_code .= "\$team_title = '" . addslashes((isset($new_settings['team_title']) && trim($new_settings['team_title']) !== '') ? $new_settings['team_title'] : ($team_title ?? 'Team')) . "';\n";
    $settings_code .= "\$team_paragraph = '" . addslashes((isset($new_settings['team_paragraph']) && trim($new_settings['team_paragraph']) !== '') ? $new_settings['team_paragraph'] : ($team_paragraph ?? 'Meet our talented team of professionals dedicated to your success.')) . "';\n";
    $settings_code .= "\$testimonials_title = '" . addslashes((isset($new_settings['testimonials_title']) && trim($new_settings['testimonials_title']) !== '') ? $new_settings['testimonials_title'] : ($testimonials_title ?? 'Testimonials')) . "';\n";
    $settings_code .= "\$testimonials_paragraph = '" . addslashes((isset($new_settings['testimonials_paragraph']) && trim($new_settings['testimonials_paragraph']) !== '') ? $new_settings['testimonials_paragraph'] : ($testimonials_paragraph ?? 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit')) . "';\n\n";

    // Contact form labels
    $settings_code .= "// Contact Form Labels\n";
    $settings_code .= "\$contact_form_labels = [\n";
    $settings_code .= "    'name' => '" . addslashes((isset($new_settings['contact_form_name']) && trim($new_settings['contact_form_name']) !== '') ? $new_settings['contact_form_name'] : ($contact_form_labels['name'] ?? 'Your Name')) . "',\n";
    $settings_code .= "    'email' => '" . addslashes((isset($new_settings['contact_form_email']) && trim($new_settings['contact_form_email']) !== '') ? $new_settings['contact_form_email'] : ($contact_form_labels['email'] ?? 'Your Email')) . "',\n";
    $settings_code .= "    'subject' => '" . addslashes((isset($new_settings['contact_form_subject']) && trim($new_settings['contact_form_subject']) !== '') ? $new_settings['contact_form_subject'] : ($contact_form_labels['subject'] ?? 'Subject')) . "',\n";
    $settings_code .= "    'message' => '" . addslashes((isset($new_settings['contact_form_message']) && trim($new_settings['contact_form_message']) !== '') ? $new_settings['contact_form_message'] : ($contact_form_labels['message'] ?? 'Message')) . "',\n";
    $settings_code .= "    'send_button' => '" . addslashes((isset($new_settings['contact_form_send_button']) && trim($new_settings['contact_form_send_button']) !== '') ? $new_settings['contact_form_send_button'] : ($contact_form_labels['send_button'] ?? 'Send Message')) . "',\n";
    $settings_code .= "    'loading' => '" . addslashes((isset($new_settings['contact_form_loading']) && trim($new_settings['contact_form_loading']) !== '') ? $new_settings['contact_form_loading'] : ($contact_form_labels['loading'] ?? 'Loading')) . "',\n";
    $settings_code .= "    'success' => '" . addslashes((isset($new_settings['contact_form_success']) && trim($new_settings['contact_form_success']) !== '') ? $new_settings['contact_form_success'] : ($contact_form_labels['success'] ?? 'Your message has been sent. Thank you!')) . "'\n";
    $settings_code .= "];\n\n";

    // Portfolio filters
    $settings_code .= "// Portfolio Filters\n";
    $settings_code .= "\$portfolio_filters = ['" . addslashes((isset($new_settings['portfolio_filter_0']) && trim($new_settings['portfolio_filter_0']) !== '') ? $new_settings['portfolio_filter_0'] : ($portfolio_filters[0] ?? 'All')) . "', '" . addslashes((isset($new_settings['portfolio_filter_1']) && trim($new_settings['portfolio_filter_1']) !== '') ? $new_settings['portfolio_filter_1'] : ($portfolio_filters[1] ?? 'Apps')) . "', '" . addslashes((isset($new_settings['portfolio_filter_2']) && trim($new_settings['portfolio_filter_2']) !== '') ? $new_settings['portfolio_filter_2'] : ($portfolio_filters[2] ?? 'Products')) . "', '" . addslashes((isset($new_settings['portfolio_filter_3']) && trim($new_settings['portfolio_filter_3']) !== '') ? $new_settings['portfolio_filter_3'] : ($portfolio_filters[3] ?? 'Websites')) . "'];\n\n";
    $settings_code .= "// About Alt Section (customizable content)\n";
    $settings_code .= "\$about_alt_heading = '" . addslashes((isset($new_settings['about_alt_heading']) && trim($new_settings['about_alt_heading']) !== '') ? $new_settings['about_alt_heading'] : ($about_alt_heading ?? '')) . "';\n";
    $settings_code .= "\$about_alt_italic = '" . addslashes((isset($new_settings['about_alt_italic']) && trim($new_settings['about_alt_italic']) !== '') ? $new_settings['about_alt_italic'] : ($about_alt_italic ?? '')) . "';\n";
    $settings_code .= "\$about_alt_list = [\n    '" . addslashes((isset($new_settings['about_alt_list_1']) && trim($new_settings['about_alt_list_1']) !== '') ? $new_settings['about_alt_list_1'] : ($about_alt_list[0] ?? '')) . "',\n    '" . addslashes((isset($new_settings['about_alt_list_2']) && trim($new_settings['about_alt_list_2']) !== '') ? $new_settings['about_alt_list_2'] : ($about_alt_list[1] ?? '')) . "',\n    '" . addslashes((isset($new_settings['about_alt_list_3']) && trim($new_settings['about_alt_list_3']) !== '') ? $new_settings['about_alt_list_3'] : ($about_alt_list[2] ?? '')) . "'\n];\n";
    $settings_code .= "\$about_alt_paragraph = '" . addslashes((isset($new_settings['about_alt_paragraph']) && trim($new_settings['about_alt_paragraph']) !== '') ? $new_settings['about_alt_paragraph'] : ($about_alt_paragraph ?? '')) . "';\n\n";
    $settings_code .= "];\n\n";

    // About Section
    $settings_code .= "// About Section\n";
    $settings_code .= "\$about_content = [\n";
    // Title
    if (isset($new_settings['about_title']) && trim($new_settings['about_title']) !== '')
    {
        $settings_code .= "    'title' => '" . addslashes($new_settings['about_title']) . "',\n";
    } else
    {
        $settings_code .= "    'title' => '" . addslashes($about_content['title'] ?? 'About Us') . "',\n";
    }
    // Main Text
    if (isset($new_settings['about_text']) && trim($new_settings['about_text']) !== '')
    {
        $settings_code .= "    'text' => '" . addslashes($new_settings['about_text']) . "',\n";
    } else
    {
        $settings_code .= "    'text' => '" . addslashes($about_content['text'] ?? '') . "',\n";
    }
    // Side Text
    if (isset($new_settings['about_side_text']) && trim($new_settings['about_side_text']) !== '')
    {
        $settings_code .= "    'side_text' => '" . addslashes($new_settings['about_side_text']) . "'\n";
    } else
    {
        $settings_code .= "    'side_text' => '" . addslashes($about_content['side_text'] ?? '') . "'\n";
    }
    $settings_code .= "];\n\n";

    // Footer Links
    $settings_code .= "// Footer Links\n";
    $settings_code .= "\$footer_links = [\n";
    $settings_code .= "    'accessibility_policy' => '" . addslashes((isset($new_settings['footer_accessibility_policy']) && trim($new_settings['footer_accessibility_policy']) !== '') ? $new_settings['footer_accessibility_policy'] : ($footer_links['accessibility_policy'] ?? '')) . "',\n";
    $settings_code .= "    'terms_of_service' => '" . addslashes((isset($new_settings['footer_terms_of_service']) && trim($new_settings['footer_terms_of_service']) !== '') ? $new_settings['footer_terms_of_service'] : ($footer_links['terms_of_service'] ?? '')) . "',\n";
    $settings_code .= "    'privacy_policy' => '" . addslashes((isset($new_settings['footer_privacy_policy']) && trim($new_settings['footer_privacy_policy']) !== '') ? $new_settings['footer_privacy_policy'] : ($footer_links['privacy_policy'] ?? '')) . "',\n";
    $settings_code .= "    'faq' => '" . addslashes((isset($new_settings['footer_faq']) && trim($new_settings['footer_faq']) !== '') ? $new_settings['footer_faq'] : ($footer_links['faq'] ?? '')) . "',\n";
    $settings_code .= "    'webmaster_email' => '" . addslashes((isset($new_settings['footer_webmaster_email']) && trim($new_settings['footer_webmaster_email']) !== '') ? $new_settings['footer_webmaster_email'] : ($footer_links['webmaster_email'] ?? '')) . "'\n";
    $settings_code .= "];\n\n";

    // Social Links
    $settings_code .= "// Social Links\n";
    $settings_code .= "\$social_links = [\n";
    $settings_code .= "    'facebook' => '" . addslashes((isset($new_settings['social_facebook']) && trim($new_settings['social_facebook']) !== '') ? $new_settings['social_facebook'] : ($social_links['facebook'] ?? '')) . "',\n";
    $settings_code .= "    'twitter' => '" . addslashes((isset($new_settings['social_twitter']) && trim($new_settings['social_twitter']) !== '') ? $new_settings['social_twitter'] : ($social_links['twitter'] ?? '')) . "',\n";
    $settings_code .= "    'linkedin' => '" . addslashes((isset($new_settings['social_linkedin']) && trim($new_settings['social_linkedin']) !== '') ? $new_settings['social_linkedin'] : ($social_links['linkedin'] ?? '')) . "',\n";
    $settings_code .= "    'instagram' => '" . addslashes((isset($new_settings['social_instagram']) && trim($new_settings['social_instagram']) !== '') ? $new_settings['social_instagram'] : ($social_links['instagram'] ?? '')) . "'\n";
    $settings_code .= "];\n\n";

    // FAQs
    $faq_count = isset($new_settings['faq_count']) ? (int) $new_settings['faq_count'] : 0;
    $faqs_array = [];
    for ($i = 0; $i <= $faq_count; $i++)
    {
        $question = isset($new_settings['faq_question_' . $i]) ? trim($new_settings['faq_question_' . $i]) : '';
        $answer = isset($new_settings['faq_answer_' . $i]) ? trim($new_settings['faq_answer_' . $i]) : '';
        if ($question !== '' && $answer !== '')
        {
            $faqs_array[] = [
                'question' => $question,
                'answer' => $answer
            ];
        }
    }
    // If no new faqs submitted, preserve previous
    if (empty($faqs_array) && isset($faqs) && is_array($faqs))
    {
        $faqs_array = $faqs;
    }
    $settings_code .= "// FAQs\n";
    $settings_code .= "\$faqs = [\n";
    foreach ($faqs_array as $faq)
    {
        $settings_code .= "    [\n        'question' => '" . addslashes($faq['question']) . "',\n        'answer' => '" . addslashes($faq['answer']) . "'\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Services
    // If no new services submitted, preserve previous
    if (empty($services_array) && isset($services) && is_array($services))
    {
        $services_array = $services;
    }
    $settings_code .= "// Services\n";
    $settings_code .= "\$services = [\n";
    foreach ($services_array as $service)
    {
        $settings_code .= "    [\n        'title' => '" . addslashes($service['title']) . "',\n        'desc' => '" . addslashes($service['desc']) . "',\n        'url' => '" . addslashes($service['url'] ?? '') . "'\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Team Members
    $team_array = [];
    if (isset($_POST['team_member_count']) && (int) $_POST['team_member_count'] > 0)
    {
        for ($j = 0; $j < (int) $_POST['team_member_count']; $j++)
        {
            $name = isset($_POST['team_member_name_' . $j]) ? trim($_POST['team_member_name_' . $j]) : '';
            $role = isset($_POST['team_member_role_' . $j]) ? trim($_POST['team_member_role_' . $j]) : '';
            $twitter = isset($_POST['team_member_twitter_' . $j]) ? trim($_POST['team_member_twitter_' . $j]) : '';
            $facebook = isset($_POST['team_member_facebook_' . $j]) ? trim($_POST['team_member_facebook_' . $j]) : '';
            $instagram = isset($_POST['team_member_instagram_' . $j]) ? trim($_POST['team_member_instagram_' . $j]) : '';
            $linkedin = isset($_POST['team_member_linkedin_' . $j]) ? trim($_POST['team_member_linkedin_' . $j]) : '';
            if ($name !== '' || $role !== '')
            {
                $team_array[] = [
                    'name' => $name,
                    'role' => $role,
                    'social' => [
                        'twitter' => $twitter,
                        'facebook' => $facebook,
                        'instagram' => $instagram,
                        'linkedin' => $linkedin
                    ]
                ];
            }
        }
    }
    if (empty($team_array) && isset($team_members) && is_array($team_members))
    {
        $team_array = $team_members;
    }
    $settings_code .= "// Team Members\n";
    $settings_code .= "\$team_members = [\n";
    foreach ($team_array as $member)
    {
        $settings_code .= "    [\n        'name' => '" . addslashes($member['name']) . "',\n        'role' => '" . addslashes($member['role']) . "',\n        'social' => [\n            'twitter' => '" . addslashes($member['social']['twitter']) . "',\n            'facebook' => '" . addslashes($member['social']['facebook']) . "',\n            'instagram' => '" . addslashes($member['social']['instagram']) . "',\n            'linkedin' => '" . addslashes($member['social']['linkedin']) . "'\n        ]\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Portfolio Items
    $portfolio_array = [];
    if (isset($_POST['portfolio_item_count']) && (int) $_POST['portfolio_item_count'] > 0)
    {
        for ($k = 0; $k < (int) $_POST['portfolio_item_count']; $k++)
        {
            $title = isset($_POST['portfolio_item_title_' . $k]) ? trim($_POST['portfolio_item_title_' . $k]) : '';
            $desc = isset($_POST['portfolio_item_desc_' . $k]) ? trim($_POST['portfolio_item_desc_' . $k]) : '';
            if ($title !== '' || $desc !== '')
            {
                $portfolio_array[] = [
                    'title' => $title,
                    'desc' => $desc
                ];
            }
        }
    }
    if (empty($portfolio_array) && isset($portfolio_items) && is_array($portfolio_items))
    {
        $portfolio_array = $portfolio_items;
    }
    $settings_code .= "// Portfolio Items\n";
    $settings_code .= "\$portfolio_items = [\n";
    foreach ($portfolio_array as $item)
    {
        $settings_code .= "    [\n        'title' => '" . addslashes($item['title']) . "',\n        'desc' => '" . addslashes($item['desc']) . "'\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Testimonials
    $testimonial_array = [];
    if (isset($_POST['testimonial_count']) && (int) $_POST['testimonial_count'] > 0)
    {
        for ($t = 0; $t < (int) $_POST['testimonial_count']; $t++)
        {
            $name = isset($_POST['testimonial_name_' . $t]) ? trim($_POST['testimonial_name_' . $t]) : '';
            $role = isset($_POST['testimonial_role_' . $t]) ? trim($_POST['testimonial_role_' . $t]) : '';
            $text = isset($_POST['testimonial_text_' . $t]) ? trim($_POST['testimonial_text_' . $t]) : '';
            if ($name !== '' || $role !== '' || $text !== '')
            {
                $testimonial_array[] = [
                    'name' => $name,
                    'role' => $role,
                    'text' => $text
                ];
            }
        }
    }
    if (empty($testimonial_array) && isset($testimonials) && is_array($testimonials))
    {
        $testimonial_array = $testimonials;
    }
    $settings_code .= "// Testimonials\n";
    $settings_code .= "\$testimonials = [\n";
    foreach ($testimonial_array as $testimonial)
    {
        $settings_code .= "    [\n        'name' => '" . addslashes($testimonial['name']) . "',\n        'role' => '" . addslashes($testimonial['role']) . "',\n        'text' => '" . addslashes($testimonial['text']) . "'\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Stats
    $stats_array = [];
    if (isset($stats) && is_array($stats))
    {
        for ($i = 0; $i < count($stats); $i++)
        {
            $label = isset($_POST['stats_label_' . $i]) ? trim($_POST['stats_label_' . $i]) : '';
            $value = isset($_POST['stats_value_' . $i]) ? trim($_POST['stats_value_' . $i]) : '';
            if ($label !== '' || $value !== '')
            {
                $stats_array[] = [
                    'label' => $label !== '' ? $label : $stats[$i]['label'],
                    'value' => $value !== '' ? $value : $stats[$i]['value']
                ];
            } else
            {
                $stats_array[] = $stats[$i];
            }
        }
    }
    $settings_code .= "// Stats\n";
    $settings_code .= "\$stats = [\n";
    foreach ($stats_array as $stat)
    {
        $settings_code .= "    [\n        'label' => '" . addslashes($stat['label']) . "',\n        'value' => '" . addslashes($stat['value']) . "'\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Pricing Plans
    $pricing_array = [];
    if (isset($_POST['pricing_plan_count']) && (int) $_POST['pricing_plan_count'] > 0)
    {
        for ($p = 0; $p < (int) $_POST['pricing_plan_count']; $p++)
        {
            $name = isset($_POST['pricing_plan_name_' . $p]) ? trim($_POST['pricing_plan_name_' . $p]) : '';
            $price = isset($_POST['pricing_plan_price_' . $p]) ? trim($_POST['pricing_plan_price_' . $p]) : '';
            $button_text = isset($_POST['pricing_plan_button_text_' . $p]) ? trim($_POST['pricing_plan_button_text_' . $p]) : '';
            $button_link = isset($_POST['pricing_plan_button_link_' . $p]) ? trim($_POST['pricing_plan_button_link_' . $p]) : '';
            $features = [];
            $feature_status = [];
            if (isset($pricing_plans[$p]['features']) && is_array($pricing_plans[$p]['features']))
            {
                for ($f = 0; $f < count($pricing_plans[$p]['features']); $f++)
                {
                    $feature = isset($_POST['pricing_plan_' . $p . '_feature_' . $f . '_text']) ? trim($_POST['pricing_plan_' . $p . '_feature_' . $f . '_text']) : '';
                    $status = isset($_POST['pricing_plan_' . $p . '_feature_' . $f . '_status']) ? trim($_POST['pricing_plan_' . $p . '_feature_' . $f . '_status']) : '';
                    $features[] = $feature !== '' ? $feature : $pricing_plans[$p]['features'][$f];
                    $feature_status[] = $status !== '' ? $status : ($pricing_plans[$p]['feature_status'][$f] ?? 'check');
                }
            }
            if ($name !== '' || $price !== '' || $button_text !== '' || $button_link !== '')
            {
                $pricing_array[] = [
                    'name' => $name !== '' ? $name : $pricing_plans[$p]['name'],
                    'price' => $price !== '' ? $price : $pricing_plans[$p]['price'],
                    'button_text' => $button_text !== '' ? $button_text : ($pricing_plans[$p]['button_text'] ?? 'Buy Now'),
                    'button_link' => $button_link !== '' ? $button_link : ($pricing_plans[$p]['button_link'] ?? '#'),
                    'features' => $features,
                    'feature_status' => $feature_status
                ];
            } else
            {
                $pricing_array[] = $pricing_plans[$p];
            }
        }
    }
    if (empty($pricing_array) && isset($pricing_plans) && is_array($pricing_plans))
    {
        $pricing_array = $pricing_plans;
    }
    $settings_code .= "// Pricing Plans\n";
    $settings_code .= "\$pricing_plans = [\n";
    foreach ($pricing_array as $plan)
    {
        $settings_code .= "    [\n        'name' => '" . addslashes($plan['name']) . "',\n        'price' => '" . addslashes($plan['price']) . "',\n        'button_text' => '" . addslashes($plan['button_text']) . "',\n        'button_link' => '" . addslashes($plan['button_link']) . "',\n        'features' => [\n";
        foreach ($plan['features'] as $feature)
        {
            $settings_code .= "            '" . addslashes($feature) . "',\n";
        }
        $settings_code .= "        ],\n        'feature_status' => [\n";
        foreach ($plan['feature_status'] as $status)
        {
            $settings_code .= "            '" . addslashes($status) . "',\n";
        }
        $settings_code .= "        ]\n    ],\n";
    }
    $settings_code .= "];\n\n";

    // Call To Action Section
    $settings_code .= "// Call To Action Section\n";
    $settings_code .= "\$cta_heading = '" . addslashes((isset($new_settings['cta_heading']) && trim($new_settings['cta_heading']) !== '') ? $new_settings['cta_heading'] : ($cta_heading ?? '')) . "';\n";
    $settings_code .= "\$cta_text = '" . addslashes((isset($new_settings['cta_text']) && trim($new_settings['cta_text']) !== '') ? $new_settings['cta_text'] : ($cta_text ?? '')) . "';\n";
    $settings_code .= "\$cta_button_text = '" . addslashes((isset($new_settings['cta_button_text']) && trim($new_settings['cta_button_text']) !== '') ? $new_settings['cta_button_text'] : ($cta_button_text ?? '')) . "';\n";
    $settings_code .= "\$cta_button_link = '" . addslashes((isset($new_settings['cta_button_link']) && trim($new_settings['cta_button_link']) !== '') ? $new_settings['cta_button_link'] : ($cta_button_link ?? '')) . "';\n\n";

    // DISABLED: Automatic file writing to prevent overwrites of manual edits
    // file_put_contents($settings_path, $settings_code);
    
    // Instead, show the generated code for manual application
    $show_generated_code = true;
    $generated_settings_code = $settings_code;
}
?>
<?= template_admin_header('Public Settings', 'settings', 'public') ?>

<!-- Summernote CSS -->
<link rel="stylesheet" href="/assets/summernote/summernote-lite.min.css">

<style>
/* Custom admin styling for better form layout */
.form-help-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
.content-note {
    background: #e7f3ff;
    border: 1px solid #b8daff;
    border-radius: 4px;
    padding: 8px 12px;
    margin-bottom: 15px;
    font-size: 0.875rem;
    color: #004085;
}
</style>


<div class="container">
    <h1 class="mb-4">Public Website Settings</h1>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Settings updated successfully.</div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <!-- Business Info now managed by comprehensive branding system at admin/settings/branding_settings.php -->
        <div class="form-section col-12">
            <h2>Contact Info</h2>
            <label class="form-label">Email</label>
            <input type="text" name="contact_email" class="form-control"
                value="<?php echo htmlspecialchars($contact_email); ?>">
            <label class="form-label mt-2">Phone</label>
            <input type="text" name="contact_phone" class="form-control"
                value="<?php echo htmlspecialchars($contact_phone); ?>">
            <label class="form-label mt-2">Street Address</label>
            <input type="text" name="contact_address" class="form-control"
                value="<?php echo htmlspecialchars($contact_address); ?>">
            <label class="form-label mt-2">City</label>
            <input type="text" name="contact_city" class="form-control"
                value="<?php echo htmlspecialchars($contact_city); ?>">
            <label class="form-label mt-2">State</label>
            <input type="text" name="contact_state" class="form-control"
                value="<?php echo htmlspecialchars($contact_state); ?>">
            <label class="form-label mt-2">Zip Code</label>
            <input type="text" name="contact_zipcode" class="form-control"
                value="<?php echo htmlspecialchars($contact_zipcode); ?>">
        </div>

        <div class="form-section col-12">
            <h2>Footer Special Links</h2>
            <label class="form-label">RSS Feed URL</label>
            <input type="text" name="footer_special_links_rss" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['rss']); ?>">
            <label class="form-label mt-2">XML Sitemap URL</label>
            <input type="text" name="footer_special_links_sitemap" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['sitemap']); ?>">
            <label class="form-label mt-2">Accessibility Policy URL</label>
            <input type="text" name="footer_special_links_accessibility_policy" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['accessibility_policy']); ?>">
            <label class="form-label mt-2">Terms of Service URL</label>
            <input type="text" name="footer_special_links_terms_of_service" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['terms_of_service']); ?>">
            <label class="form-label mt-2">Privacy Policy URL</label>
            <input type="text" name="footer_special_links_privacy_policy" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['privacy_policy']); ?>">
            <label class="form-label mt-2">FAQs URL</label>
            <input type="text" name="footer_special_links_faq" class="form-control" value="<?php echo htmlspecialchars($footer_special_links['faq']); ?>">
        </div>
        <div class="form-section col-12">
            <h2>Header Menu Links</h2>
            <?php $i = 0;
            foreach ($header_menu as $label => $url): ?>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Label</label>
                        <input type="text" name="header_menu_label_<?php echo $i; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($label); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">URL</label>
                        <input type="text" name="header_menu_url_<?php echo $i; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($url); ?>">
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            <input type="hidden" name="header_menu_count" value="<?php echo count($header_menu); ?>">
        </div>
        <div class="form-section col-12">
            <h2>Team Members</h2>
            <?php $j = 0;
            foreach ($team_members as $member): ?>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="team_member_name_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['name']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <input type="text" name="team_member_role_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['role']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Twitter Link</label>
                        <input type="text" name="team_member_twitter_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['social']['twitter']); ?>">
                        <label class="form-label mt-2">Facebook Link</label>
                        <input type="text" name="team_member_facebook_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['social']['facebook']); ?>">
                        <label class="form-label mt-2">Instagram Link</label>
                        <input type="text" name="team_member_instagram_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['social']['instagram']); ?>">
                        <label class="form-label mt-2">LinkedIn Link</label>
                        <input type="text" name="team_member_linkedin_<?php echo $j; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($member['social']['linkedin']); ?>">
                    </div>
                </div>
                <?php $j++; endforeach; ?>
            <input type="hidden" name="team_member_count" value="<?php echo count($team_members); ?>">
        </div>
        <div class="form-section col-12">
            <h2>Portfolio Items</h2>
            <?php $k = 0;
            foreach ($portfolio_items as $item): ?>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" name="portfolio_item_title_<?php echo $k; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="portfolio_item_desc_<?php echo $k; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($item['desc']); ?>">
                    </div>
                </div>
                <?php $k++; endforeach; ?>
            <input type="hidden" name="portfolio_item_count" value="<?php echo count($portfolio_items); ?>">
        </div>
        <div class="form-section col-12">
            <h2>Services</h2>
            <?php $s = 0;
            foreach ($services as $service): ?>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="service_title_<?php echo $s; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($service['title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <input type="text" name="service_desc_<?php echo $s; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($service['desc'] ?? ''); ?>">
                    </div>
                </div>
                <?php $s++; endforeach; ?>
            <input type="hidden" name="service_count" value="<?php echo count($services); ?>">
        </div>
        <div class="form-section col-12">
            <h2>Testimonials</h2>
            <?php $t = 0;
            foreach ($testimonials as $testimonial): ?>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="testimonial_name_<?php echo $t; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($testimonial['name']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role/Title</label>
                        <input type="text" name="testimonial_role_<?php echo $t; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($testimonial['role']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quote</label>
                        <input type="text" name="testimonial_text_<?php echo $t; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($testimonial['text']); ?>">
                    </div>
                </div>
                <?php $t++; endforeach; ?>
            <input type="hidden" name="testimonial_count" value="<?php echo count($testimonials); ?>">
        </div>
        <div class="form-section col-12">
            <h2>FAQs</h2>
            <?php $f = 0;
            foreach ($faqs as $faq): ?>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Question</label>
                        <input type="text" name="faq_question_<?php echo $f; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($faq['question']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Answer</label>
                        <input type="text" name="faq_answer_<?php echo $f; ?>" class="form-control"
                            value="<?php echo htmlspecialchars($faq['answer']); ?>">
                    </div>
                </div>
                <?php $f++; endforeach; ?>
            <input type="hidden" name="faq_count" value="<?php echo count($faqs); ?>">
            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text" name="faq_question_<?php echo $f; ?>" class="form-control"
                        placeholder="Add new question">
                </div>
                <div class="col-md-6">
                    <input type="text" name="faq_answer_<?php echo $f; ?>" class="form-control"
                        placeholder="Add new answer">
                </div>
            </div>
        </div>
        <div class="form-section col-12">
            <h2>Hero Section</h2>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Hero Headline</label>
                    <input type="text" name="hero_headline" class="form-control"
                        value="<?php echo isset($hero_content['headline']) ? htmlspecialchars($hero_content['headline']) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hero Subheadline</label>
                    <input type="text" name="hero_subheadline" class="form-control"
                        value="<?php echo isset($hero_content['subheadline']) ? htmlspecialchars($hero_content['subheadline']) : ''; ?>">
                </div>
            </div>
            <!-- Hero background image is now managed in the public image settings admin page. -->
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Hero Button Text <span style="color:blue;">*</span></label>
                    <input type="text" name="hero_button_text" class="form-control"
                        value="<?php echo htmlspecialchars($hero_button_text ?? 'Get Started'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hero Button Link</label>
                    <input type="text" name="hero_button_link" class="form-control"
                        value="<?php echo htmlspecialchars($hero_button_link ?? 'about.php'); ?>">
                </div>
            </div>
        </div>

        <div class="form-section col-12">
            <h2>Section Titles & Content</h2>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Services Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="services_title" class="form-control"
                        value="<?php echo htmlspecialchars($services_title ?? 'Services'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Services Section Paragraph <span style="color:blue;">*</span></label>
                    <textarea name="services_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($services_paragraph ?? 'Explore our creative and practical services designed to help your business thrive.'); ?></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Contact Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_title" class="form-control"
                        value="<?php echo htmlspecialchars($contact_title ?? 'Contact'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Section Paragraph</label>
                    <textarea name="contact_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($contact_paragraph ?? 'We look forward to serving you!'); ?></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Portfolio Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="portfolio_title" class="form-control"
                        value="<?php echo htmlspecialchars($portfolio_title ?? 'Portfolio'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Portfolio Section Paragraph <span style="color:blue;">*</span></label>
                    <textarea name="portfolio_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($portfolio_paragraph ?? 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.'); ?></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Pricing Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="pricing_title" class="form-control"
                        value="<?php echo htmlspecialchars($pricing_title ?? 'Pricing'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pricing Section Paragraph <span style="color:blue;">*</span></label>
                    <textarea name="pricing_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($pricing_paragraph ?? 'Choose the plan that fits your needs. All plans are fully customizable.'); ?></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Team Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="team_title" class="form-control"
                        value="<?php echo htmlspecialchars($team_title ?? 'Team'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Section Paragraph <span style="color:blue;">*</span></label>
                    <textarea name="team_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($team_paragraph ?? 'Meet our talented team of professionals dedicated to your success.'); ?></textarea>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Testimonials Section Title <span style="color:blue;">*</span></label>
                    <input type="text" name="testimonials_title" class="form-control"
                        value="<?php echo htmlspecialchars($testimonials_title ?? 'Testimonials'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Testimonials Section Paragraph <span style="color:blue;">*</span></label>
                    <textarea name="testimonials_paragraph" class="form-control summernote" style="height:100px;"><?php echo htmlspecialchars($testimonials_paragraph ?? 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit'); ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-section col-12">
            <h2>Contact Form Labels</h2>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Name Field Label <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_name" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['name'] ?? 'Your Name'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Field Label <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_email" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['email'] ?? 'Your Email'); ?>">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Subject Field Label <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_subject" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['subject'] ?? 'Subject'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Message Field Label <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_message" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['message'] ?? 'Message'); ?>">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <label class="form-label">Send Button Text <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_send_button" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['send_button'] ?? 'Send Message'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Loading Message <span style="color:blue;">*</span></label>
                    <input type="text" name="contact_form_loading" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['loading'] ?? 'Loading'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Success Message</label>
                    <input type="text" name="contact_form_success" class="form-control"
                        value="<?php echo htmlspecialchars($contact_form_labels['success'] ?? 'Your message has been sent. Thank you!'); ?>">
                </div>
            </div>
        </div>

        <div class="form-section col-12">
            <h2>Portfolio Filter Labels</h2>
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="form-label">Filter 1 (All) <span style="color:blue;">*</span></label>
                    <input type="text" name="portfolio_filter_0" class="form-control"
                        value="<?php echo htmlspecialchars($portfolio_filters[0] ?? 'All'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter 2 (Apps) <span style="color:blue;">*</span></label>
                    <input type="text" name="portfolio_filter_1" class="form-control"
                        value="<?php echo htmlspecialchars($portfolio_filters[1] ?? 'Apps'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter 3 (Products) <span style="color:blue;">*</span></label>
                    <input type="text" name="portfolio_filter_2" class="form-control"
                        value="<?php echo htmlspecialchars($portfolio_filters[2] ?? 'Products'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter 4 (Websites) <span style="color:blue;">*</span></label>
                    <input type="text" name="portfolio_filter_3" class="form-control"
                        value="<?php echo htmlspecialchars($portfolio_filters[3] ?? 'Websites'); ?>">
                </div>
            </div>
        </div>
        <div class="form-section col-12">
            <h3>About Section</h3>
            <div class="content-note">
                <strong>Content Guidelines:</strong>
                <ul class="mb-0">
                    <li><strong>Main Paragraph:</strong> Keep to one line for best visual impact (recommended: 100-150 characters)</li>
                    <li><strong>Side Text:</strong> This appears next to the image and can be longer with formatting</li>
                </ul>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <label for="about_title">Section Title</label>
                    <input type="text" name="about_title" id="about_title" class="form-control"
                        value="<?php echo htmlspecialchars($about_content['title'] ?? 'About Us'); ?>">
                </div>
                <div class="col-md-8">
                    <label for="about_text">Main Paragraph <span class="text-muted">(Keep brief - one line recommended)</span></label>
                    <textarea name="about_text" id="about_text" class="form-control"
                        style="height:60px;" maxlength="200"><?php echo htmlspecialchars($about_content['text'] ?? ''); ?></textarea>
                    <div class="form-help-text">Character count: <span id="about_text_count">0</span>/200</div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label for="about_side_text">Side Content <span class="text-muted">(Appears next to image - can include formatting)</span></label>
                    <textarea name="about_side_text" id="about_side_text" class="form-control summernote"
                        style="height:120px;"><?php echo htmlspecialchars($about_content['side_text'] ?? ''); ?></textarea>
                    <div class="form-help-text">Use the rich text editor to add formatting like <strong>bold</strong>, <em>italic</em>, lists, etc.</div>
                </div>
            </div>
        </div>

            <div class="form-section col-12">
                <h3>Stats Section</h3>
                <div class="row mb-2">
                    <?php for ($i = 0; $i < count($stats); $i++): ?>
                        <div class="col-md-6">
                            <label for="stats_label_<?php echo $i; ?>">Label <?php echo $i + 1; ?></label>
                            <input type="text" name="stats_label_<?php echo $i; ?>" id="stats_label_<?php echo $i; ?>"
                                class="form-control" value="<?php echo htmlspecialchars($stats[$i]['label']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="stats_value_<?php echo $i; ?>">Value <?php echo $i + 1; ?></label>
                            <input type="number" name="stats_value_<?php echo $i; ?>" id="stats_value_<?php echo $i; ?>"
                                class="form-control" value="<?php echo htmlspecialchars($stats[$i]['value']); ?>">
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="form-section col-12">
                    <h2>Footer Links & Social</h2>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Accessibility Policy URL</label>
                            <input type="text" name="footer_accessibility_policy" class="form-control"
                                value="<?php echo isset($footer_links['accessibility_policy']) ? htmlspecialchars($footer_links['accessibility_policy']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Terms of Service URL</label>
                            <input type="text" name="footer_terms_of_service" class="form-control"
                                value="<?php echo isset($footer_links['terms_of_service']) ? htmlspecialchars($footer_links['terms_of_service']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Privacy Policy URL</label>
                            <input type="text" name="footer_privacy_policy" class="form-control"
                                value="<?php echo isset($footer_links['privacy_policy']) ? htmlspecialchars($footer_links['privacy_policy']) : ''; ?>">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">FAQ Page URL</label>
                            <input type="text" name="footer_faq" class="form-control"
                                value="<?php echo isset($footer_links['faq']) ? htmlspecialchars($footer_links['faq']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contact Webmaster Email</label>
                            <input type="text" name="footer_webmaster_email" class="form-control"
                                value="<?php echo isset($footer_links['webmaster_email']) ? htmlspecialchars($footer_links['webmaster_email']) : ''; ?>">
                        </div>
                    </div>
                    <h3 class="mt-3">Social Links</h3>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="social_facebook" class="form-control"
                                value="<?php echo isset($social_links['facebook']) ? htmlspecialchars($social_links['facebook']) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Twitter</label>
                            <input type="text" name="social_twitter" class="form-control"
                                value="<?php echo isset($social_links['twitter']) ? htmlspecialchars($social_links['twitter']) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">LinkedIn</label>
                            <input type="text" name="social_linkedin" class="form-control"
                                value="<?php echo isset($social_links['linkedin']) ? htmlspecialchars($social_links['linkedin']) : ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" name="social_instagram" class="form-control"
                                value="<?php echo isset($social_links['instagram']) ? htmlspecialchars($social_links['instagram']) : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="form-section col-12">
                    <h2>Call To Action Section</h2>
                    <label class="form-label">Heading</label>
                    <input type="text" name="cta_heading" class="form-control"
                        value="<?php echo htmlspecialchars($cta_heading); ?>">
                    <label class="form-label mt-2">Text</label>
                    <input type="text" name="cta_text" class="form-control"
                        value="<?php echo htmlspecialchars($cta_text); ?>">
                    <label class="form-label mt-2">Button Text</label>
                    <input type="text" name="cta_button_text" class="form-control"
                        value="<?php echo htmlspecialchars($cta_button_text); ?>">
                    <label class="form-label mt-2">Button Link</label>
                    <input type="text" name="cta_button_link" class="form-control"
                        value="<?php echo htmlspecialchars($cta_button_link); ?>">
                </div>
                <div class="form-section col-12">
                    <h2>Pricing Plans</h2>
                    <?php $p = 0;
                    foreach ($pricing_plans as $plan): ?>
                        <div class="row mb-2 border rounded p-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Plan Name</label>
                                <input type="text" name="pricing_plan_name_<?php echo $p; ?>" class="form-control"
                                    value="<?php echo htmlspecialchars($plan['name']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price</label>
                                <input type="text" name="pricing_plan_price_<?php echo $p; ?>" class="form-control"
                                    value="<?php echo htmlspecialchars($plan['price']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="pricing_plan_button_text_<?php echo $p; ?>" class="form-control"
                                    value="<?php echo isset($plan['button_text']) ? htmlspecialchars($plan['button_text']) : 'Buy Now'; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Button Link</label>
                                <input type="text" name="pricing_plan_button_link_<?php echo $p; ?>" class="form-control"
                                    value="<?php echo isset($plan['button_link']) ? htmlspecialchars($plan['button_link']) : '#'; ?>">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12">
                                <label class="form-label">Features</label>
                                <?php foreach ($plan['features'] as $f => $feature): ?>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text">
                                            <select name="pricing_plan_<?php echo $p; ?>_feature_<?php echo $f; ?>_status"
                                                class="form-select" style="width:70px;">
                                                <option value="check" <?php echo (isset($plan['feature_status'][$f]) && $plan['feature_status'][$f] == 'check') ? 'selected' : ''; ?>>✔️</option>
                                                <option value="x" <?php echo (isset($plan['feature_status'][$f]) && $plan['feature_status'][$f] == 'x') ? 'selected' : ''; ?>>❌</option>
                                            </select>
                                        </span>
                                        <input type="text" name="pricing_plan_<?php echo $p; ?>_feature_<?php echo $f; ?>_text"
                                            class="form-control" value="<?php echo htmlspecialchars($feature); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php $p++; endforeach; ?>
                    <input type="hidden" name="pricing_plan_count" value="<?php echo count($pricing_plans); ?>">
                </div>
                <div class="col-12">
                    <div class="content-header form-actions-header">
                        <div class="form-actions">
                            <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success" aria-label="Update settings">
                                <i class="fas fa-save me-2"></i>Update Settings
                            </button>
                        </div>
                    </div>
                </div>
    </form>
    
    <?php if (isset($show_generated_code) && $show_generated_code): ?>
    <div class="container mt-5">
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Automatic File Writing Disabled</h4>
            <p>To prevent overwriting your manual edits, this form no longer automatically updates the settings file. 
            Below is the generated PHP code that would have been written. You can manually copy and apply the changes you want:</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Generated Settings Code (Copy what you need)</h5>
                <small class="text-muted">File: <?php echo htmlspecialchars($settings_path); ?></small>
            </div>
            <div class="card-body">
                <textarea class="form-control" rows="15" readonly style="font-family: monospace; font-size: 12px;"><?php echo htmlspecialchars($generated_settings_code); ?></textarea>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-sm" onclick="navigator.clipboard.writeText(document.querySelector('textarea').value)">
                    <i class="fas fa-copy"></i> Copy to Clipboard
                </button>
                <a href="<?php echo htmlspecialchars($settings_path); ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open Settings File
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Summernote JS -->
<script src="/assets/summernote/summernote-lite.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote for rich text fields
    $('.summernote').summernote({
        height: 120,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Character counter for about_text
    function updateCharCount() {
        const text = $('#about_text').val();
        const count = text.length;
        $('#about_text_count').text(count);
        
        // Change color based on length
        if (count > 150) {
            $('#about_text_count').css('color', '#dc3545'); // Red
        } else if (count > 100) {
            $('#about_text_count').css('color', '#fd7e14'); // Orange
        } else {
            $('#about_text_count').css('color', '#28a745'); // Green
        }
    }
    
    // Update counter on page load and when typing
    updateCharCount();
    $('#about_text').on('input', updateCharCount);
});
</script>

<?php echo template_admin_footer(); ?>