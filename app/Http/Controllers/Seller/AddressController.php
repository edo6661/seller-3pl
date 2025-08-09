<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('seller.addresses.index', compact('addresses'));
    }
    public function create()
    {
        return view('seller.addresses.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $userAddressCount = auth()->user()->addresses()->count();
        if ($userAddressCount === 0) {
            $data['is_default'] = true;
        }
        UserAddress::create($data);
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan!');
    }
    public function show(UserAddress $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        return view('seller.addresses.show', compact('address'));
    }
    public function edit(UserAddress $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        return view('seller.addresses.edit', compact('address'));
    }
    public function update(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);
        $address->update($request->all());
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui!');
    }
    public function destroy(UserAddress $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        if ($address->is_default) {
            $otherAddresses = auth()->user()->addresses()
                ->where('id', '!=', $address->id)
                ->exists();
            if ($otherAddresses) {
                auth()->user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->orderBy('created_at', 'desc')
                    ->first()
                    ->update(['is_default' => true]);
            }
        }
        $address->delete();
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil dihapus!');
    }
    public function setDefault(UserAddress $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return redirect()->back()
            ->with('success', 'Alamat default berhasil diperbarui!');
    }
    public function getAddresses()
    {
        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'label', 'name', 'phone', 'city', 'province', 'postal_code', 'address', 'latitude', 'longitude', 'is_default']);
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }
}