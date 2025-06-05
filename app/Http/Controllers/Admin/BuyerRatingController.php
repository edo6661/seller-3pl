<?php
// app/Http/Controllers/Admin/BuyerRatingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuyerRatingController extends Controller
{
    public function index()
    {
        return view('admin.buyer-rating.index');
    }

    public function create()
    {
        return view('admin.buyer-rating.create');
    }

    public function store(Request $request)
    {
        // Logic untuk menyimpan buyer rating baru
    }

    public function show($id)
    {
        return view('admin.buyer-rating.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('admin.buyer-rating.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // Logic untuk update buyer rating
    }

    public function destroy($id)
    {
        // Logic untuk menghapus buyer rating
    }
}
