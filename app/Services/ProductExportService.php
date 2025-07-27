<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Collection;
use XLSXWriter;
class ProductExportService
{
    public function exportToExcel(Collection $products, string $sellerName)
    {
        $fileName = 'produk_' . str_replace(' ', '_', strtolower($sellerName)) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $headers = [
            'No' => 'integer',
            'Nama Produk' => 'string',
            'Deskripsi' => 'string',
            'Berat per Pcs (kg)' => '0.00', 
            'Harga (Rp)' => 'Rp #,##0',   
            'Status' => 'string',
            'Tanggal Dibuat' => 'DD/MM/YYYY HH:MM', 
            'Tanggal Diperbarui' => 'DD/MM/YYYY HH:MM' 
        ];
        $data = [];
        $no = 1;
        foreach ($products as $product) {
            $data[] = [
                $no++,
                $product->name,
                $product->description ?? '-',
                $product->weight_per_pcs, 
                $product->price,          
                $product->is_active ? 'Aktif' : 'Nonaktif',
                $product->created_at->format('Y-m-d H:i:s'), 
                $product->updated_at->format('Y-m-d H:i:s')  
            ];
        }
        $writer = new XLSXWriter();
        $writer->setAuthor($sellerName);
        $writer->setTitle('Data Produk - ' . $sellerName);
        $writer->setSubject('Export Data Produk');
        $writer->setDescription('Data produk yang diekspor dari sistem');
        $sheetName = 'Data Produk';
        $writer->writeSheetHeader($sheetName, $headers, [
            'widths' => [5, 30, 40, 15, 20, 10, 20, 20], 
            'auto_filter' => true, 
            'freeze_rows' => 1,    
            'font-style' => 'bold'
        ]);
        foreach ($data as $row) {
            $writer->writeSheetRow($sheetName, $row);
        }
        $tempFile = tempnam(sys_get_temp_dir(), 'products_export');
        $writer->writeToFile($tempFile);
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
