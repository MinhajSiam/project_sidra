<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Event;
use App\Models\EventCategory;

class HomeController {
    public function index(Request $request): Response {
        $featuredEvents = Event::all([
            'status' => 'published',
            'is_featured' => 1,
            'limit' => 6,
        ]);

        $upcomingEvents = Event::all([
            'status' => 'published',
            'limit' => 6,
        ]);

        $categories = EventCategory::all(true);

        return (new Response())->setContent(
            View::render('pages.home', [
                'featuredEvents' => $featuredEvents,
                'upcomingEvents' => $upcomingEvents,
                'categories' => $categories,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function about(Request $request): Response {
        return (new Response())->setContent(
            View::render('pages.about', ['layout' => 'layouts.main'])
        );
    }

    public function contact(Request $request): Response {
        return (new Response())->setContent(
            View::render('pages.contact', ['layout' => 'layouts.main'])
        );
    }

    public function terms(Request $request): Response {
        return (new Response())->setContent(
            View::render('pages.terms', ['layout' => 'layouts.main'])
        );
    }

    public function privacy(Request $request): Response {
        return (new Response())->setContent(
            View::render('pages.privacy', ['layout' => 'layouts.main'])
        );
    }
}
