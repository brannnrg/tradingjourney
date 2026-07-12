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

        Auth::user()->tradingAccounts()->create($validated);

        return redirect()->route('trading-accounts.index')
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
