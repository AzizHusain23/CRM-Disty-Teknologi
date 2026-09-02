<?php
namespace App\Http\Controllers;
use App\Models\Trainer;
use App\Models\Training;
use App\Models\TrainingSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class TrainingScheduleController extends Controller {
    private const SORTS=['training','trainer','training_date','start_time','end_time','status','created_at'];
    private const STATUS=['scheduled'=>'Terjadwal','ongoing'=>'Berlangsung','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
    public function index(Request $request): View {
        $sort=in_array($request->string('sort')->toString(),self::SORTS,true)?$request->string('sort')->toString():'training_date';
        $direction=in_array(strtolower($request->string('direction')->toString()),['asc','desc'],true)?strtolower($request->string('direction')->toString()):'desc';
        $options=[25,50,100,200,500]; $per=(int)$request->integer('per_page',50); if(!in_array($per,$options,true))$per=50;
        $q=TrainingSchedule::query()->with(['training','trainer'])->withCount(['registrations as active_registrations_count'=>fn($r)=>$r->where('status','!=','cancelled')]);
        if($request->filled('search')){$x=trim($request->string('search')->toString());$q->where(fn($w)=>$w->whereHas('training',fn($t)=>$t->where('name','like',"%$x%"))->orWhereHas('trainer',fn($t)=>$t->where('name','like',"%$x%"))->orWhere('location','like',"%$x%"));}
        if($request->filled('status') && isset(self::STATUS[$request->string('status')->toString()]))$q->where('status',$request->string('status')->toString());
        if($sort==='training')$q->orderBy(Training::query()->select('name')->whereColumn('trainings.id','training_schedules.training_id'),$direction);
        elseif($sort==='trainer')$q->orderBy(Trainer::query()->select('name')->whereColumn('trainers.id','training_schedules.trainer_id'),$direction);
        else $q->orderBy($sort,$direction);
        $schedules=$q->paginate($per)->withQueryString(); $statusLabels=self::STATUS;
        return view('training-schedules.index',compact('schedules','sort','direction','per','options','statusLabels'));
    }
    public function create(Request $request): View {
        $trainings=Training::where('is_active',true)->orderBy('name')->get(); $trainers=Trainer::where('is_active',true)->orderBy('name')->get();
        $selectedTrainingId=$request->integer('training_id')?:null; $statusLabels=self::STATUS;
        return view('training-schedules.create',compact('trainings','trainers','selectedTrainingId','statusLabels'));
    }
    public function store(Request $request): RedirectResponse {
        $v=$this->validated($request); $this->guardConflict($v);
        $schedule=TrainingSchedule::create($v); return redirect()->route('training-schedules.show',$schedule)->with('success','Jadwal pelatihan berhasil dibuat.');
    }
    public function show(TrainingSchedule $trainingSchedule): View {
        $trainingSchedule->load(['training.category','trainer','registrations.customer']); $statusLabels=self::STATUS;
        return view('training-schedules.show',compact('trainingSchedule','statusLabels'));
    }
    public function edit(TrainingSchedule $trainingSchedule): View {
        $trainings=Training::where('is_active',true)->orWhere('id',$trainingSchedule->training_id)->orderBy('name')->get();
        $trainers=Trainer::where('is_active',true)->orWhere('id',$trainingSchedule->trainer_id)->orderBy('name')->get(); $statusLabels=self::STATUS;
        return view('training-schedules.edit',compact('trainingSchedule','trainings','trainers','statusLabels'));
    }
    public function update(Request $request,TrainingSchedule $trainingSchedule): RedirectResponse {
        $v=$this->validated($request); $this->guardConflict($v,$trainingSchedule->id); $trainingSchedule->update($v);
        return redirect()->route('training-schedules.show',$trainingSchedule)->with('success','Jadwal pelatihan berhasil diperbarui.');
    }
    public function destroy(TrainingSchedule $trainingSchedule): RedirectResponse {
        if($trainingSchedule->registrations()->exists()) return back()->with('error','Jadwal tidak dapat dihapus karena sudah memiliki peserta.');
        $trainingSchedule->delete(); return redirect()->route('training-schedules.index')->with('success','Jadwal berhasil dihapus.');
    }
    private function validated(Request $request): array {
        return $request->validate(['training_id'=>'required|exists:trainings,id','trainer_id'=>'required|exists:trainers,id','training_date'=>'required|date','start_time'=>'required|date_format:H:i','end_time'=>'required|date_format:H:i|after:start_time','location'=>'nullable|string|max:255','capacity'=>'nullable|integer|min:1','status'=>'required|in:scheduled,ongoing,completed,cancelled','notes'=>'nullable|string'],['end_time.after'=>'Jam selesai harus lebih besar dari jam mulai.']);
    }
    private function guardConflict(array $v,?int $ignoreId=null): void {
        $start=$v['start_time'];$end=$v['end_time'];$date=$v['training_date'];$trainerId=$v['trainer_id'];
        $q=TrainingSchedule::query()->where('trainer_id',$trainerId)->whereDate('training_date',$date)->where('status','!=','cancelled');
        if($ignoreId)$q->where('id','!=',$ignoreId);
        $conflict=$q->where(fn($w)=>$w->where('start_time','<',$end)->where('end_time','>',$start))->exists();
        if($conflict) throw \Illuminate\Validation\ValidationException::withMessages(['trainer_id'=>'Trainer tersebut sudah memiliki jadwal yang bentrok pada tanggal dan jam tersebut.']);
    }
}
