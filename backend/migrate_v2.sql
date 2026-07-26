-- Auto Pride London — v2 Migration
-- Run this in IONOS phpMyAdmin. Safe to run multiple times (IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    review_text TEXT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    photo VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS chatbot_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    keywords VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) DEFAULT 'General',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS feature_toggles (
    feature_key VARCHAR(100) NOT NULL PRIMARY KEY,
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default settings (INSERT IGNORE = skip if key already exists)
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('business_name', 'Auto Pride London'),
('tagline', 'Premium Refurbished Cars & Accessories'),
('phone', '+44 7771 406382'),
('email', 'sales@autoprideldn.co.uk'),
('address', 'Sunbury-on-Thames'),
('hero_title', 'Auto Pride London'),
('hero_subtitle', 'Find your perfect refurbished car or upgrade with our premium accessories'),
('hero_background_image', ''),
('hero_background_video', ''),
('stats_cars_sold', '150'),
('stats_happy_customers', '120'),
('stats_years_in_business', '5'),
('whatsapp_phone', '447771406382'),
('whatsapp_message', 'Hi, I am interested in a car from Auto Pride London'),
('whatsapp_enabled', '1'),
('chatbot_enabled', '1'),
('chatbot_greeting', 'Hi! How can I help you today? Ask me about our cars, services, or location.');

-- Seed default feature toggles
INSERT IGNORE INTO feature_toggles (feature_key, is_enabled) VALUES
('hero_section', 1),
('stats_section', 1),
('testimonials_section', 1),
('whatsapp_button', 1),
('chatbot_widget', 1),
('products_page', 1),
('inquiry_form', 1);

-- Seed a few starter FAQ entries for the chatbot
INSERT IGNORE INTO chatbot_faqs (id, question, answer, keywords, category, sort_order) VALUES
(1, 'What cars do you have available?', 'We have a range of quality refurbished cars available. Please visit our Cars page to see the full inventory, or contact us for specific requirements.', 'cars,available,stock,inventory,vehicles,what,have', 'Cars', 1),
(2, 'Where are you located?', 'We are based in Sunbury-on-Thames, easily accessible from London and surrounding areas. Contact us for exact directions.', 'where,location,located,address,directions,find,visit', 'Location', 2),
(3, 'Do you offer finance?', 'Please contact us directly to discuss financing options for your chosen vehicle. We will do our best to help you find a suitable arrangement.', 'finance,financing,payment,monthly,installment,loan,credit', 'Finance', 3),
(4, 'How can I contact you?', 'You can reach us by phone at +44 7771 406382, email at sales@autoprideldn.co.uk, or use the enquiry form on our website. We also respond on WhatsApp!', 'contact,phone,call,email,reach,touch,whatsapp', 'Contact', 4),
(5, 'Do you deliver cars?', 'Delivery options may be available depending on your location. Please get in touch to discuss delivery arrangements for your vehicle.', 'deliver,delivery,transport,ship,shipping,bring,collect', 'Delivery', 5);
