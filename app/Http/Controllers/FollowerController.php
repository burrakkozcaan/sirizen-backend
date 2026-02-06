<?php

namespace App\Http\Controllers;

use App\Models\VendorFollower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FollowerController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $followers = VendorFollower::with('user')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($follower) {
                return [
                    'id' => (string) $follower->id,
                    'created_at' => $follower->created_at?->toIso8601String() ?? '',
                    'user' => $follower->user ? [
                        'id' => (string) $follower->user->id,
                        'name' => $follower->user->name,
                        'email' => $follower->user->email,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('follower/index', [
            'followers' => $followers,
        ]);
    }
}
