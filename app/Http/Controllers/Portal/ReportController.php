<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import kelas DB
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\FinancialTransaction;
use App\Models\Invoice;
use Carbon\Carbon;


class ReportController extends Controller
{ 

    public function sendReport(Request $request)
    {
        // Validasi request
        $request->validate([
            'reportType' => 'required|in:daily,monthly,yearly',
        ]);

        // Inisialisasi variabel tanggal awal dan akhir
        $startDate = null;
        $endDate = null;

        // Tentukan rentang tanggal berdasarkan jenis laporan
        switch ($request->reportType) {
            case 'daily':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $title = 'Harian';
                $starting       = Carbon::createFromDate(2023, 12, 1);
                $yesterday      = $startDate->copy()->subDay();
                break;        
            case 'monthly':
                $startDate      = Carbon::now()->startOfMonth()->startOfDay();
                $endDate        = Carbon::now()->endOfMonth()->endOfDay();
                $title          = 'Bulanan';
                $starting       = Carbon::createFromDate(2023, 12, 1);
                $yesterday      = $startDate->copy()->subDay();
                break;
            case 'yearly':
                $startDate      = Carbon::now()->startOfYear()->startOfDay();
                $endDate        = Carbon::now()->endOfYear()->endOfDay();
                $title          = 'Tahunan';
                $starting       = Carbon::createFromDate(2023, 12, 1);
                $yesterday      = $startDate->copy()->subDay();
                break;
            default:
                // Jika jenis laporan tidak valid, kembalikan respon error
                return response()->json(['error' => 'Invalid report type.'], 400);
        }
     
        // $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->get();
        $invoices               = Invoice::whereBetween('created_at', [$startDate, $endDate])->where('total_amount', '!=', 0.00)->get();    
        $unpaidInvoices         = Invoice::whereBetween('created_at', [$startDate, $endDate])->where('total_amount', '!=', 0.00)->where('panjar_amount', 0.00)->count();  
        $partiallyPaidInvoices  = Invoice::whereBetween('created_at', [$startDate, $endDate])->where('total_amount', '>', DB::raw('panjar_amount'))->where('panjar_amount', '!=', 0.00)->count();
        $fullyPaidInvoices      = Invoice::whereBetween('created_at', [$startDate, $endDate])->where('total_amount', '<=', DB::raw('panjar_amount'))->where('panjar_amount', '!=', 0.00)->count();    
        $totalPanjarAmount      = $invoices->sum('panjar_amount');

        // Keuangan
        $totalPanjarAmount  = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where('source_receiver', 'Tagihan Invoice')->sum('transaction_amount');
        $totalOutcomeAmount = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where(function ($query) {$query->whereIn('status', [4, 5]);})->sum('transaction_amount');
        $setoranKas         = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where(function ($query) {$query->whereIn('status', [6]);})->sum('transaction_amount');
        $topupKas           = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])->where(function ($query) {$query->whereIn('status', [7]);})->sum('transaction_amount');
        
        //Geting Saldo Sisa
            $incomeForSisa      = FinancialTransaction::getIncomeRangeAmount($starting, $yesterday);
            $outcomeForSisa     = FinancialTransaction::getRangeOutTransonAmount($starting, $yesterday);
            $topupForSisa       = FinancialTransaction::getWeeklyTopUpAmount($starting, $yesterday);
            $setorKasForSisa    = FinancialTransaction::getWeeklySetorKasAmount($starting, $yesterday);
            $sisaBefore         = $incomeForSisa+$topupForSisa-$outcomeForSisa-$setorKasForSisa;          
        //!Geting Saldo Sisa

        $sisaHariIni        = $sisaBefore+$totalPanjarAmount+$topupKas-$totalOutcomeAmount-$setoranKas;

        // dd($startDate, $endDate, $title, $totalPanjarAmount);
        $startDateFormatted = urlencode($startDate->format('Y-m-d'));
        $endDateFormatted = urlencode($endDate->format('Y-m-d'));
        
        $baseUrl = url()->to('/');

        $message            = "*Laporan $title " . Carbon::parse($startDate)->isoFormat('dddd, DD MMMM YYYY') . " - " . Carbon::parse($endDate)->isoFormat('dddd, DD MMMM YYYY') . "*\n\n";
        $message           .= '*Invoice:*' . "\n";
        $message           .= 'Invoice: ' . $invoices->count() . "\n";
        $message           .= 'Belum Bayar: ' . $unpaidInvoices . "\n";
        $message           .= 'Sudah Panjar: ' . $partiallyPaidInvoices . "\n";
        $message           .= 'Sudah Lunas: ' . $fullyPaidInvoices . "\n\n";
        $message           .= '*Keuangan:*' . "\n";
        $message           .= 'Pemasukan: *Rp.' . number_format($totalPanjarAmount, 0, ',', '.') . ',-*' . "\n";
        $message           .= 'Pengeluaran: *Rp.' . number_format($totalOutcomeAmount, 0, ',', '.') . ',-*' . "\n";
        $message           .= 'Top Up Kas: *Rp.' . number_format($topupKas, 0, ',', '.') . ',-*' . "\n";
        $message           .= 'Setoran Kas: *Rp.' . number_format($setoranKas, 0, ',', '.') . ',-*' . "\n\n";
        $message           .= 'Sisa Kas: *Rp.' . number_format($sisaHariIni, 0, ',', '.') . ',-*' . "\n";
        $message           .= "\nDownload Laporan:\n";
        $message           .= "*$baseUrl/report/?startDate=$startDateFormatted&endDate=$endDateFormatted*\n";
        $message           .= "\n*Laporan Dikirim Secara Semi Otomatis*\n";

        // URL untuk redirect ke wa.me
        $waUrl = 'https://wa.me/?text=' . urlencode($message);

        // Redirect pengguna ke wa.me dalam tab atau jendela baru
        return "<script>window.open('$waUrl', '_blank');window.location.href = '" . redirect()->back()->getTargetUrl() . "';</script>";
    }
    
    public function cariReport(Request $request)
    {
        // Validasi request
        $request->validate([
            'reportType' => 'required|in:daily,monthly,yearly',
        ]);

        // Inisialisasi variabel tanggal awal dan akhir
        $startDate = null;
        $endDate = null;
        $redirectUrl = null;

        // Tentukan rentang tanggal berdasarkan jenis laporan dan atur URL redirect
        switch ($request->reportType) {
            case 'daily':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $redirectUrl = route('laporan'); // Ganti 'laporan.index' dengan nama rute yang sesuai
                break;        
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                $redirectUrl = route('laporan.bulanan'); // Ganti 'laporan.bulanan' dengan nama rute yang sesuai
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear()->startOfDay();
                $endDate = Carbon::now()->endOfYear()->endOfDay();
                $redirectUrl = route('laporan.tahunan'); // Ganti 'laporan.tahunan' dengan nama rute yang sesuai
                break;
            default:
                // Jika jenis laporan tidak valid, kembalikan respon error
                return response()->json(['error' => 'Invalid report type.'], 400);
        }

        $response = [
            'title' => 'Berhasil',
            'success' => true,
            'message' => 'Laporan Berhasil Dimuat.',
        ];
        
        return redirect($redirectUrl)->with('response', $response);        
    }



}
