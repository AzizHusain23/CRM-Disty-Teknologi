<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    private const PER_PAGE_OPTIONS = [25, 50, 100, 200, 500];

    private const SORTABLE_COLUMNS = [
        'registration_number',
        'customer',
        'training',
        'training_date',
        'status',
        'amount',
        'created_at',
    ];

    private const STATUS_LABELS = [
        'registered' => 'Registered',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, self::SORTABLE_COLUMNS, true) ? $sort : 'created_at';

        $direction = strtolower($request->string('direction')->toString());
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $perPageOptions = self::PER_PAGE_OPTIONS;
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $query = Registration::query()
            ->with([
                'customer.institution',
                'training.category',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('customer_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('training', function ($trainingQuery) use ($search) {
                        $trainingQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && array_key_exists($request->string('status')->toString(), self::STATUS_LABELS)) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($sort === 'customer') {
            $query->orderBy(
                Customer::query()
                    ->select('name')
                    ->whereColumn('customers.id', 'registrations.customer_id'),
                $direction
            );
        } elseif ($sort === 'training') {
            $query->orderBy(
                Training::query()
                    ->select('name')
                    ->whereColumn('trainings.id', 'registrations.training_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $registrations = $query
            ->paginate($perPage)
            ->withQueryString();

        $statusLabels = self::STATUS_LABELS;

        return view('registrations.index', compact(
            'registrations',
            'sort',
            'direction',
            'perPage',
            'perPageOptions',
            'statusLabels'
        ));
    }

    public function create(Request $request): View
    {
        $customers = Customer::query()
            ->whereIn('status', ['active', 'repeat'])
            ->orderBy('name')
            ->get();

        $trainings = Training::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        $selectedCustomerId = $request->integer('customer_id') ?: null;
        $selectedTrainingId = $request->integer('training_id') ?: null;
        $statusLabels = self::STATUS_LABELS;

        if ($selectedCustomerId && !$customers->contains('id', $selectedCustomerId)) {
            $selectedCustomerId = null;
        }

        return view('registrations.create', compact(
            'customers',
            'trainings',
            'selectedCustomerId',
            'selectedTrainingId',
            'statusLabels'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRegistration($request);

        $customer = Customer::findOrFail($validated['customer_id']);
        $training = Training::findOrFail($validated['training_id']);

        if (!in_array($customer->status, ['active', 'repeat'], true)) {
            return back()
                ->withInput()
                ->with('error', 'Hanya customer Active atau Repeat Customer yang dapat didaftarkan ke pelatihan.');
        }

        if (!$training->is_active) {
            return back()
                ->withInput()
                ->with('error', 'Pelatihan yang dipilih sedang nonaktif.');
        }

        $registration = DB::transaction(function () use ($validated, $customer, $training) {
            $registration = Registration::create([
                'customer_id' => $customer->id,
                'training_id' => $training->id,
                'training_date' => $validated['training_date'] ?? null,
                'status' => $validated['status'],
                'amount' => $validated['amount'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (!$registration->registration_number) {
                $registration->update([
                    'registration_number' => sprintf(
                        'REG-%s-%06d',
                        now()->format('Y'),
                        $registration->id
                    ),
                ]);
            }

            Activity::create([
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'type' => 'note',
                'subject' => 'Customer terdaftar pada pelatihan',
                'description' => sprintf(
                    '%s terdaftar pada pelatihan %s dengan status %s.',
                    $customer->name,
                    $training->name,
                    self::STATUS_LABELS[$registration->status] ?? ucfirst($registration->status)
                ),
                'activity_at' => now(),
            ]);

            return $registration;
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer berhasil dicatat masuk ke pelatihan. Nomor registrasi: ' . $registration->registration_number);
    }

    public function edit(Registration $registration): View
    {
        $registration->load(['customer', 'training']);

        $customers = Customer::query()
            ->whereIn('status', ['active', 'repeat'])
            ->orderBy('name')
            ->get();

        $trainings = Training::query()
            ->where(function ($query) use ($registration) {
                $query->where('is_active', true)
                    ->orWhere('id', $registration->training_id);
            })
            ->with('category')
            ->orderBy('name')
            ->get();

        $statusLabels = self::STATUS_LABELS;

        return view('registrations.edit', compact(
            'registration',
            'customers',
            'trainings',
            'statusLabels'
        ));
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $validated = $this->validateRegistration($request);

        $customer = Customer::findOrFail($validated['customer_id']);
        $training = Training::findOrFail($validated['training_id']);

        if (!in_array($customer->status, ['active', 'repeat'], true)) {
            return back()
                ->withInput()
                ->with('error', 'Hanya customer Active atau Repeat Customer yang dapat memiliki registrasi aktif.');
        }

        if (!$training->is_active && (int) $training->id !== (int) $registration->training_id) {
            return back()
                ->withInput()
                ->with('error', 'Pelatihan yang dipilih sedang nonaktif.');
        }

        $oldTrainingId = $registration->training_id;
        $oldStatus = $registration->status;

        DB::transaction(function () use ($registration, $validated, $customer, $training, $oldTrainingId, $oldStatus) {
            $registration->update([
                'customer_id' => $customer->id,
                'training_id' => $training->id,
                'training_date' => $validated['training_date'] ?? null,
                'status' => $validated['status'],
                'amount' => $validated['amount'] ?? null,
                'registration_number' => $validated['registration_number'] ?? $registration->registration_number,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($oldTrainingId !== $training->id || $oldStatus !== $registration->status) {
                Activity::create([
                    'customer_id' => $customer->id,
                    'user_id' => auth()->id(),
                    'type' => 'note',
                    'subject' => 'Data pendaftaran pelatihan diperbarui',
                    'description' => sprintf(
                        'Pendaftaran %s diperbarui. Pelatihan: %s, status: %s.',
                        $registration->registration_number,
                        $training->name,
                        self::STATUS_LABELS[$registration->status] ?? ucfirst($registration->status)
                    ),
                    'activity_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('registrations.index')
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $customer = $registration->customer;
        $registration->delete();

        return redirect()
            ->route('registrations.index')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    private function validateRegistration(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'training_id' => ['required', 'integer', 'exists:trainings,id'],
            'training_date' => ['nullable', 'date'],
            'status' => ['required', 'in:registered,confirmed,completed,cancelled'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
            'training_id.required' => 'Pelatihan wajib dipilih.',
            'training_id.exists' => 'Pelatihan tidak ditemukan.',
            'training_date.date' => 'Tanggal training tidak valid.',
            'status.in' => 'Status pendaftaran tidak valid.',
            'amount.numeric' => 'Amount harus berupa angka.',
            'amount.min' => 'Amount tidak boleh negatif.',
        ]);
    }
}
