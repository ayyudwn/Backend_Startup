<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPackageController extends Controller
{
    public function index(): View
    {
        $packages = ServicePackage::query()
            ->withCount('orders')
            ->latest()
            ->get();

        return view(
            'admin.packages',
            compact('packages')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $data = $this->validatePackage(
            $request
        );

        ServicePackage::query()->create(
            $this->preparePackageData($data)
        );

        return redirect()
            ->route('admin.packages')
            ->with(
                'success',
                'Paket layanan berhasil ditambahkan.'
            );
    }

    public function update(
        Request $request,
        ServicePackage $package
    ): RedirectResponse {
        $data = $this->validatePackage(
            $request,
            $package
        );

        $package->update(
            $this->preparePackageData(
                $data,
                $package
            )
        );

        return redirect()
            ->route('admin.packages')
            ->with(
                'success',
                'Paket layanan berhasil diperbarui.'
            );
    }

    public function toggleStatus(
        ServicePackage $package
    ): RedirectResponse {
        $package->update([
            'is_active' => ! $package->is_active,
        ]);

        $message = $package->is_active
            ? 'Paket layanan berhasil diaktifkan.'
            : 'Paket layanan berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.packages')
            ->with('success', $message);
    }

    public function destroy(
        ServicePackage $package
    ): RedirectResponse {
        if ($package->orders()->exists()) {
            $package->update([
                'is_active' => false,
            ]);

            return redirect()
                ->route('admin.packages')
                ->with(
                    'error',
                    'Paket sudah pernah digunakan dalam pesanan sehingga tidak dapat dihapus. Paket telah dinonaktifkan.'
                );
        }

        $package->delete();

        return redirect()
            ->route('admin.packages')
            ->with(
                'success',
                'Paket layanan berhasil dihapus.'
            );
    }

    private function validatePackage(
        Request $request,
        ?ServicePackage $package = null
    ): array {
        return $request->validate([
            'service_type' => [
                'required',
                'string',
                Rule::in(
                    ServicePackage::SERVICE_TYPES
                ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'packages',
                    'name'
                )->ignore($package?->id),
            ],

            'description' => [
                'required',
                'string',
                'max:3000',
            ],

            'includes_text' => [
                'required',
                'string',
                'max:5000',
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
                'max:100',
            ],

            'total_slot' => [
                'required',
                'integer',
                'min:0',
                'max:10000',
            ],
        ], [
            'service_type.required' =>
                'Jenis layanan wajib dipilih.',

            'service_type.in' =>
                'Jenis layanan yang dipilih tidak valid.',

            'name.required' =>
                'Nama paket wajib diisi.',

            'name.unique' =>
                'Nama paket tersebut sudah digunakan.',

            'description.required' =>
                'Deskripsi paket wajib diisi.',

            'includes_text.required' =>
                'Daftar isi layanan wajib diisi.',

            'price.required' =>
                'Harga paket wajib diisi.',

            'duration.required' =>
                'Durasi pengerjaan wajib diisi.',
        ]);
    }

    private function preparePackageData(
        array $data,
        ?ServicePackage $package = null
    ): array {
        return [
            'service_type' =>
                $data['service_type'],

            'name' =>
                trim($data['name']),

            'description' =>
                trim($data['description']),

            'includes' =>
                $this->parseIncludes(
                    $data['includes_text']
                ),

            'price' =>
                $data['price'],

            'duration' =>
                trim($data['duration']),

            'revision_limit' =>
                $data['revision_limit'],

            'total_slot' =>
                $data['total_slot'],

            'is_active' =>
                $package?->is_active ?? true,
        ];
    }

    private function parseIncludes(
        string $includesText
    ): array {
        return collect(
            preg_split(
                '/\r\n|\r|\n/',
                $includesText
            )
        )
            ->map(
                fn ($include) =>
                    trim((string) $include)
            )
            ->filter()
            ->unique(
                fn ($include) =>
                    strtolower($include)
            )
            ->values()
            ->all();
    }
}