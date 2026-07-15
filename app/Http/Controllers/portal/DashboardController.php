<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\BusinessInformation;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Page;
use App\Models\Role;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usercount = User::count();
        $categorycount = Category::count();
        $pagecount = Page::count();
        $businessCount = BusinessInformation::count();
        $contactCount = Contact::count();
        $subscriberCount = Subscriber::count();
        // Users carry a role_id pointing at the `roles` table, not a plain
        // `role` string column — match by role_id, not `role`, or these are
        // always zero regardless of how many users actually have that role.
        $buyerCount = User::whereIn('role_id', Role::where('name', 'Buyer')->pluck('id'))->count();
        $sellerCount = User::whereIn('role_id', Role::where('name', 'Seller')->pluck('id'))->count();
        $attorneyCount = User::whereIn('role_id', Role::where('name', 'like', '%Attorney%')->pluck('id'))->count();
        // "Brokers" spans multiple granular role names (Business Broker, Real
        // Estate Broker, ...) rather than a single exact "Broker" role.
        $brokerCount = User::whereIn('role_id', Role::where('name', 'like', '%Broker%')->pluck('id'))->count();
        // No subscription-purchase or testimonial tables exist yet, so those
        // two dashboard tiles have nothing real to report until that data
        // model is built — left out here so the view's `?? 0` fallback shows
        // a plain 0 instead of a stand-in number that looks real but isn't.

        return view('portal.dashboard.dashboard', compact('usercount', 'categorycount', 'pagecount', 'businessCount', 'contactCount', 'subscriberCount', 'buyerCount', 'brokerCount', 'sellerCount', 'attorneyCount'));
    }
}
