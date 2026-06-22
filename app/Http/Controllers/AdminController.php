<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductionQuota;
use App\Models\ProductionTeam;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $stats = [
            'orders_today' => Order::whereDate(
                'created_at',
                $today
            )->count(),

            'pending_payments' => Payment::where(
                'status',
                'pending'
            )
                ->whereNotNull('proof_image')
                ->count(),

            'in_production' => Order::query()
                ->inProduction()
                ->withVerifiedPayment()
                ->count(),

            'done_today' => Order::query()
                ->completed()
                ->whereDate(
                    'updated_at',
                    $today
                )
                ->count(),

            'revenue_today' => Payment::where(
                'status',
                'verified'
            )
                ->whereDate(
                    'verified_at',
                    $today
                )
                ->sum('amount'),

            'total_customers' => User::where(
                'role',
                'user'
            )->count(),
        ];

        $latestOrders = Order::with([
            'user',
            'package',
            'payment',
            'productionTeam',
        ])
            ->latest()
            ->take(5)
            ->get();

        return view(
            'admin.dashboard',
            compact('stats', 'latestOrders')
        );
    }

    public function orders()
    {
        $orders = Order::with([
            'user',
            'package',
            'payment',
            'productionTeam',
        ])
            ->withVerifiedPayment()
            ->inProduction()
            ->whereNotNull(
                'production_team_id'
            )
            ->orderByRaw("
                CASE
                    WHEN status = 'queue' THEN 1
                    WHEN status = 'process' THEN 2
                    WHEN status = 'review' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->get();

        return view(
            'admin.orders',
            compact('orders')
        );
    }

    public function orderHistory()
    {
        $orders = Order::with([
            'user',
            'package',
            'payment',
            'productionTeam',
        ])
            ->completed()
            ->latest('updated_at')
            ->get();

        return view(
            'admin.order-history',
            compact('orders')
        );
    }

    public function showOrder(Order $order)
    {
        $order->load([
            'user',
            'package',
            'voucher',
            'payment',
            'results',
            'productionTeam',
        ]);

        return view(
            'admin.order-show',
            compact('order')
        );
    }

    public function updateOrderStatus(
        Request $request,
        Order $order
    ) {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    Order::STATUS_QUEUE,
                    Order::STATUS_PROCESS,
                    Order::STATUS_REVIEW,
                    Order::STATUS_DONE,
                ]),
            ],
        ]);

        $order->loadMissing([
            'payment',
            'productionTeam',
        ]);

        if (! $order->payment) {
            return back()->with(
                'error',
                'Data pembayaran pesanan tidak ditemukan.'
            );
        }

        if (
            $order->payment->status
            !== 'verified'
        ) {
            return back()->with(
                'error',
                'Status produksi hanya dapat diubah setelah pembayaran diverifikasi.'
            );
        }

        if (! $order->production_team_id) {
            return back()->with(
                'error',
                'Pesanan belum memiliki anggota tim produksi.'
            );
        }

        $order->update([
            'status' => $data['status'],
        ]);

        if (
            $data['status']
            === Order::STATUS_DONE
        ) {
            return redirect()
                ->route(
                    'admin.orders.history'
                )
                ->with(
                    'success',
                    'Pesanan selesai dan dipindahkan ke riwayat. Jumlah tugas aktif dan selesai tim diperbarui otomatis.'
                );
        }

        return back()->with(
            'success',
            'Status produksi berhasil diperbarui.'
        );
    }

    public function payments()
    {
        $payments = Payment::with([
            'order.user',
            'order.package',
        ])
            ->where(
                'status',
                'pending'
            )
            ->whereNotNull(
                'proof_image'
            )
            ->latest()
            ->get();

        $teams = ProductionTeam::withCount(
            'activeOrders'
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.payments',
            compact('payments', 'teams')
        );
    }

    public function verifyPayment(
        Request $request,
        Payment $payment
    ) {
        $data = $request->validate([
            'production_team_id' => [
                'required',
                'integer',
                'exists:production_teams,id',
            ],
        ], [
            'production_team_id.required' =>
                'Pilih anggota tim produksi terlebih dahulu.',

            'production_team_id.exists' =>
                'Anggota tim produksi yang dipilih tidak ditemukan.',
        ]);

        DB::transaction(function () use (
            $payment,
            $data
        ) {
            $lockedPayment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $lockedPayment->load([
                'order.package',
            ]);

            $order = $lockedPayment->order;

            if (! $order) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Pesanan dari pembayaran ini tidak ditemukan.',
                ]);
            }

            if (! $order->package) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Layanan dari pesanan ini tidak ditemukan.',
                ]);
            }

            if (
                blank(
                    $lockedPayment->proof_image
                )
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Bukti pembayaran belum diunggah.',
                ]);
            }

            if (
                $lockedPayment->status
                !== 'pending'
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Pembayaran ini sudah diproses sebelumnya.',
                ]);
            }

            if (
                $order->status
                !== Order::STATUS_PENDING
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Pesanan ini sudah masuk ke proses produksi.',
                ]);
            }

            $requiredSkill = trim(
                (string) $order->required_skill
            );

            if (
                ! in_array(
                    $requiredSkill,
                    ProductionTeam::ALLOWED_SKILLS,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'production_team_id' =>
                        'Jenis layanan pesanan belum sesuai dengan daftar keahlian tim produksi.',
                ]);
            }

            $team = ProductionTeam::query()
                ->lockForUpdate()
                ->findOrFail(
                    $data['production_team_id']
                );

            if (
                ! $team->hasSkill(
                    $requiredSkill
                )
            ) {
                throw ValidationException::withMessages([
                    'production_team_id' =>
                        'Anggota tim yang dipilih tidak memiliki keahlian ' .
                        $requiredSkill .
                        '.',
                ]);
            }

            if (
                $team->status
                === 'offline'
            ) {
                throw ValidationException::withMessages([
                    'production_team_id' =>
                        'Anggota tim sedang offline dan tidak dapat menerima pesanan.',
                ]);
            }

            $activeOrderCount = $team
                ->activeOrders()
                ->count();

            if (
                $activeOrderCount
                >= ProductionTeam::MAX_ACTIVE_ORDERS
            ) {
                throw ValidationException::withMessages([
                    'production_team_id' =>
                        'Anggota tim sudah memiliki lima pesanan aktif.',
                ]);
            }

            $lockedPayment->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            $order->update([
                'production_team_id' =>
                    $team->id,

                'status' =>
                    Order::STATUS_QUEUE,
            ]);
        });

        return back()->with(
            'success',
            'Pembayaran berhasil diverifikasi. Pesanan telah diberikan kepada anggota tim yang sesuai dan masuk antrean produksi.'
        );
    }

    public function rejectPayment(
        Payment $payment
    ) {
        DB::transaction(function () use (
            $payment
        ) {
            $lockedPayment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $lockedPayment->load('order');

            if (
                $lockedPayment->status
                !== 'pending'
            ) {
                throw ValidationException::withMessages([
                    'payment' =>
                        'Pembayaran ini sudah diproses sebelumnya.',
                ]);
            }

            $lockedPayment->update([
                'status' => 'rejected',
                'verified_at' => null,
            ]);

            if ($lockedPayment->order) {
                $lockedPayment->order->update([
                    'production_team_id' =>
                        null,

                    'status' =>
                        Order::STATUS_PENDING,
                ]);
            }
        });

        return back()->with(
            'success',
            'Pembayaran ditolak. Pelanggan dapat mengunggah ulang bukti pembayaran.'
        );
    }

    public function packages()
    {
        $packages = ServicePackage::latest()
            ->get();

        return view(
            'admin.packages',
            compact('packages')
        );
    }

    public function storePackage(
        Request $request
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::in(
                    ServicePackage::SERVICE_NAMES
                ),
                'unique:packages,name',
            ],

            'description' => [
                'required',
                'string',
            ],

            'includes' => [
                'nullable',
                'array',
            ],

            'includes.*' => [
                'nullable',
                'string',
                'max:255',
            ],

            'price' => [
                'required',
                'integer',
                'min:0',
            ],

            'duration' => [
                'required',
                'string',
                'max:100',
            ],

            'revision_limit' => [
                'required',
                'integer',
                'min:0',
            ],

            'total_slot' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $data['includes'] = collect(
            $data['includes'] ?? []
        )
            ->map(
                fn ($include) => trim(
                    (string) $include
                )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data['is_active'] = true;

        ServicePackage::create($data);

        return back()->with(
            'success',
            'Layanan berhasil ditambahkan.'
        );
    }

    public function deletePackage(
        ServicePackage $package
    ) {
        if ($package->orders()->exists()) {
            $package->update([
                'is_active' => false,
            ]);

            return back()->with(
                'success',
                'Layanan sudah memiliki pesanan sehingga dinonaktifkan, bukan dihapus.'
            );
        }

        $package->delete();

        return back()->with(
            'success',
            'Layanan berhasil dihapus.'
        );
    }

    public function quotas()
    {
        $quotas = ProductionQuota::orderBy(
            'date'
        )->get();

        return view(
            'admin.quotas',
            compact('quotas')
        );
    }

    public function storeQuota(
        Request $request
    ) {
        $data = $request->validate([
            'date' => [
                'required',
                'date',
            ],

            'max_quota' => [
                'required',
                'integer',
                'min:0',
            ],

            'used_quota' => [
                'required',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'open',
                    'full',
                    'closed',
                ]),
            ],
        ]);

        if (
            $data['used_quota']
            > $data['max_quota']
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kuota terpakai tidak boleh melebihi maksimal kuota.'
                );
        }

        ProductionQuota::updateOrCreate(
            [
                'date' => $data['date'],
            ],
            $data
        );

        return back()->with(
            'success',
            'Kuota produksi berhasil disimpan.'
        );
    }

    public function vouchers()
    {
        $vouchers = Voucher::latest()
            ->get();

        return view(
            'admin.vouchers',
            compact('vouchers')
        );
    }

    public function storeVoucher(
        Request $request
    ) {
        $request->merge([
            'code' => strtoupper(
                trim(
                    (string) $request->input(
                        'code'
                    )
                )
            ),
        ]);

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:vouchers,code',
            ],

            'discount_percent' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'usage_limit' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $data['usage_count'] = 0;
        $data['is_active'] = true;

        Voucher::create($data);

        return back()->with(
            'success',
            'Voucher berhasil dibuat.'
        );
    }

    public function teams()
    {
        $teams = ProductionTeam::withCount([
            'activeOrders',
            'completedOrders',
        ])
            ->latest()
            ->get();

        return view(
            'admin.teams',
            compact('teams')
        );
    }

    public function storeTeam(
        Request $request
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'role' => [
                'required',
                'string',
                'max:100',
            ],

            'skills' => [
                'required',
                'array',
                'min:1',
                'max:' .
                    ProductionTeam::MAX_SKILLS,
            ],

            'skills.*' => [
                'required',
                'string',
                'distinct',
                Rule::in(
                    ProductionTeam::ALLOWED_SKILLS
                ),
            ],

            'is_offline' => [
                'nullable',
                'boolean',
            ],
        ]);

        $skills = collect(
            $data['skills']
        )
            ->map(
                fn ($skill) => trim(
                    (string) $skill
                )
            )
            ->filter()
            ->unique()
            ->take(
                ProductionTeam::MAX_SKILLS
            )
            ->values()
            ->all();

        ProductionTeam::create([
            'name' => $data['name'],
            'role' => $data['role'],
            'skills' => $skills,

            'status' => $request->boolean(
                'is_offline'
            )
                ? 'offline'
                : 'available',
        ]);

        return back()->with(
            'success',
            'Anggota tim produksi berhasil ditambahkan.'
        );
    }

    public function toggleTeamStatus(
        ProductionTeam $team
    ) {
        if (
            $team->status
            === 'offline'
        ) {
            $team->update([
                'status' => 'available',
            ]);

            return back()->with(
                'success',
                'Anggota tim diaktifkan kembali.'
            );
        }

        $team->update([
            'status' => 'offline',
        ]);

        return back()->with(
            'success',
            'Anggota tim diubah menjadi offline.'
        );
    }
}