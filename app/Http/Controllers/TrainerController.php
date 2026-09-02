<?php
namespace App\Http\Controllers;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class TrainerController extends Controller {
    private const SORTS=['name','email','phone','is_active','created_at'];
    public function index(Request $request): View {
        $sort=in_array($request->string('sort')->toString(),self::SORTS,true)?$request->string('sort')->toString():'name';
        $direction=in_array(strtolower($request->string('direction')->toString()),['asc','desc'],true)?strtolower($request->string('direction')->toString()):'asc';
        $options=[25,50,100,200,500]; $per=(int)$request->integer('per_page',50); if(!in_array($per,$options,true))$per=50;
        $q=Trainer::query(); if($request->filled('search')){$x=trim($request->string('search')->toString());$q->where(fn($w)=>$w->where('name','like',"%$x%")->orWhere('email','like',"%$x%")->orWhere('phone','like',"%$x%"));}
        $trainers=$q->orderBy($sort,$direction)->paginate($per)->withQueryString();
        return view('trainers.index',compact('trainers','sort','direction','per','options'));
    }
    public function create(): View { return view('trainers.create'); }
    public function store(Request $request): RedirectResponse {
        $v=$request->validate(['name'=>'required|string|max:255','email'=>'nullable|email|max:255','phone'=>'nullable|string|max:50','notes'=>'nullable|string','is_active'=>'nullable|boolean'],['name.required'=>'Nama trainer wajib diisi.','email.email'=>'Email trainer tidak valid.']);
        Trainer::create([...$v,'name'=>trim($v['name']),'is_active'=>$request->boolean('is_active')]);
        return redirect()->route('trainers.index')->with('success','Trainer berhasil ditambahkan.');
    }
    public function edit(Trainer $trainer): View { return view('trainers.edit',compact('trainer')); }
    public function update(Request $request,Trainer $trainer): RedirectResponse {
        $v=$request->validate(['name'=>'required|string|max:255','email'=>'nullable|email|max:255','phone'=>'nullable|string|max:50','notes'=>'nullable|string','is_active'=>'nullable|boolean'],['name.required'=>'Nama trainer wajib diisi.','email.email'=>'Email trainer tidak valid.']);
        $trainer->update([...$v,'name'=>trim($v['name']),'is_active'=>$request->boolean('is_active')]);
        return redirect()->route('trainers.index')->with('success','Trainer berhasil diperbarui.');
    }
    public function destroy(Trainer $trainer): RedirectResponse {
        if($trainer->schedules()->exists()) return back()->with('error','Trainer tidak dapat dihapus karena sudah memiliki jadwal.');
        $trainer->delete(); return redirect()->route('trainers.index')->with('success','Trainer berhasil dihapus.');
    }
}
