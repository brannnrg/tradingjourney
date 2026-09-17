<?php

namespace App\Http\Controllers;

use App\Models\TradingAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingAccountController extends Controller
{
    /**
     * Display a listing of the user's trading accounts.
     */
    public function index()
    {
        $accounts = Auth::user()->tradingAccounts()->latest()->get();
        return view('trading-accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new trading account.
     */
    public function create()
    {
        return view('trading-accounts.create');
    }

    /**
     * Store a newly created trading account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name'    => 'required|string|max:255',
            'exchange'        => 'nullable|string|max:255',
            'currency'        => 'required|string|max:10',
            'initial_balance' => 'required|numeric|min:0.00000001',
            'start_date'      => 'required|date',
            'description'     => 'nullable|string|max:1000',
        ]);

        $account = Auth::user()->tradingAccounts()->create($validated);

        return redirect()->route('trading-accounts.show', $account)
            ->with('success', 'Akun trading berhasil dibuat!');
    }

    /**
     * Show a single account's details.
     */
    public function show(TradingAccount $tradingAccount)
    {
        $this->authorize($tradingAccount);

        $tradingAccount->load(['trades' => function ($q) {
            $q->orderByDesc('entry_time');
        }, 'trades.tags']);

        return view('trading-accounts.show', compact('tradingAccount'));
    }

    /**
     * Show report / printable journey summary for the trading account.
     */
    public function report(TradingAccount $tradingAccount)
    {
        $this->authorize($tradingAccount);

        $trades = $tradingAccount->trades()
            ->with('tags')
            ->orderBy('entry_time', 'asc')
            ->get();

        // Monthly breakdown
        $monthly = [];
        foreach ($trades->where('status', 'closed') as $trade) {
            if (!$trade->exit_time) continue;
            $monthKey = $trade->exit_time->format('Y-m');
            $monthLabel = $trade->exit_time->format('F Y');

            if (!isset($monthly[$monthKey])) {
                $monthly[$monthKey] = [
                    'label'  => $monthLabel,
                    'trades' => 0,
                    'wins'   => 0,
                    'losses' => 0,
                    'pnl'    => 0.0,
                ];
            }

            $monthly[$monthKey]['trades']++;
            $monthly[$monthKey]['pnl'] += (float) $trade->profit_loss;
            if ($trade->profit_loss > 0) $monthly[$monthKey]['wins']++;
            if ($trade->profit_loss < 0) $monthly[$monthKey]['losses']++;
        }

        foreach ($monthly as $key => $val) {
            $monthly[$key]['win_rate'] = $val['trades'] > 0
                ? round(($val['wins'] / $val['trades']) * 100, 1)
                : 0;
            $monthly[$key]['pnl'] = round($val['pnl'], 4);
        }

        return view('trading-accounts.report', compact('tradingAccount', 'trades', 'monthly'));
    }

    /**
     * Show the form for editing a trading account.
     */
    public function edit(TradingAccount $tradingAccount)
    {
        $this->authorize($tradingAccount);
        return view('trading-accounts.edit', compact('tradingAccount'));
    }

    /**
     * Update the trading account.
     */
    public function update(Request $request, TradingAccount $tradingAccount)
    {
        $this->authorize($tradingAccount);

        $validated = $request->validate([
            'account_name'    => 'required|string|max:255',
            'exchange'        => 'nullable|string|max:255',
            'currency'        => 'required|string|max:10',
            'initial_balance' => 'required|numeric|min:0.00000001',
            'start_date'      => 'required|date',
            'description'     => 'nullable|string|max:1000',
        ]);

        $tradingAccount->update($validated);

        return redirect()->route('trading-accounts.show', $tradingAccount)
            ->with('success', 'Akun trading berhasil diperbarui!');
    }

    /**
     * Delete the trading account.
     */
    public function destroy(TradingAccount $tradingAccount)
    {
        $this->authorize($tradingAccount);
        $tradingAccount->delete();

        return redirect()->route('trading-accounts.index')
            ->with('success', 'Akun trading berhasil dihapus.');
    }

    /**
     * Simple ownership check — abort 403 if not owned by current user.
     */
    private function authorize(TradingAccount $account): void
    {
        if ($account->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
