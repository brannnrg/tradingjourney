<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Trade;
use App\Models\TradingAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TradeController extends Controller
{
    /**
     * Display all trades for a given account.
     */
    public function index(Request $request)
    {
        $accounts = Auth::user()->tradingAccounts()->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);
        $account = $accounts->find($selectedAccountId);

        $trades = $account
            ? $account->trades()
                ->with('tags')
                ->orderByDesc('entry_time')
                ->paginate(15)
            : collect();

        return view('trades.index', compact('accounts', 'account', 'trades', 'selectedAccountId'));
    }

    /**
     * Show form to create a new trade.
     */
    public function create(Request $request)
    {
        $accounts = Auth::user()->tradingAccounts()->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);

        if ($accounts->isEmpty()) {
            return redirect()->route('trading-accounts.create')
                ->with('info', 'Buat akun trading terlebih dahulu sebelum menambahkan trade.');
        }

        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        return view('trades.create', compact('accounts', 'tags', 'selectedAccountId'));
    }

    /**
     * Store a new trade.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTrade($request);

        // Ensure the account belongs to the current user
        $account = TradingAccount::findOrFail($validated['trading_account_id']);
        if ($account->user_id !== Auth::id()) abort(403);

        // Handle screenshot upload
        if ($request->hasFile('screenshot')) {
            $validated['screenshot_path'] = $request->file('screenshot')
                ->store('screenshots', 'public');
        }

        $trade = Trade::create($validated);

        // Sync tags
        if ($request->has('tags')) {
            $trade->tags()->sync($request->input('tags', []));
        }

        return redirect()->route('trades.index', ['account_id' => $trade->trading_account_id])
            ->with('success', 'Trade berhasil ditambahkan!');
    }

    /**
     * Show trade details.
     */
    public function show(Trade $trade)
    {
        $this->authorize($trade);
        $trade->load('tags', 'tradingAccount');
        return view('trades.show', compact('trade'));
    }

    /**
     * Show form to edit a trade.
     */
    public function edit(Trade $trade)
    {
        $this->authorize($trade);
        $accounts = Auth::user()->tradingAccounts()->get();
        $tags = Tag::orderBy('type')->orderBy('name')->get()->groupBy('type');
        $selectedTags = $trade->tags->pluck('id')->toArray();
        return view('trades.edit', compact('trade', 'accounts', 'tags', 'selectedTags'));
    }

    /**
     * Update a trade.
     */
    public function update(Request $request, Trade $trade)
    {
        $this->authorize($trade);

        $validated = $this->validateTrade($request, $trade);

        // Handle screenshot upload
        if ($request->hasFile('screenshot')) {
            if ($trade->screenshot_path) {
                Storage::disk('public')->delete($trade->screenshot_path);
            }
            $validated['screenshot_path'] = $request->file('screenshot')
                ->store('screenshots', 'public');
        }

        $trade->update($validated);
        $trade->tags()->sync($request->input('tags', []));

        return redirect()->route('trades.index', ['account_id' => $trade->trading_account_id])
            ->with('success', 'Trade berhasil diperbarui!');
    }

    /**
     * Delete a trade.
     */
    public function destroy(Trade $trade)
    {
        $this->authorize($trade);

        if ($trade->screenshot_path) {
            Storage::disk('public')->delete($trade->screenshot_path);
        }

        $trade->delete();

        return redirect()->back()->with('success', 'Trade berhasil dihapus.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    private function validateTrade(Request $request, ?Trade $trade = null): array
    {
        $isClosed = $request->input('status') === 'closed';

        return $request->validate([
            'trading_account_id' => 'required|exists:trading_accounts,id',
            'pair'               => 'required|string|max:20',
            'direction'          => 'required|in:long,short',
            'quantity'           => 'required|numeric|min:0.00000001',
            'entry_price'        => 'required|numeric|min:0',
            'exit_price'         => $isClosed ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'stop_loss'          => 'nullable|numeric|min:0',
            'take_profit'        => 'nullable|numeric|min:0',
            'entry_time'         => 'required|date',
            'exit_time'          => $isClosed ? 'required|date|after_or_equal:entry_time' : 'nullable|date',
            'status'             => 'required|in:open,closed',
            'notes'              => 'nullable|string|max:5000',
            'screenshot'         => 'nullable|image|max:5120',
            'tags'               => 'nullable|array',
            'tags.*'             => 'exists:tags,id',
        ]);
    }

    private function authorize(Trade $trade): void
    {
        if ($trade->tradingAccount->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
