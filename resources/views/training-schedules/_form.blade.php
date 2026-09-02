@php
    $selectedTrainingId = old('training_id', $selectedTrainingId ?? ($trainingSchedule?->training_id));
    $selectedTrainerId = old('trainer_id', $trainingSchedule?->trainer_id);
    $selectedStatus = old('status', $trainingSchedule?->status ?? 'scheduled');
@endphp
<form method="POST" action="{{ $action }}" class="space-y-6">
@csrf @if($method!=='POST') @method($method) @endif
@if($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 p-5"><ul class="list-disc space-y-1 pl-5 text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="grid gap-5 md:grid-cols-2">
<div><label class="mb-2 block text-sm font-medium">Pelatihan *</label><select name="training_id" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"><option value="">Pilih pelatihan</option>@foreach($trainings as $training)<option value="{{ $training->id }}" @selected((int)$selectedTrainingId===(int)$training->id)>{{ $training->name }}</option>@endforeach</select></div>
<div><label class="mb-2 block text-sm font-medium">Trainer *</label><select name="trainer_id" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"><option value="">Pilih trainer</option>@foreach($trainers as $trainer)<option value="{{ $trainer->id }}" @selected((int)$selectedTrainerId===(int)$trainer->id)>{{ $trainer->name }}</option>@endforeach</select><p class="mt-2 text-xs text-slate-500">Sistem akan menolak jadwal trainer yang waktunya bentrok.</p></div>
<div><label class="mb-2 block text-sm font-medium">Tanggal *</label><input type="date" name="training_date" required value="{{ old('training_date',$trainingSchedule?->training_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div>
<div class="grid grid-cols-2 gap-3"><div><label class="mb-2 block text-sm font-medium">Mulai *</label><input type="time" name="start_time" required value="{{ old('start_time',$trainingSchedule?->start_time ? substr($trainingSchedule->start_time,0,5) : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div><div><label class="mb-2 block text-sm font-medium">Selesai *</label><input type="time" name="end_time" required value="{{ old('end_time',$trainingSchedule?->end_time ? substr($trainingSchedule->end_time,0,5) : '') }}" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div></div>
<div><label class="mb-2 block text-sm font-medium">Lokasi</label><input name="location" value="{{ old('location',$trainingSchedule?->location) }}" placeholder="Ruang / alamat / online" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div>
<div><label class="mb-2 block text-sm font-medium">Kapasitas</label><input type="number" min="1" name="capacity" value="{{ old('capacity',$trainingSchedule?->capacity) }}" placeholder="Contoh: 25" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div>
<div><label class="mb-2 block text-sm font-medium">Status *</label><select name="status" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">@foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedStatus===$value)>{{ $label }}</option>@endforeach</select></div>
<div><label class="mb-2 block text-sm font-medium">Catatan</label><textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">{{ old('notes',$trainingSchedule?->notes) }}</textarea></div>
</div></div>
<div class="flex justify-end gap-2"><a href="{{ route('training-schedules.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm">Batal</a><button class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white">{{ $buttonLabel }}</button></div>
</form>
