<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Trade;
use App\Models\TradingAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TradeController extends Controller
{
    /**
     * Display all trades for a given account with advanced filters.
     */
    public function index(Request $request)
    {
        $accounts = Auth::user()->tradingAccounts()->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);
        $account = $accounts->find($selectedAccountId);

        $tags = Tag::orderBy('type')->orderBy('name')->get();

        if (!$account) {
            return view('trades.index', [
                'accounts'          => $accounts,
                'account'           => null,
                'trades'            => collect(),
                'selectedAccountId' => null,
                'tags'              => $tags,
                'filters'           => $request->all(),
            ]);
        }

        $query = $account->trades()->with('tags');

        // Filter Pair / Search
        if ($request->filled('search')) {
            $search = strtoupper(trim($request->input('search')));
            $query->where('pair', 'like', "%{$search}%");
        }

        // Filter Direction
        if ($request->filled('direction') && in_array($request->direction, ['long', 'short'])) {
            $query->where('direction', $request->direction);
        }

        // Filter Status
        if ($request->filled('status') && in_array($request->status, ['open', 'closed'])) {
            $query->where('status', $request->status);
        }

        // Filter by Tag
        if ($request->filled('tag_id')) {
            $tagId = $request->input('tag_id');
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        // Filter Date From
        if ($request->filled('date_from')) {
            $query->whereDate('entry_time', '>=', $request->date_from);
        }

        // Filter Date To
        if ($request->filled('date_to')) {
            $query->whereDate('entry_time', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'entry_time');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, ['entry_time', 'exit_time', 'profit_loss', 'quantity', 'pair'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('entry_time');
        }

        $trades = $query->paginate(15)->withQueryString();

        return view('trades.index', compact('accounts', 'account', 'trades', 'selectedAccountId', 'tags'));
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

        return redirect()->route('trades.show', $trade)
            ->with('success', 'Trade berhasil ditambahkan!');
    }

    /**
     * Show trade details.
     */
    public function show(Trade $trade)
    {
        $this->authorize($trade);
        $trade->load(['tags', 'tradingAccount']);

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

        return redirect()->route('trades.show', $trade)
            ->with('success', 'Trade berhasil diperbarui!');
    }

    /**
     * Quick close an open trade.
     */
    public function close(Request $request, Trade $trade)
    {
        $this->authorize($trade);

        if ($trade->status === 'closed') {
            return redirect()->back()->with('info', 'Trade ini sudah closed.');
        }

        $validated = $request->validate([
            'exit_price' => 'required|numeric|min:0.00000001',
            'exit_time'  => 'nullable|date',
            'close_note' => 'nullable|string|max:1000',
        ]);

        $exitTime = $validated['exit_time'] ? Carbon::parse($validated['exit_time']) : now();

        $trade->exit_price = $validated['exit_price'];
        $trade->exit_time = $exitTime;
        $trade->status = 'closed';

        if (!empty($validated['close_note'])) {
            $trade->notes = ($trade->notes ? $trade->notes . "\n\n[Closed: " . $exitTime->format('d M Y H:i') . "] " : '') . $validated['close_note'];
        }

        $trade->calculateProfitLoss();
        $trade->save();

        return redirect()->route('trades.show', $trade)
            ->with('success', 'Trade berhasil di-close dengan hasil ' . $trade->formatted_pnl);
    }

    /**
     * Delete a trade.
     */
    public function destroy(Trade $trade)
    {
        $this->authorize($trade);
        $accountId = $trade->trading_account_id;

        if ($trade->screenshot_path) {
            Storage::disk('public')->delete($trade->screenshot_path);
        }

        $trade->delete();

        return redirect()->route('trades.index', ['account_id' => $accountId])
            ->with('success', 'Trade berhasil dihapus.');
    }

    /**
     * Export trades to CSV format.
     */
    public function export(Request $request): StreamedResponse
    {
        $accounts = Auth::user()->tradingAccounts()->get();
        $selectedAccountId = $request->get('account_id', $accounts->first()?->id);
        $account = $accounts->find($selectedAccountId);

        if (!$account) {
            abort(404, 'Akun tidak ditemukan.');
        }

        $query = $account->trades()->with('tags');

        if ($request->filled('search')) {
            $search = strtoupper(trim($request->input('search')));
            $query->where('pair', 'like', "%{$search}%");
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tag_id')) {
            $tagId = $request->input('tag_id');
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('entry_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_time', '<=', $request->date_to);
        }

        $trades = $query->orderBy('entry_time', 'asc')->get();

        $filename = 'trading_journal_' . str_replace(' ', '_', strtolower($account->account_name)) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->streamDownload(function () use ($trades, $account) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($output, [
                'ID',
                'Akun Trading',
                'Pair',
                'Arah',
                'Status',
                'Quantity',
                'Entry Price',
                'Exit Price',
                'Stop Loss',
                'Take Profit',
                'R:R',
                'Entry Time',
                'Exit Time',
                'Durasi',
                'Net P/L (' . $account->currency . ')',
                'Net P/L (%)',
                'Tags',
                'Catatan',
            ]);

            foreach ($trades as $t) {
                $tagsString = $t->tags->pluck('name')->implode(', ');

                fputcsv($output, [
                    $t->id,
                    $account->account_name,
                    $t->pair,
                    strtoupper($t->direction),
                    strtoupper($t->status),
                    $t->quantity,
                    $t->entry_price,
                    $t->exit_price ?? '-',
                    $t->stop_loss ?? '-',
                    $t->take_profit ?? '-',
                    $t->risk_reward ? '1:' . $t->risk_reward : '-',
                    $t->entry_time ? $t->entry_time->format('Y-m-d H:i:s') : '-',
                    $t->exit_time ? $t->exit_time->format('Y-m-d H:i:s') : '-',
                    $t->duration ?? '-',
                    $t->profit_loss !== null ? number_format($t->profit_loss, 4, '.', '') : '-',
                    $t->profit_loss_percent !== null ? number_format($t->profit_loss_percent, 2, '.', '') . '%' : '-',
                    $tagsString,
                    str_replace(["\r", "\n"], ' ', $t->notes ?? ''),
                ]);
            }

            fclose($output);
        }, $filename, $headers);
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
