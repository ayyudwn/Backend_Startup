<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $customers = User::query()
            ->where('role', 'user')
            ->withCount('orders')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($customerQuery) use ($search) {
                    $customerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_customers' => User::where('role', 'user')->count(),

            'active_customers' => User::where('role', 'user')
                ->where('is_active', true)
                ->count(),

            'inactive_customers' => User::where('role', 'user')
                ->where('is_active', false)
                ->count(),

            'customers_with_orders' => User::where('role', 'user')
                ->whereHas('orders')
                ->count(),
        ];

        return view('admin.customers.index', compact(
            'customers',
            'stats',
            'search'
        ));
    }

    public function show(User $user): View
    {
        abort_unless(
            $user->role === 'user',
            404,
            'Pelanggan tidak ditemukan.'
        );

        $user->load([
            'orders' => function ($query) {
                $query
                    ->with(['package', 'payment'])
                    ->latest();
            },
        ]);

        $customerStats = [
            'total_orders' => $user->orders->count(),

            'pending_orders' => $user->orders
                ->where('status', 'pending')
                ->count(),

            'active_orders' => $user->orders
                ->whereIn('status', ['queue', 'process', 'review'])
                ->count(),

            'done_orders' => $user->orders
                ->where('status', 'done')
                ->count(),

            'total_spending' => $user->orders
                ->filter(function ($order) {
                    return $order->payment?->status === 'verified';
                })
                ->sum('total_price'),
        ];

        return view('admin.customers.show', compact(
            'user',
            'customerStats'
        ));
    }

    public function resetPassword(
        Request $request,
        User $user
    ): RedirectResponse {
        abort_unless(
            $user->role === 'user',
            404,
            'Pelanggan tidak ditemukan.'
        );

        $data = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with(
            'success',
            'Password pelanggan berhasil diganti.'
        );
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless(
            $user->role === 'user',
            404,
            'Pelanggan tidak ditemukan.'
        );

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        $message = $user->is_active
            ? 'Akun pelanggan berhasil diaktifkan.'
            : 'Akun pelanggan berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }
}