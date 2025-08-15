<?php
/*
Page: public_settings.php
Location: public_html/assets/includes/settings/public_settings.php
Purpose: Centralized public-facing settings for business branding, content, and SEO. Edit this file to customize the entire public website for a new business/client.
*/


// Header Menu (for header.php navigation)
$header_menu = [
    'About' => 'about.php',
    'Services' => 'index.php#services',
    'Contact' => 'index.php#contact'
];

// Business Info
$business_name = 'GWS Universal Hybrid Application';
$business_logo = 'assets/img/logo.png';
$favicon = 'assets/img/favicon.png';
$apple_touch_icon = 'assets/img/apple-touch-icon.png';
$author = 'GWS';
// Branding
$brand_primary_color = '#6c2eb6';
$brand_secondary_color = '#bf5512';
$brand_font_family = 'Roboto, Poppins, Raleway, Arial, sans-serif';

// Contact Section
$contact_email = 'info@gws.com';
$contact_phone = '+1 555-123-4567';
$contact_address = '123 Main Street';
$contact_city = 'Crawfordville,';
$contact_state = 'FL';
$contact_zipcode = '32327';

// Missing variables that need to be added:
$services_title = 'Services';
$services_paragraph = 'Explore our creative and practical services designed to help your business thrive.';
$hero_button_text = 'Get Started';
$hero_button_link = 'about.php';
$contact_title = 'Contact';
$contact_paragraph = 'We look forward to serving you!';
$contact_form_labels = [
    'name' => 'Your Name',
    'email' => 'Your Email', 
    'subject' => 'Subject',
    'message' => 'Message',
    'send_button' => 'Send Message',
    'loading' => 'Loading',
    'success' => 'Your message has been sent. Thank you!'
];
$portfolio_title = 'Portfolio';
$portfolio_paragraph = 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.';
$portfolio_filters = ['All', 'Apps', 'Products', 'Websites'];
$pricing_title = 'Pricing';
$pricing_paragraph = 'Choose the plan that fits your needs. All plans are fully customizable.';
$team_title = 'Team';
$team_paragraph = 'Meet our talented team of professionals dedicated to your success.';
$testimonials_title = 'Testimonials';
$testimonials_paragraph = 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit';

// Hero Section
$hero_content = [
    'headline' => 'Welcome to GWS Universal Hybrid Application',
    'subheadline' => 'A modern, flexible template for any business.',
    'bg_image' => ''
];

// About Alt Section (customizable content)
$about_alt_heading = 'Discover Our Unique Approach';
$about_alt_italic = 'We combine creativity, technology, and strategy to deliver outstanding results for your business.';
$about_alt_list = [
    'Custom solutions tailored to your brand and goals.',
    'Expert team with years of experience in web development and design.',
    'Ongoing support and collaboration to ensure your success.'
];
$about_alt_paragraph = 'Let us help you build a strong online presence and achieve your business objectives. Contact us today to get started!';

// About Section
$stats = [
    ['label' => 'Projects', 'value' => 120],
    ['label' => 'Clients', 'value' => 85],
    ['label' => 'Awards', 'value' => 15],
    ['label' => 'Team Members', 'value' => 10]
];
$about_content = [
    'title' => 'About Us',
    'text' => 'We are a passionate team dedicated to building modern, accessible, and high-performing websites that help businesses of all sizes succeed online.',
    'side_text' => 'Our team brings together years of experience in web development, design, and digital strategy. We focus on accessibility, performance, and user experience to help your business thrive online.'
];

// Footer Links

$footer_links = [
    'Reviews' => 'public_reviews.php',
    'FAQs' => 'faqs.php',
];

// Footer Special Links (RSS, Sitemap, Accessibility Policy)
$footer_special_links = [
    'rss' => 'rss',
    'sitemap' => 'sitemap',
    'accessibility_policy' => 'policy-accessibility.php',
    'terms_of_service' => 'policy-terms.php',
    'privacy_policy' => 'policy-privacy.php',
    'faq' => 'faqs.php'
];

// Social Links
$social_links = [
    'facebook' => 'https://www.facebook.com/GlitchwizardSolutions',
    'twitter' => '#',
    'linkedin' => 'https://www.linkedin.com/in/glitchwizard',
    'instagram' => '#'
];

// Services
$services = [
    [
        'title' => 'Web Development',
        'desc' => 'Custom websites and web applications built for your business.',
        'url' => ''
    ],
    [
        'title' => 'UI/UX Design',
        'desc' => 'Modern, accessible, and beautiful user experiences.',
        'url' => ''
    ],
    [
        'title' => 'SEO & Marketing',
        'desc' => 'Grow your audience and reach with proven strategies.',
        'url' => ''
    ],
     
    [
        'title' => 'Hosting & Security',
        'desc' => 'Reliable hosting and robust security solutions for peace of mind.',
        'url' => ''
    ]  
    
];

// FAQs
$faqs = [
    [
        'question' => 'What is a hybrid app?',
        'answer' => 'A hybrid app combines the best of web and native technologies.'
    ],
    [
        'question' => 'How do I get started?',
        'answer' => 'Contact us for a free consultation.'
    ],
    [
        'question' => 'What services do you offer?',
        'answer' => 'We offer a wide range of web development, design, and digital marketing services.'
    ],
    [
        'question' => 'How can I contact your support team?',
        'answer' => 'You can reach our support team via email, phone, or the contact form on our website.'
    ],
    [
        'question' => 'Do you provide custom website solutions?',
        'answer' => 'Yes, we specialize in custom website solutions tailored to your business needs.'
    ],
    [
        'question' => 'What is your typical project turnaround time?',
        'answer' => 'Project timelines vary, but most websites are completed within 2-6 weeks.'
    ],
    [
        'question' => 'Can you help with SEO and online marketing?',
        'answer' => 'Absolutely! We offer SEO optimization and digital marketing services.'
    ],
    [
        'question' => 'Do you offer website maintenance?',
        'answer' => 'Yes, we provide ongoing website maintenance and support packages.'
    ],
    [
        'question' => 'Is my website mobile-friendly?',
        'answer' => 'All our websites are designed to be fully responsive and mobile-friendly.'
    ],
    [
        'question' => 'Can I update my website content myself?',
        'answer' => 'We build sites with easy-to-use admin panels so you can update content anytime.'
    ],
    [
        'question' => 'Do you offer e-commerce solutions?',
        'answer' => 'Yes, we can build secure and scalable e-commerce platforms for your business.'
    ],
    [
        'question' => 'How do I get started with a new project?',
        'answer' => 'Contact us for a free consultation and we’ll guide you through the next steps.'
    ],
];

// Testimonials
$testimonials = [
    [
        'name' => 'Jane Smith',
        'role' => 'CEO, ExampleCorp',
        'text' => 'This platform transformed our business. Highly recommended!'
    ],
    [
        'name' => 'John Doe',
        'role' => 'Lead Developer, DevWorks',
        'text' => 'The flexibility and customization options are unmatched.'
    ],
    [
        'name' => 'Emily Chen',
        'role' => 'Marketing Director, MarketPro',
        'text' => 'Our team loves the intuitive admin panel and robust features.'
    ],
    [
        'name' => 'Carlos Ruiz',
        'role' => 'Designer, Creativa',
        'text' => 'Beautiful design and easy to update content. Perfect for agencies.'
    ],
    [
        'name' => 'Priya Patel',
        'role' => 'Operations Manager, BizOps',
        'text' => 'Reliable, secure, and accessible. Our clients are impressed.'
    ],
    [
        'name' => 'Michael Lee',
        'role' => 'Consultant, Lee Consulting',
        'text' => 'Setup was a breeze and support is excellent. Five stars!'
    ]
];

// Team Members
$team_members = [
    [
        'name' => 'Jane Smith',
        'role' => 'CEO',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'John Doe',
        'role' => 'Lead Developer',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'Emily Chen',
        'role' => 'Marketing Director',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ],
    [
        'name' => 'Carlos Ruiz',
        'role' => 'Designer',
        'social' => [
            'twitter' => '#',
            'facebook' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ]
    ]
];

// Pricing Plans
$pricing_plans = [
    [
        'name' => 'Basic',
        'price' => '$99',
        'button_text' => 'Buy Now',
        'button_link' => '#',
        'features' => [
            '1 Website',
            'Basic Support',
            'SSL Certificate'
        ],
        'feature_status' => ['check', 'check', 'check']
    ],
    [
        'name' => 'Pro',
        'price' => '$199',
        'button_text' => 'Buy Now',
        'button_link' => '#',
        'features' => [
            '3 Websites',
            'Priority Support',
            'SSL Certificate',
            'SEO Optimization'
        ],
        'feature_status' => ['check', 'check', 'check', 'check']
    ],
    [
        'name' => 'Enterprise',
        'price' => 'Contact Us',
        'button_text' => 'Contact Sales',
        'button_link' => '#',
        'features' => [
            'Unlimited Websites',
            'Dedicated Support',
            'SSL Certificate',
            'SEO Optimization',
            'Custom Integrations'
        ],
        'feature_status' => ['check', 'check', 'check', 'check', 'check']
    ]
];

// Portfolio Items
$portfolio_items = [
    [
        'title' => 'Mobile App Design',
        'desc' => 'Modern, user-friendly mobile application for business productivity.'
    ],
    [
        'title' => 'Product Launch Campaign',
        'desc' => 'Comprehensive marketing campaign for a new product release.'
    ],
    [
        'title' => 'E-commerce Platform',
        'desc' => 'Full-featured online store with payment integration.'
    ],
    [
        'title' => 'Brand Identity Design',
        'desc' => 'Complete brand identity package including logo and guidelines.'
    ],
    [
        'title' => 'Web Development',
        'desc' => 'Custom website development with modern frameworks.'
    ],
    [
        'title' => 'Digital Marketing',
        'desc' => 'Strategic digital marketing campaigns for growth.'
    ]
];

