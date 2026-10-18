<?php

namespace App\Http\Controllers;

use App\Models\QcEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $roleFilter = in_array($request->query('role'), ['admin', 'member'], true)
            ? $request->query('role')
            : null;
        $userQuery = User::query();
        if ($roleFilter !== null) {
            $userQuery->where('role', $roleFilter);
        }

        $users = $userQuery->latest()->paginate(15)->withQueryString();
        $memberCount = User::where('role', 'member')->count();
        $adminCount = User::where('role', 'admin')->count();
        $accountCount = $memberCount + $adminCount;
        $eventCount = QcEvent::count();

        return view('admin.dashboard', compact(
            'users',
            'memberCount',
            'adminCount',
            'accountCount',
            'eventCount',
            'roleFilter'
        ));
    }
}
