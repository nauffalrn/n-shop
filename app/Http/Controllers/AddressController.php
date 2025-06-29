<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return view('user.address.address_index', compact('addresses')); 
    }

    public function create()
    {
        return view('user.address.address_create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required_without:district|nullable|string|max:100',
            'district' => 'required_without:city|nullable|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:100'
        ], [
            'city.required_without' => 'Anda harus mengisi setidaknya Kota atau Kabupaten',
            'district.required_without' => 'Anda harus mengisi setidaknya Kota atau Kabupaten',
        ]);

        Address::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country
        ]);

        return Redirect::route('address.index')->with('success', 'Alamat berhasil ditambahkan!');
    }

    public function edit(Address $address)
    {
        if($address->user_id != Auth::id()) {
            return Redirect::route('address.index')->with('error', 'Akses ditolak!');
        }

        return view('user.address.address_edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        // Cek kepemilikan
        if($address->user_id != Auth::id()) {
            return Redirect::route('address.index')->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required_without:district|nullable|string|max:100',
            'district' => 'required_without:city|nullable|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:100'
        ], [
            'city.required_without' => 'Anda harus mengisi setidaknya Kota atau Kabupaten',
            'district.required_without' => 'Anda harus mengisi setidaknya Kota atau Kabupaten',
        ]);

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country
        ]);

        return Redirect::route('address.index')->with('success', 'Alamat berhasil diupdate!');
    }

    public function destroy(Address $address)
    {
        // Cek kepemilikan
        if($address->user_id != Auth::id()) {
            return Redirect::route('address.index')->with('error', 'Akses ditolak!');
        }

        $address->delete();
        return Redirect::route('address.index')->with('success', 'Alamat berhasil dihapus!');
    }
}