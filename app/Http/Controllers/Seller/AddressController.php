<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AddressController extends Controller
{
    private function getSellerId()
    {
        $user = auth()->user();
        $membership = $user->memberOf()->first();
        if ($membership) {
            return $membership->seller_id;
        }
        if ($user->isSeller()) {
            return $user->id;
        }
        abort(403, 'Akses tidak diizinkan.');
    }
    private function getSeller(): User
    {
        $sellerId = $this->getSellerId();
        return User::findOrFail($sellerId);
    }
    public function index()
    {
        $seller = $this->getSeller();
        $addresses = $seller->addresses()
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
        $seller = $this->getSeller();
        $data = $request->all();
        $data['user_id'] = $seller->id;
        $userAddressCount = $seller->addresses()->count();
        if ($userAddressCount === 0 || $request->is_default) {
            $seller->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }
        UserAddress::create($data);
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan!');
    }
    public function show(UserAddress $address)
    {
        $sellerId = $this->getSellerId();
        abort_unless($address->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke alamat ini.');
        return view('seller.addresses.show', compact('address'));
    }
    public function edit(UserAddress $address)
    {
        $sellerId = $this->getSellerId();
        abort_unless($address->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke alamat ini.');
        return view('seller.addresses.edit', compact('address'));
    }
    public function update(Request $request, UserAddress $address)
    {
        $sellerId = $this->getSellerId();
        abort_unless($address->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke alamat ini.');
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
        $seller = $this->getSeller();
        if ($request->is_default) {
            $seller->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        $address->update($request->all());
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui!');
    }
    public function destroy(UserAddress $address)
    {
        $sellerId = $this->getSellerId();
        abort_unless($address->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke alamat ini.');
        if ($address->is_default) {
            $otherAddress = $this->getSeller()->addresses()
                ->where('id', '!=', $address->id)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($otherAddress) {
                $otherAddress->update(['is_default' => true]);
            }
        }
        $address->delete();
        return redirect()->route('seller.addresses.index')
            ->with('success', 'Alamat berhasil dihapus!');
    }
    public function setDefault(UserAddress $address)
    {
        $sellerId = $this->getSellerId();
        abort_unless($address->user_id === $sellerId, 403, 'Anda tidak memiliki akses ke alamat ini.');
        $this->getSeller()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        return redirect()->back()
            ->with('success', 'Alamat default berhasil diperbarui!');
    }
    public function getAddresses()
    {
        $seller = $this->getSeller();
        $addresses = $seller->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'label', 'name', 'phone', 'city', 'province', 'postal_code', 'address', 'latitude', 'longitude', 'is_default']);
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }
}
