<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        $query = User::query()->with('facility')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (in_array($role, UserRole::values(), true)) {
            $query->where('role', $role);
        }

        if (in_array($status, array_column(UserStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'search' => $search,
            'role' => $role,
            'status' => $status,
        ]);
    }
}
