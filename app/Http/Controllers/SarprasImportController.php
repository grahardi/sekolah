<?php

namespace App\Http\Controllers;

use App\Models\SarprasAsset;
use App\Models\SarprasCategory;
use App\Models\SarprasLocation;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SarprasImportController extends Controller
{
    public function showImport()
    {
        return view('sarpras.import.form');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Kode Barang', 'Nama Barang', 'Kategori', 'Lokasi', 'Tahun Pembelian', 'Status', 'Keterangan'],
            ['000001', 'Kursi Kelas', 'Furnitur', 'Ruang 7A', 2024, 'baik', 'Contoh baris - hapus sebelum upload'],
        ]);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $path = storage_path('app/template-import-sarpras.xlsx');
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls|max:5120']);

        $sekolahId = auth()->user()->sekolah_id;
        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();

        $imported = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $kodeBarang = trim((string) $sheet->getCell('A' . $row)->getValue());
            $namaBarang = trim((string) $sheet->getCell('B' . $row)->getValue());
            $namaKategori = trim((string) $sheet->getCell('C' . $row)->getValue());
            $namaLokasi = trim((string) $sheet->getCell('D' . $row)->getValue());
            $tahun = $sheet->getCell('E' . $row)->getValue();
            $status = trim((string) $sheet->getCell('F' . $row)->getValue()) ?: 'baik';
            $keterangan = trim((string) $sheet->getCell('G' . $row)->getValue());

            if ($namaBarang === '') continue;

            if (! $kodeBarang) {
                $kodeBarang = SarprasAsset::generateNextKodeBarang($sekolahId);
            }

            $category = $namaKategori ? SarprasCategory::firstOrCreate(['sekolah_id' => $sekolahId, 'name' => $namaKategori]) : null;
            $location = $namaLokasi ? SarprasLocation::firstOrCreate(['sekolah_id' => $sekolahId, 'name' => $namaLokasi]) : null;

            SarprasAsset::updateOrCreate(
                ['sekolah_id' => $sekolahId, 'kode_barang' => $kodeBarang],
                [
                    'nama_barang' => $namaBarang,
                    'category_id' => $category?->id,
                    'location_id' => $location?->id,
                    'tahun_pembelian' => is_numeric($tahun) ? (int) $tahun : null,
                    'status' => in_array($status, ['baik', 'rusak', 'dalam_perbaikan']) ? $status : 'baik',
                    'keterangan' => $keterangan ?: null,
                ]
            );

            $imported++;
        }

        return back()->with('success', "{$imported} barang berhasil diimport.");
    }

    public function scanCari(Request $request)
    {
        $kode = $request->input('kode');
        $asset = SarprasAsset::where('kode_barang', $kode)->orWhere('kode_aset', $kode)->first();

        if (! $asset) {
            return back()->with('error', "Barang dengan kode \"{$kode}\" tidak ditemukan.");
        }

        return redirect()->route('sarpras.assets.show', $asset);
    }

    public function scanForm()
    {
        return view('sarpras.scan.form');
    }
}
