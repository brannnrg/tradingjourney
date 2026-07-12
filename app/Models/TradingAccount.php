<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradingAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_name',
        'exchange',
        'currency',
        'initial_balance',
        'start_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'initial_balance' => 'float',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function closedTrades(): HasMany
    {
        return $this->hasMany(Trade::class)->where('status', 'closed');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────────

    /**
     * Current balance = initial_balance + sum of all closed P/L
     */
    public function getCurrentBalanceAttribute(): float
    {
        $totalPnl = $this->closedTrades()->sum('profit_loss');
        return $this->initial_balance + $totalPnl;
    }

    /**
     * Net profit/loss from all closed trades
     */
    public function getNetProfitAttribute(): float
    {
        return $this->closedTrades()->sum('profit_loss');
    }

    /**
     * Win rate = (winning trades / total closed trades) * 100
     */
    public function getWinRateAttribute(): float
    {
        $total = $this->closedTrades()->count();
        if ($total === 0) return 0;
        $wins = $this->closedTrades()->where('profit_loss', '>', 0)->count();
        return round(($wins / $total) * 100, 2);
    }

    /**
     * Profit factor = sum(profits) / abs(sum(losses))
     */
    public function getProfitFactorAttribute(): float
    {
        $totalProfit = $this->closedTrades()->where('profit_loss', '>', 0)->sum('profit_loss');
        $totalLoss = abs($this->closedTrades()->where('profit_loss', '<', 0)->sum('profit_loss'));
        if ($totalLoss == 0) return $totalProfit > 0 ? 999 : 0;
        return round($totalProfit / $totalLoss, 2);
    }

    /**
     * Total closed trades count
     */
    public function getTotalTradesAttribute(): int
    {
        return $this->closedTrades()->count();
    }

    /**
     * Winning trades count
     */
    public function getWinningTradesAttribute(): int
    {
        return $this->closedTrades()->where('profit_loss', '>', 0)->count();
    }

    /**
     * Losing trades count
     */
    public function getLosingTradesAttribute(): int
    {
        return $this->closedTrades()->where('profit_loss', '<', 0)->count();
    }

    /**
     * Equity curve data: cumulative balance after each trade (for Chart.js)
     */
    public function getEquityCurveDataAttribute(): array
    {
        $trades = $this->closedTrades()
            ->orderBy('exit_time')
            ->get(['exit_time', 'profit_loss', 'pair']);

        $balance = $this->initial_balance;
        $labels = ['Start'];
        $data = [$balance];

        foreach ($trades as $trade) {
            $balance += $trade->profit_loss;
            $labels[] = $trade->exit_time
                ? $trade->exit_time->format('d M H:i')
                : $trade->pair;
            $data[] = round($balance, 4);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Performance per pair
     */
    public function getPairPerformanceAttribute(): array
    {
        return $this->closedTrades()
            ->selectRaw('pair, SUM(profit_loss) as total_pnl, COUNT(*) as trade_count')
            ->groupBy('pair')
            ->orderByDesc('total_pnl')
            ->get()
            ->toArray();
    }
}
