<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Venue;

class VenueController {
    public function index(Request $request): Response {
        $venues = Venue::all();
        return (new Response())->setContent(
            View::render('admin.venues.index', [
                'venues' => $venues,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function store(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:150',
            'address' => 'required|min:3|max:255',
            'city' => 'required|max:80',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/venues');
        }

        Venue::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'city' => $request->input('city', 'Dhaka'),
            'capacity' => (int)$request->input('capacity', 1000),
            'map_url' => $request->input('map_url'),
            'contact_phone' => $request->input('contact_phone'),
        ]);

        AuditLog::record(Auth::id(), 'venue.created', 'venue', null, null, ['name' => $request->input('name')]);
        flash('success', 'Venue registered successfully.');
        redirect('/admin/venues');
    }

    public function update(Request $request, string $id): Response {
        $venueId = (int)$id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:150',
            'address' => 'required|min:3|max:255',
            'city' => 'required|max:80',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/venues');
        }

        Venue::update($venueId, [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'city' => $request->input('city', 'Dhaka'),
            'capacity' => (int)$request->input('capacity', 1000),
            'map_url' => $request->input('map_url'),
            'contact_phone' => $request->input('contact_phone'),
        ]);

        AuditLog::record(Auth::id(), 'venue.updated', 'venue', $venueId);
        flash('success', 'Venue details updated.');
        redirect('/admin/venues');
    }
}
