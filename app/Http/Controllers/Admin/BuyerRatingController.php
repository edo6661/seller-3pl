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
        // Logic untuk menampilkan form create buyer rating
    }

    public function store(Request $request)
    {
        // Logic untuk menyimpan buyer rating baru
    }

    public function show($id)
    {
        // Logic untuk menampilkan detail buyer rating
    }

    public function edit($id)
    {
        // Logic untuk menampilkan form edit buyer rating
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