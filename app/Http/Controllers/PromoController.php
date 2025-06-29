<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'Admin']);
    }
    
    public function index()
    {
        $promos = Promo::orderBy('created_at', 'desc')->get();
        return view('admin.promos.index', compact('promos'));
    }
    
    public function create()
    {
        return view('admin.promos.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:promos,code|min:3|max:15',
            'description' => 'required|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at'
        ]);
        
        Promo::create($request->all());
        
        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil dibuat!');
    }
    
    public function edit(Promo $promo)
    {
        return view('admin.promos.edit', compact('promo'));
    }
    
    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'code' => [
                'required',
                'min:3',
                'max:15',
                Rule::unique('promos')->ignore($promo->id)
            ],
            'description' => 'required|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at'
        ]);
        
        $promo->update($request->all());
        
        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil diperbarui!');
    }
    
    public function destroy(Promo $promo)
    {
        $promo->delete();
        
        return redirect()->route('admin.promos.index')
            ->with('success', 'Kode promo berhasil dihapus!');
    }
}
