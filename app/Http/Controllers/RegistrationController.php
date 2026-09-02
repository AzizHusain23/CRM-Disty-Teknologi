<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Registration;
use App\Models\Trainer;
use App\Models\Training;
use App\Models\TrainingSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    private const PER_PAGE_OPTIONS = [25, 50, 100, 200, 500];
    private const SORTABLE_COLUMNS = ['registration_number','customer','training','schedule','status','amount','created_at'];
    private const STATUS_LABELS = ['registered'=>'Registered','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled'];

    public function index(Request $request): View
    {
        $sort = in_array($request->string('sort')->toString(), self::SORTABLE_COLUMNS, true) ? $request->string('sort')->toString() : 'created_at';
        $direction = in_array(strtolower($request->string('direction')->toString()), ['asc','desc'], true) ? strtolower($request->string('direction')->toString()) : 'desc';
        $perPageOptions = self::PER_PAGE_OPTIONS;
        $perPage = (int) $request->integer('per_page', 50);
        if (!in_array($perPage, $perPageOptions, true)) $perPage = 50;

        $query = Registration::query()->with(['customer.institution','training.category','schedule.trainer']);
        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($q) use ($search) {
                $q->where('registration_number','like',"%{$search}%")
                    ->orWhereHas('customer', fn($c) => $c->where('name','like',"%{$search}%")->orWhere('customer_code','like',"%{$search}%"))
                    ->orWhereHas('training', fn($t) => $t->where('name','like',"%{$search}%"))
                    ->orWhereHas('schedule.trainer', fn($t) => $t->where('name','like',"%{$search}%"));
            });
        }
        if ($request->filled('status') && isset(self::STATUS_LABELS[$request->string('status')->toString()])) $query->where('status',$request->string('status')->toString());
        if ($sort === 'customer') {
            $query->orderBy(Customer::query()->select('name')->whereColumn('customers.id','registrations.customer_id'),$direction);
        } elseif ($sort === 'training') {
            $query->orderBy(Training::query()->select('name')->whereColumn('trainings.id','registrations.training_id'),$direction);
        } elseif ($sort === 'schedule') {
            $query->orderBy(TrainingSchedule::query()->select('training_date')->whereColumn('training_schedules.id','registrations.training_schedule_id'),$direction);
        } else {
            $query->orderBy($sort,$direction);
        }
        $registrations = $query->paginate($perPage)->withQueryString();
        $statusLabels = self::STATUS_LABELS;
        return view('registrations.index', compact('registrations','sort','direction','perPage','perPageOptions','statusLabels'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::query()->whereIn('status',['active','repeat'])->orderBy('name')->get();
        $schedules = TrainingSchedule::query()->with(['training.category','trainer'])
            ->whereIn('status',['scheduled','ongoing'])
            ->orderBy('training_date')->orderBy('start_time')->get();
        $selectedCustomerId = $request->integer('customer_id') ?: null;
        $selectedScheduleId = $request->integer('training_schedule_id') ?: null;
        if ($selectedCustomerId && !$customers->contains('id',$selectedCustomerId)) $selectedCustomerId = null;
        if ($selectedScheduleId && !$schedules->contains('id',$selectedScheduleId)) $selectedScheduleId = null;
        return view('registrations.create', compact('customers','schedules','selectedCustomerId','selectedScheduleId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRegistration($request);
        $customer = Customer::findOrFail($validated['customer_id']);
        $schedule = TrainingSchedule::with(['training','trainer'])->findOrFail($validated['training_schedule_id']);
        $this->guardCustomerAndSchedule($customer,$schedule);
        $activeCount = $schedule->registrations()->where('status','!=','cancelled')->count();
        if ($schedule->capacity !== null && $activeCount >= $schedule->capacity) return back()->withInput()->with('error','Kapasitas jadwal ini sudah penuh.');

        $registration = DB::transaction(function () use ($validated,$customer,$schedule) {
            $registration = Registration::create([
                'customer_id'=>$customer->id,
                'training_id'=>$schedule->training_id,
                'training_schedule_id'=>$schedule->id,
                'training_date'=>$schedule->training_date,
                'status'=>$validated['status'],
                'amount'=>$validated['amount'] ?? $schedule->training->price,
                'registration_number'=>$validated['registration_number'] ?? null,
                'notes'=>$validated['notes'] ?? null,
            ]);
            if (!$registration->registration_number) $registration->update(['registration_number'=>sprintf('REG-%s-%06d',now()->format('Y'),$registration->id)]);
            Activity::create([
                'customer_id'=>$customer->id,'user_id'=>auth()->id(),'type'=>'note','subject'=>'Customer terdaftar pada jadwal pelatihan',
                'description'=>sprintf('%s terdaftar pada %s (%s, %s) bersama trainer %s.',$customer->name,$schedule->training->name,$schedule->training_date->format('d M Y'),substr($schedule->start_time,0,5),$schedule->trainer->name),'activity_at'=>now(),
            ]);
            $this->syncCustomerLifecycle($customer);
            return $registration;
        });
        return redirect()->route('customers.show',$customer)->with('success','Customer berhasil dicatat masuk ke jadwal pelatihan. Nomor registrasi: '.$registration->registration_number);
    }

    public function edit(Registration $registration): View
    {
        $registration->load(['customer','training','schedule']);
        $customers=Customer::query()->whereIn('status',['active','repeat'])->orderBy('name')->get();
        $schedules=TrainingSchedule::query()->with(['training.category','trainer'])
            ->where(fn($q)=>$q->whereIn('status',['scheduled','ongoing'])->orWhere('id',$registration->training_schedule_id))
            ->orderBy('training_date')->orderBy('start_time')->get();
        $statusLabels=self::STATUS_LABELS;
        $selectedCustomerId=$registration->customer_id; $selectedScheduleId=$registration->training_schedule_id;
        return view('registrations.edit',compact('registration','customers','schedules','statusLabels','selectedCustomerId','selectedScheduleId'));
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $validated=$this->validateRegistration($request);
        $customer=Customer::findOrFail($validated['customer_id']);
        $schedule=TrainingSchedule::with(['training','trainer'])->findOrFail($validated['training_schedule_id']);
        $this->guardCustomerAndSchedule($customer,$schedule);
        $registrationCount = $schedule->registrations()->where('status','!=','cancelled')->whereKey('!=',$registration->id)->count();
        if($schedule->capacity!==null && $registrationCount >= $schedule->capacity) return back()->withInput()->with('error','Kapasitas jadwal ini sudah penuh.');
        $oldCustomerId=$registration->customer_id;
        DB::transaction(function() use($registration,$validated,$customer,$schedule,$oldCustomerId){
            $registration->update([
                'customer_id'=>$customer->id,'training_id'=>$schedule->training_id,'training_schedule_id'=>$schedule->id,
                'training_date'=>$schedule->training_date,'status'=>$validated['status'],'amount'=>$validated['amount'] ?? $schedule->training->price,
                'registration_number'=>$validated['registration_number'] ?? $registration->registration_number,'notes'=>$validated['notes'] ?? null,
            ]);
            Activity::create(['customer_id'=>$customer->id,'user_id'=>auth()->id(),'type'=>'note','subject'=>'Pendaftaran pelatihan diperbarui','description'=>'Pendaftaran '.$registration->registration_number.' diperbarui ke jadwal '.$schedule->training->name.' pada '.$schedule->training_date->format('d M Y').'.','activity_at'=>now()]);
            if((int)$oldCustomerId !== (int)$customer->id) $this->syncCustomerLifecycle(Customer::findOrFail($oldCustomerId));
            $this->syncCustomerLifecycle($customer);
        });
        return redirect()->route('registrations.index')->with('success','Pendaftaran pelatihan berhasil diperbarui.');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $customer=$registration->customer;
        DB::transaction(function() use($registration,$customer){
            $trainingName=optional($registration->training)->name; $number=$registration->registration_number;
            $registration->delete();
            Activity::create(['customer_id'=>$customer->id,'user_id'=>auth()->id(),'type'=>'note','subject'=>'Pendaftaran pelatihan dihapus','description'=>sprintf('Pendaftaran %s%s dihapus dari histori customer.',$number,$trainingName?' ('.$trainingName.')':''),'activity_at'=>now()]);
            $this->syncCustomerLifecycle($customer);
        });
        return redirect()->route('registrations.index')->with('success','Data pendaftaran berhasil dihapus.');
    }

    private function guardCustomerAndSchedule(Customer $customer, TrainingSchedule $schedule): void
    {
        if (!in_array($customer->status,['active','repeat'],true)) abort(422,'Hanya customer Active atau Repeat Customer yang dapat didaftarkan ke pelatihan.');
        if (!in_array($schedule->status,['scheduled','ongoing'],true)) abort(422,'Jadwal ini tidak menerima pendaftaran.');
    }

    private function syncCustomerLifecycle(Customer $customer): void
    {
        if (!in_array($customer->status,['active','repeat'],true)) return;
        $completedCount=$customer->registrations()->where('status','completed')->count();
        $newStatus=$completedCount>=2?'repeat':'active';
        if($customer->status===$newStatus)return;
        $oldStatus=$customer->status;
        $customer->update(['status'=>$newStatus]);
        Activity::create(['customer_id'=>$customer->id,'user_id'=>auth()->id(),'type'=>'note','subject'=>'Status customer diperbarui otomatis','description'=>sprintf('Status customer berubah dari %s menjadi %s berdasarkan %d training dengan status Completed.',ucfirst($oldStatus),$newStatus==='repeat'?'Repeat Customer':'Active',$completedCount),'activity_at'=>now()]);
    }

    private function validateRegistration(Request $request): array
    {
        return $request->validate([
            'customer_id'=>['required','integer','exists:customers,id'],
            'training_schedule_id'=>['required','integer','exists:training_schedules,id'],
            'status'=>['required','in:registered,confirmed,completed,cancelled'],
            'amount'=>['nullable','numeric','min:0'],
            'registration_number'=>['nullable','string','max:100'],
            'notes'=>['nullable','string'],
        ],[
            'customer_id.required'=>'Customer wajib dipilih.','training_schedule_id.required'=>'Jadwal pelatihan wajib dipilih.','status.in'=>'Status pendaftaran tidak valid.','amount.numeric'=>'Amount harus berupa angka.','amount.min'=>'Amount tidak boleh negatif.',
        ]);
    }
}
