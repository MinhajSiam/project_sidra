<?php

declare(strict_types=1);

// Define Application Start
define('SIDRA_START', microtime(true));
define('SIDRA_ROOT', dirname(__DIR__));

// Autoloader & Helpers
require_once SIDRA_ROOT . '/vendor/autoload.php';

use App\Core\Application;
use App\Middleware\AuthMiddleware;
use App\Middleware\CustomerAuthMiddleware;
use App\Middleware\RoleMiddleware;

$app = Application::getInstance();
$router = $app->getRouter();

// ------------------------------------------------------------
// 1. Public Portal & Pages
// ------------------------------------------------------------
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/about', [\App\Controllers\HomeController::class, 'about']);
$router->get('/contact', [\App\Controllers\HomeController::class, 'contact']);
$router->get('/terms', [\App\Controllers\HomeController::class, 'terms']);
$router->get('/privacy', [\App\Controllers\HomeController::class, 'privacy']);

// ------------------------------------------------------------
// 2. Event Discovery & Booking
// ------------------------------------------------------------
$router->get('/events', [\App\Controllers\EventController::class, 'index']);
$router->get('/events/{slug}', [\App\Controllers\EventController::class, 'show']);
$router->get('/events/{slug}/checkout', [\App\Controllers\EventController::class, 'checkout']);
$router->post('/booking/store', [\App\Controllers\BookingController::class, 'store']);
$router->get('/booking/{ref}/payment', [\App\Controllers\BookingController::class, 'payment']);
$router->post('/booking/{ref}/payment', [\App\Controllers\BookingController::class, 'submitPayment']);
$router->get('/booking/{ref}/confirmation', [\App\Controllers\BookingController::class, 'confirmation']);

// ------------------------------------------------------------
// 3. Digital Ticket Verification & Gate Operations
// ------------------------------------------------------------
$router->get('/verify/{token}', [\App\Controllers\VerificationController::class, 'publicVerify']);
$router->get('/tickets/{code}', [\App\Controllers\CustomerDashboardController::class, 'showTicketPublic']);
$router->get('/tickets/{code}/print', [\App\Controllers\CustomerDashboardController::class, 'printTicketPublic']);
$router->get('/gate/scan', [\App\Controllers\VerificationController::class, 'scan'], [AuthMiddleware::class, new RoleMiddleware(['gate_staff', 'admin', 'super_admin'])]);
$router->post('/gate/verify', [\App\Controllers\VerificationController::class, 'verify'], [AuthMiddleware::class, new RoleMiddleware(['gate_staff', 'admin', 'super_admin'])]);

// ------------------------------------------------------------
// 4. Customer Authentication
// ------------------------------------------------------------
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/forgot-password', [\App\Controllers\AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [\App\Controllers\AuthController::class, 'sendResetLink']);
$router->get('/reset-password', [\App\Controllers\AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [\App\Controllers\AuthController::class, 'resetPassword']);

// ------------------------------------------------------------
// 5. Customer Dashboard (Guarded)
// ------------------------------------------------------------
$router->get('/customer/dashboard', [\App\Controllers\CustomerDashboardController::class, 'dashboard'], [CustomerAuthMiddleware::class]);
$router->get('/customer/bookings', [\App\Controllers\CustomerDashboardController::class, 'bookings'], [CustomerAuthMiddleware::class]);
$router->get('/customer/tickets', [\App\Controllers\CustomerDashboardController::class, 'tickets'], [CustomerAuthMiddleware::class]);
$router->get('/customer/tickets/{code}', [\App\Controllers\CustomerDashboardController::class, 'showTicket'], [CustomerAuthMiddleware::class]);
$router->get('/customer/tickets/{code}/print', [\App\Controllers\CustomerDashboardController::class, 'printTicket']);
$router->get('/customer/profile', [\App\Controllers\CustomerDashboardController::class, 'profile'], [CustomerAuthMiddleware::class]);
$router->post('/customer/profile', [\App\Controllers\CustomerDashboardController::class, 'profile'], [CustomerAuthMiddleware::class]);

// ------------------------------------------------------------
// 6. Administrative Portal Authentication
// ------------------------------------------------------------
$router->get('/admin/login', [\App\Controllers\AuthController::class, 'showAdminLogin']);
$router->post('/admin/login', [\App\Controllers\AuthController::class, 'adminLogin']);
$router->post('/admin/logout', [\App\Controllers\AuthController::class, 'adminLogout']);

// ------------------------------------------------------------
// 7. Administrative & Management Modules (Guarded by RBAC)
// ------------------------------------------------------------
// Dashboard
$router->get('/admin', [\App\Controllers\Admin\DashboardController::class, 'index'], [AuthMiddleware::class]);

// Events Management
$router->get('/admin/events', [\App\Controllers\Admin\EventController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->get('/admin/events/create', [\App\Controllers\Admin\EventController::class, 'create'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/events/store', [\App\Controllers\Admin\EventController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->get('/admin/events/{id}/edit', [\App\Controllers\Admin\EventController::class, 'edit'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/events/{id}/update', [\App\Controllers\Admin\EventController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/events/{id}/toggle', [\App\Controllers\Admin\EventController::class, 'toggleStatus'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/events/{id}/delete', [\App\Controllers\Admin\EventController::class, 'delete'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin'])]);

// Ticket Inventory
$router->get('/admin/tickets', [\App\Controllers\Admin\TicketController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/tickets/type/store', [\App\Controllers\Admin\TicketController::class, 'storeType'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/tickets/type/{id}/update', [\App\Controllers\Admin\TicketController::class, 'updateType'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);

// Categories & Venues
$router->get('/admin/categories', [\App\Controllers\Admin\EventCategoryController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/categories', [\App\Controllers\Admin\EventCategoryController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/categories/store', [\App\Controllers\Admin\EventCategoryController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/categories/{id}', [\App\Controllers\Admin\EventCategoryController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/categories/{id}/update', [\App\Controllers\Admin\EventCategoryController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);

$router->get('/admin/venues', [\App\Controllers\Admin\VenueController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/venues', [\App\Controllers\Admin\VenueController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/venues/store', [\App\Controllers\Admin\VenueController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/venues/{id}', [\App\Controllers\Admin\VenueController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);
$router->post('/admin/venues/{id}/update', [\App\Controllers\Admin\VenueController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'event_manager'])]);

// Bookings
$router->get('/admin/bookings', [\App\Controllers\Admin\BookingController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);
$router->get('/admin/bookings/{id}', [\App\Controllers\Admin\BookingController::class, 'show'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);

// Payments Approval Workflow
$router->get('/admin/payments', [\App\Controllers\Admin\PaymentController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);
$router->post('/admin/payments/{id}/approve', [\App\Controllers\Admin\PaymentController::class, 'approve'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);
$router->post('/admin/payments/{id}/reject', [\App\Controllers\Admin\PaymentController::class, 'reject'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);

// Customers Database
$router->get('/admin/customers', [\App\Controllers\Admin\CustomerController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin'])]);
$router->get('/admin/customers/{id}', [\App\Controllers\Admin\CustomerController::class, 'show'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin'])]);

// Staff & Role Management
$router->get('/admin/users', [\App\Controllers\Admin\UserController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/users', [\App\Controllers\Admin\UserController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/users/store', [\App\Controllers\Admin\UserController::class, 'store'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/users/{id}', [\App\Controllers\Admin\UserController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/users/{id}/update', [\App\Controllers\Admin\UserController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);

// Reports & Analytics
$router->get('/admin/reports', [\App\Controllers\Admin\ReportController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);
$router->get('/admin/reports/export', [\App\Controllers\Admin\ReportController::class, 'exportCsv'], [AuthMiddleware::class, new RoleMiddleware(['super_admin', 'admin', 'finance_manager'])]);

// Audit Logs
$router->get('/admin/audit-logs', [\App\Controllers\Admin\AuditLogController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);

// System Settings
$router->get('/admin/settings', [\App\Controllers\Admin\SettingController::class, 'index'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/settings', [\App\Controllers\Admin\SettingController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);
$router->post('/admin/settings/update', [\App\Controllers\Admin\SettingController::class, 'update'], [AuthMiddleware::class, new RoleMiddleware(['super_admin'])]);

// ------------------------------------------------------------
// Run Application
// ------------------------------------------------------------
$app->run();
