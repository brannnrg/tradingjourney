<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Auth::user()->tradingAccounts()->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);
        $account = $accounts->find($selectedAccountId);

        if (!$account) {
            return view('dashboard', [
                'accounts'            => $accounts,
                'account'             => null,
                'equityCurve'         => ['labels' => [], 'data' => []],
                'pairPerformance'     => [],
                'strategyPerformance' => [],
                'dailyPnl'            => [],
                'recentTrades'        => collect(),
                'openTrades'          => collect(),
                'selectedAccountId'   => null,
            ]);
        }

        $recentTrades = $account->trades()
            ->with('tags')
            ->orderByDesc('entry_time')
            ->limit(8)
            ->get();

        $openTrades = $account->openTrades()
            ->with('tags')
            ->orderByDesc('entry_time')
            ->get();

        return view('dashboard', [
            'accounts'            => $accounts,
            'account'             => $account,
            'equityCurve'         => $account->equity_curve_data,
            'pairPerformance'     => $account->pair_performance,
            'strategyPerformance' => $account->strategy_performance,
            'dailyPnl'            => $account->daily_pnl,
            'recentTrades'        => $recentTrades,
            'openTrades'          => $openTrades,
            'selectedAccountId'   => $selectedAccountId,
        ]);
    }
}
