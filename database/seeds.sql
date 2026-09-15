-- ============================================================
-- SIDRA EVENT TICKETING PLATFORM - SEED DATA
-- ============================================================

-- 1. Insert Roles
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES
(1, 'super_admin', 'Super Administrator', 'Full system access and security administration'),
(2, 'admin', 'Administrator', 'General administration for events, bookings, and users'),
(3, 'event_manager', 'Event Manager', 'Create and manage events, venues, and ticket types'),
(4, 'finance_manager', 'Finance Manager', 'Review and approve/reject manual payments'),
(5, 'gate_staff', 'Gate Staff', 'Gate access for verifying and checking in tickets via QR code');

-- 2. Insert Permissions
INSERT INTO `permissions` (`id`, `name`, `display_name`, `module`) VALUES
(1, 'events.view', 'View Events', 'events'),
(2, 'events.create', 'Create Events', 'events'),
(3, 'events.edit', 'Edit Events', 'events'),
(4, 'events.delete', 'Delete Events', 'events'),
(5, 'events.publish', 'Publish Events', 'events'),
(6, 'tickets.manage', 'Manage Ticket Inventory', 'tickets'),
(7, 'bookings.view', 'View Bookings', 'bookings'),
(8, 'bookings.manage', 'Manage Bookings', 'bookings'),
(9, 'payments.view', 'View Payments', 'payments'),
(10, 'payments.approve', 'Approve / Reject Payments', 'payments'),
(11, 'tickets.verify', 'Verify & Scan Tickets', 'verification'),
(12, 'customers.view', 'View Customers', 'customers'),
(13, 'users.manage', 'Manage Admin Users & Roles', 'users'),
(14, 'reports.view', 'View Reports & Analytics', 'reports'),
(15, 'settings.manage', 'Manage System Settings', 'settings'),
(16, 'audit_logs.view', 'View Audit Logs', 'audit_logs');

-- 3. Map Role Permissions
-- Super Admin: All permissions (1 to 16)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16),
-- Admin: Permissions 1-12, 14, 16
(2, 1), (2, 2), (2, 3), (2, 5), (2, 6), (2, 7), (2, 8), (2, 9), (2, 10), (2, 11), (2, 12), (2, 14), (2, 16),
-- Event Manager: 1-6, 7, 11, 14
(3, 1), (3, 2), (3, 3), (3, 5), (3, 6), (3, 7), (3, 11), (3, 14),
-- Finance Manager: 7, 9, 10, 14
(4, 7), (4, 9), (4, 10), (4, 14),
-- Gate Staff: 11
(5, 11);

-- 4. Insert Default Staff Users (Password: Password123!)
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `password_hash`, `status`) VALUES
(1, 1, 'Sidra Super Admin', 'admin@sidra.test', '+8801711000001', '$2y$12$ER0LNk3.L55tTViGlB3Rs.5XVzUZz.hhNuCuop1w0iWMDy8PivGpe', 'active'),
(2, 3, 'Tariq Event Lead', 'manager@sidra.test', '+8801711000002', '$2y$12$ER0LNk3.L55tTViGlB3Rs.5XVzUZz.hhNuCuop1w0iWMDy8PivGpe', 'active'),
(3, 4, 'Nusrat Finance Lead', 'finance@sidra.test', '+8801711000003', '$2y$12$ER0LNk3.L55tTViGlB3Rs.5XVzUZz.hhNuCuop1w0iWMDy8PivGpe', 'active'),
(4, 5, 'Kamal Gate Marshal', 'gate@sidra.test', '+8801711000004', '$2y$12$ER0LNk3.L55tTViGlB3Rs.5XVzUZz.hhNuCuop1w0iWMDy8PivGpe', 'active');

-- 5. Insert Demo Customer (Password: Password123!)
INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `password_hash`, `status`) VALUES
(1, 'Tanvir Ahmed', 'customer@sidra.test', '+8801819000001', '$2y$12$ER0LNk3.L55tTViGlB3Rs.5XVzUZz.hhNuCuop1w0iWMDy8PivGpe', 'active');

-- 6. Insert Event Categories
INSERT INTO `event_categories` (`id`, `name`, `slug`, `description`, `icon`, `is_active`) VALUES
(1, 'Technology & AI', 'technology-ai', 'Developer summits, AI showcases, and cutting-edge tech conferences', 'cpu', 1),
(2, 'Concerts & Music', 'concerts-music', 'Live music festivals, acoustic concerts, and orchestral performances', 'music', 1),
(3, 'Business & Leadership', 'business-leadership', 'Executive summits, startup masterclasses, and networking forums', 'briefcase', 1),
(4, 'Arts & Culture', 'arts-culture', 'Art exhibitions, theatrical plays, and cultural galas', 'palette', 1),
(5, 'Sports & Fitness', 'sports-fitness', 'Marathons, tournaments, and wellness festivals', 'trophy', 1);

-- 7. Insert Venues
INSERT INTO `venues` (`id`, `name`, `slug`, `address`, `city`, `capacity`, `map_url`, `contact_phone`) VALUES
(1, 'Bangabandhu International Conference Center (BICC)', 'bicc-dhaka', 'Agargaon, Sher-e-Bangla Nagar', 'Dhaka', 5000, 'https://maps.google.com/?q=BICC+Dhaka', '+88028181678'),
(2, 'Army Stadium', 'army-stadium-dhaka', 'Airport Road, Cantonment', 'Dhaka', 25000, 'https://maps.google.com/?q=Army+Stadium+Dhaka', '+8801700000111'),
(3, 'Radisson Blu Water Garden Grand Ballroom', 'radisson-blu-dhaka', 'Airport Road', 'Dhaka', 1200, 'https://maps.google.com/?q=Radisson+Blu+Dhaka', '+88029834555'),
(4, 'International Convention City Bashundhara (ICCB)', 'iccb-dhaka', 'Kuril Bishwa Road, Next to 300 Feet Purbachal Express', 'Dhaka', 15000, 'https://maps.google.com/?q=ICCB+Dhaka', '+88028401991');

-- 8. Insert Rich Sample Events
INSERT INTO `events` (`id`, `category_id`, `venue_id`, `created_by`, `title`, `slug`, `summary`, `description`, `banner_image`, `event_date`, `start_time`, `end_time`, `status`, `is_featured`) VALUES
(1, 1, 1, 1, 'Sidra Tech & AI Summit 2026', 'sidra-tech-ai-summit-2026', 'Bangladesh''s premier technology and artificial intelligence symposium featuring global tech visionaries and startup innovators.', 'Join 2,500+ engineers, product architects, data scientists, and venture leaders for the landmark tech gathering of 2026. Explore hands-on keynotes on Foundation Models, Cloud Native scale, Cyber Resilience, and high-frequency fintech systems.\n\nHighlights:\n- 4 High-Impact Keynote Sessions\n- 24 Technical Masterclasses\n- Startup Pitching Stage with $100K Angel Pool\n- VIP Executive Networking Dinner\n- Full Access to Exhibition Floors and Swag Bag', '/assets/images/event_tech.jpg', '2026-10-15', '09:00:00', '18:30:00', 'published', 1),
(2, 2, 4, 1, 'Dhaka Symphony & Acoustic Echoes 2026', 'dhaka-symphony-acoustic-echoes-2026', 'An enchanting evening of fusion music, folk melodies, and modern orchestral symphony under the stars.', 'Experience an extraordinary live sensory spectacle as top musical maestros and legendary vocalists unite for a breathtaking acoustic concert at ICCB Hall 4. State-of-the-art immersive acoustics, laser light artistry, and designated VIP seating lounges.\n\nHighlights:\n- 6 Full Performance Sets\n- Artisan Food & Beverage Stalls\n- Exclusive Merchandise Booth\n- Secure Dedicated Parking', '/assets/images/event_music.jpg', '2026-10-28', '17:00:00', '23:00:00', 'published', 1),
(3, 3, 3, 1, 'Future Leaders & Venture Forum', 'future-leaders-venture-forum', 'High-level executive roundtable, growth strategy keynote, and private investor syndicate mixer.', 'A curated gathering for C-Suite executives, founders, and private equity partners navigating global market expansion, governance, and operational resilience.', '/assets/images/event_business.jpg', '2026-11-12', '10:00:00', '16:00:00', 'published', 1);

-- 9. Insert Ticket Types (Inventory)
INSERT INTO `ticket_types` (`id`, `event_id`, `name`, `description`, `price`, `total_quantity`, `remaining_quantity`, `max_per_user`, `sales_start`, `sales_end`, `status`) VALUES
-- Event 1: Tech Summit
(1, 1, 'Standard Pass', 'Full access to general conference hall, expo floor, lunch buffet, and official summit kit.', 1500.00, 1000, 995, 5, '2026-08-01 00:00:00', '2026-10-14 23:59:59', 'active'),
(2, 1, 'VIP Delegate', 'Priority front-row seating, speakers lounge access, exclusive VIP dinner, and networking pass.', 4500.00, 200, 198, 4, '2026-08-01 00:00:00', '2026-10-14 23:59:59', 'active'),
(3, 1, 'Student Pass', 'Discounted entry for verified university students with valid institutional student ID.', 750.00, 300, 300, 2, '2026-08-01 00:00:00', '2026-10-10 23:59:59', 'active'),

-- Event 2: Music Concert
(4, 2, 'General Admission', 'Standing arena access close to the main stage and festival fairground.', 1200.00, 3000, 3000, 6, '2026-08-15 00:00:00', '2026-10-27 23:59:59', 'active'),
(5, 2, 'VIP Platinum Lounge', 'Elevated seated platform, complimentary refreshments, and artist photo-op pass.', 3500.00, 500, 500, 4, '2026-08-15 00:00:00', '2026-10-27 23:59:59', 'active'),

-- Event 3: Leadership Forum
(6, 3, 'Executive Pass', 'Access to keynotes, panel discussions, and executive buffet networking lunch.', 6000.00, 150, 150, 2, '2026-09-01 00:00:00', '2026-11-10 23:59:59', 'active');

-- 10. Insert System Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('platform_name', 'Sidra Event Ticketing Platform', 'general', 'Platform public branding name'),
('platform_tagline', 'Discover, Book & Verify Digital Tickets with Absolute Confidence', 'general', 'Public hero tagline'),
('contact_email', 'support@sidra.test', 'general', 'Customer support email'),
('contact_phone', '+880 9612-888999', 'general', 'Customer support helpline'),
('currency_code', 'BDT', 'financial', 'Default currency code'),
('currency_symbol', '৳', 'financial', 'Default currency symbol display'),
('bkash_number', '01711-223344', 'payments', 'bKash Merchant / Personal receiving number'),
('bkash_type', 'Merchant', 'payments', 'bKash account type (Merchant / Personal)'),
('bkash_instructions', 'Dial *247# or use bKash App -> Make Payment -> Enter Number -> Enter Amount -> Enter Ref -> Confirm with PIN', 'payments', 'Step-by-step bKash payment instructions'),
('nagad_number', '01811-223344', 'payments', 'Nagad Merchant / Personal receiving number'),
('nagad_type', 'Merchant', 'payments', 'Nagad account type (Merchant / Personal)'),
('nagad_instructions', 'Dial *167# or use Nagad App -> Merchant Pay -> Enter Number -> Enter Amount -> Enter Ref -> Confirm with PIN', 'payments', 'Step-by-step Nagad payment instructions'),
('rocket_number', '01911-223344-5', 'payments', 'Rocket Biller / Personal receiving number'),
('rocket_type', 'Merchant', 'payments', 'Rocket account type (Merchant / Personal)'),
('rocket_instructions', 'Dial *322# or use Rocket App -> Merchant Pay -> Enter Number -> Enter Amount -> Enter Ref -> Confirm with PIN', 'payments', 'Step-by-step Rocket payment instructions'),
('ticket_terms', '1. Tickets are non-transferable and non-refundable once verified.\n2. Must present QR code (digital or printed) at the entrance gate.\n3. Gate staff reserves the right of admission.', 'tickets', 'Default terms and conditions printed on digital tickets');
