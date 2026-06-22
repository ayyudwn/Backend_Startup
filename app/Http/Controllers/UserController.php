<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductionQuota;
use App\Models\ServicePackage;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class UserController extends Controller
{
    public function home(): View
    {
        $packages = ServicePackage::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('user.home', compact('packages'));
    }

    public function dashboard(Request $request): View
    {
        $userId = $request->user()->id;

        $stats = [
            'total' => Order::where('user_id', $userId)->count(),

            'waiting_payment' => Order::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),

            'in_progress' => Order::where('user_id', $userId)
                ->whereIn('status', ['queue', 'process', 'review'])
                ->count(),

            'done' => Order::where('user_id', $userId)
                ->where('status', 'done')
                ->count(),
        ];

        $orders = Order::with(['package', 'payment', 'results'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('user.dashboard', compact('stats', 'orders'));
    }

    public function createOrder(Request $request): View
    {
        $packages = ServicePackage::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $vouchers = Voucher::query()
            ->where('is_active', true)
            ->whereColumn('usage_count', '<', 'usage_limit')
            ->orderBy('code')
            ->get();

        $quotas = ProductionQuota::query()
            ->whereDate('date', '>=', now()->toDateString())
            ->where('status', 'open')
            ->whereColumn('used_quota', '<', 'max_quota')
            ->orderBy('date')
            ->get();

        $selectedPackageId = $request->integer('package');

        return view('user.order-create', compact(
            'packages',
            'vouchers',
            'quotas',
            'selectedPackageId'
        ));
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package_id' => [
                'required',
                Rule::exists('packages', 'id')->where('is_active', true),
            ],
            'title' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'platform' => ['nullable', 'string', 'max:50'],
            'content_size' => ['nullable', 'string', 'max:50'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'deadline_type' => [
                'required',
                Rule::in(['regular', 'express', 'kilat']),
            ],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => [
                'required',
                Rule::in(['Transfer Bank', 'QRIS', 'E-Wallet']),
            ],
            'reference_file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,zip',
                'max:10240',
            ],
        ]);

        try {
            $order = DB::transaction(function () use ($request, $data) {
                $package = ServicePackage::query()
                    ->lockForUpdate()
                    ->findOrFail($data['package_id']);

                $quota = ProductionQuota::query()
                    ->whereDate('date', $data['booking_date'])
                    ->lockForUpdate()
                    ->first();

                if (! $quota) {
                    throw new \RuntimeException(
                        'Tanggal tersebut belum dibuka oleh admin.'
                    );
                }

                if (
                    $quota->status !== 'open'
                    || $quota->used_quota >= $quota->max_quota
                ) {
                    throw new \RuntimeException(
                        'Kuota produksi pada tanggal tersebut sudah penuh atau ditutup.'
                    );
                }

                $basePrice = (int) $package->price;
                $additionalPrice = $this->calculateAdditionalPrice(
                    $basePrice,
                    $data['deadline_type']
                );

                $subtotal = $basePrice + $additionalPrice;

                $voucher = null;
                $discount = 0;

                if (! empty($data['voucher_code'])) {
                    $voucher = Voucher::query()
                        ->whereRaw('UPPER(code) = ?', [
                            strtoupper($data['voucher_code']),
                        ])
                        ->lockForUpdate()
                        ->first();

                    if (! $voucher || ! $voucher->isAvailable()) {
                        throw new \RuntimeException(
                            'Voucher tidak ditemukan, sudah tidak aktif, atau kuotanya habis.'
                        );
                    }

                    $discount = (int) round(
                        $subtotal * ($voucher->discount_percent / 100)
                    );
                }

                $totalPrice = max(0, $subtotal - $discount);

                $referencePath = null;

                if ($request->hasFile('reference_file')) {
                    $referencePath = $request->file('reference_file')
                        ->store('order-references', 'public');
                }

                $order = Order::create([
                    'order_code' => $this->generateOrderCode(),
                    'user_id' => $request->user()->id,
                    'package_id' => $package->id,
                    'voucher_id' => $voucher?->id,
                    'title' => $data['title'],
                    'notes' => $data['notes'] ?? null,
                    'reference_file' => $referencePath,
                    'platform' => $data['platform'] ?? null,
                    'content_size' => $data['content_size'] ?? null,
                    'booking_date' => $data['booking_date'],
                    'deadline_type' => $data['deadline_type'],
                    'base_price' => $basePrice,
                    'additional_price' => $additionalPrice,
                    'discount' => $discount,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'priority' => $this->priorityLabel(
                        $data['deadline_type']
                    ),
                ]);

                Payment::create([
                    'order_id' => $order->id,
                    'method' => $data['payment_method'],
                    'amount' => $totalPrice,
                    'proof_image' => null,
                    'status' => 'pending',
                ]);

                $quota->increment('used_quota');

                if ($quota->fresh()->used_quota >= $quota->max_quota) {
                    $quota->update([
                        'status' => 'full',
                    ]);
                }

                if ($voucher) {
                    $voucher->increment('usage_count');
                }

                return $order;
            });

            return redirect()
                ->route('user.orders.show', $order)
                ->with(
                    'success',
                    'Pesanan berhasil dibuat. Silakan unggah bukti pembayaran.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function showOrder(Request $request, Order $order): View
    {
        $this->ensureOrderOwner($request, $order);

        $order->load([
            'package',
            'voucher',
            'payment',
            'results',
        ]);

        return view('user.order-show', compact('order'));
    }


    public function uploadPayment(
        Request $request,
        Order $order
    ): RedirectResponse {
        $this->ensureOrderOwner($request, $order);

        $data = $request->validate([
            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $payment = $order->payment;

        if (! $payment) {
            return back()->with(
                'error',
                'Data pembayaran untuk pesanan ini tidak ditemukan.'
            );
        }

        if ($payment->status === 'verified') {
            return back()->with(
                'error',
                'Pembayaran pesanan ini sudah diverifikasi.'
            );
        }

        if (
            $payment->proof_image
            && Storage::disk('public')->exists(
                $payment->proof_image
            )
        ) {
            Storage::disk('public')->delete(
                $payment->proof_image
            );
        }

        $proofPath = $data['proof_image']->store(
            'payment-proofs',
            'public'
        );

        $payment->update([
            'proof_image' => $proofPath,
            'status' => 'pending',
            'verified_at' => null,
        ]);

        $order->update([
            'status' => 'pending',
            'production_team_id' => null,
        ]);

        return back()->with(
            'success',
            'Bukti pembayaran berhasil dikirim dan menunggu verifikasi admin.'
        );
    }

    public function downloadResult(
        Request $request,
        Order $order,
        int $result
    ) {
        $this->ensureOrderOwner($request, $order);

        $orderResult = $order->results()
            ->whereKey($result)
            ->firstOrFail();

        if (
            ! $orderResult->file_path
            || ! Storage::disk('public')->exists($orderResult->file_path)
        ) {
            return back()->with(
                'error',
                'File hasil belum tersedia atau sudah tidak ditemukan.'
            );
        }

        return Storage::disk('public')->download(
            $orderResult->file_path,
            $orderResult->file_name
        );
    }

    private function ensureOrderOwner(
        Request $request,
        Order $order
    ): void {
        abort_unless(
            $order->user_id === $request->user()->id,
            403,
            'Pesanan ini bukan milik kamu.'
        );
    }

    private function calculateAdditionalPrice(
        int $basePrice,
        string $deadlineType
    ): int {
        return match ($deadlineType) {
            'express' => (int) round($basePrice * 0.25),
            'kilat' => (int) round($basePrice * 0.50),
            default => 0,
        };
    }

    private function priorityLabel(string $deadlineType): string
    {
        return match ($deadlineType) {
            'express' => 'Cepat',
            'kilat' => 'Kilat',
            default => 'Reguler',
        };
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'CTF-'.now()->format('ymd').'-'.Str::upper(
                Str::random(5)
            );
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}