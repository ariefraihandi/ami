<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class DashboardController extends Controller
{
    public function showPortalPage()
{
    $sevenDaysAgo = now()->subDays(6)->toDateString();
    $today = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();

    $incomeToday        = DB::table('financial_transactions')->whereDate('transaction_date', '=', $today)->whereIn('status', [1, 2, 3])->sum('transaction_amount');
    $outcomeToday       = DB::table('financial_transactions')->whereDate('transaction_date', '=', $today)->whereIn('status', [4, 5])->sum('transaction_amount');
    $dataIncomeYes      = DB::table('financial_transactions')->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(transaction_amount) as total_amount'))->whereDate('transaction_date', '=', $yesterday)->whereIn('status', [1, 2, 3])->groupBy('date')->orderBy('date', 'asc')->get();
    $dataOutcomeYes     = DB::table('financial_transactions')->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(transaction_amount) as total_amount'))->whereDate('transaction_date', '=', $yesterday)->whereIn('status', [4, 5])->groupBy('date')->orderBy('date', 'asc')->get();

    $percentageToday    = ($dataIncomeYes->sum('total_amount') > 0) ? (($incomeToday - $dataIncomeYes->sum('total_amount')) / $dataIncomeYes->sum('total_amount')) * 100 : 0;
    $percentageOutToday = ($dataOutcomeYes->sum('total_amount') > 0) ? (($outcomeToday - $dataOutcomeYes->sum('total_amount')) / $dataOutcomeYes->sum('total_amount')) * 100 : 0;
    $incomeWeekData     = DB::table('financial_transactions')->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(transaction_amount) as total_amount'))->whereDate('transaction_date', '>=', $sevenDaysAgo)->whereDate('transaction_date', '<=', $today)->whereIn('status', [1, 2, 3])->groupBy('date')->orderBy('date', 'asc')->get();
    $outcomeWeekData    = DB::table('financial_transactions')->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(transaction_amount) as total_amount'))->whereDate('transaction_date', '>=', $sevenDaysAgo)->whereDate('transaction_date', '<=', $today)->whereNotIn('status', [4, 5])->groupBy('date')->orderBy('date', 'asc')->get();
    
    // kas
    $qincomeTotal       = FinancialTransaction::whereIn('status', [6])->get();
    $qTopup             = FinancialTransaction::whereIn('status', [7])->get();
    $totalKas           = $qincomeTotal->sum('transaction_amount') - $qTopup->sum('transaction_amount');
    
    $qincomeTotalYes    = FinancialTransaction::whereDate('transaction_date', '<=', $yesterday)->whereIn('status', [6])->get();
    $qTopupYes          = FinancialTransaction::whereDate('transaction_date', '<=', $yesterday)->whereIn('status', [7])->get();
    $totalKasYes        = $qincomeTotalYes->sum('transaction_amount') - $qTopupYes->sum('transaction_amount');    
    $percentageKas      = ($totalKas - $totalKasYes) / $totalKasYes * 100;
    // !kas
    
    // Pengeluaran
    $qOutcomeTotal      = FinancialTransaction::whereIn('status', [4, 5])->get();
    $qOutcomeTotalYes   = FinancialTransaction::whereDate('transaction_date', '<=', $yesterday)->whereIn('status', [4, 5])->get();
    $outcomeTotal       = $qOutcomeTotal->sum('transaction_amount');
    $percentageOut      = ($qOutcomeTotal->sum('transaction_amount') - $qOutcomeTotalYes->sum('transaction_amount')) / $qOutcomeTotalYes->sum('transaction_amount') * 100;
    // !Pengeluaran



    $incomeSeriesData = $incomeWeekData->map(function ($item) {
        return [
            'date'          => $item->date,
            'total_amount'  => (float) $item->total_amount,
        ];
    });

    $outcomeSeriesData = $outcomeWeekData->map(function ($item) {
        return [
            'date'          => $item->date,
            'total_amount'  => (float) $item->total_amount,
        ];
    });

    $data = [
        'title'                 => 'Customer List',
        'subtitle'              => 'Dashboard',
        'incomeToday'           => $this->formatCurrency($incomeToday),
        'outcomeToday'          => $this->formatCurrency($outcomeToday),
        'seriesData'            => $incomeSeriesData->toArray(),
        'outcomeSeriesData'     => $outcomeSeriesData->toArray(),
        'categories'            => $incomeSeriesData->pluck('date')->toArray(),
        'outCategories'         => $outcomeSeriesData->pluck('date')->toArray(),
        'percentageIncrease'    => $percentageToday,
        'percentageOutcomeToday'=> $percentageOutToday,
        
        'totalKas'              => $this->formatCurrency($totalKas),
        'percentageKas'         => $percentageKas,
        'outcomeTotal'          => $this->formatCurrency($outcomeTotal),
        'percentageOut'         => $percentageOut,
    ];

    return view('Konten/Portal/dashboard', $data);
}


    private function formatCurrency($value)
    {
        $absValue = abs($value);

        if ($absValue >= 1000000000) {
            return 'Rp. ' . number_format($value / 1000000000) . ' m';
        } elseif ($absValue >= 1000000) {
            return 'Rp. ' . number_format($value / 1000000) . ' jt';
        } elseif ($absValue >= 1000) {
            return 'Rp. ' . number_format($value / 1000) . ' rb';
        } else {
            return 'Rp. ' . number_format($value);
        }
    }

}
