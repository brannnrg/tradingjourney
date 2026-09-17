<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public function openTrades(): HasMany
    {
        return $this->hasMany(Trade::class)->where('status', 'open');
    }

    // ─── Accessors & Calculations ────────────────────────────────────────────────

    /**
     * Current balance = initial_balance + sum of all closed P/L
     */
    public function getCurrentBalanceAttribute(): float
    {
        $totalPnl = (float) $this->closedTrades()->sum('profit_loss');
        return round($this->initial_balance + $totalPnl, 4);
    }

    /**
     * Net profit/loss from all closed trades
     */
    public function getNetProfitAttribute(): float
    {
        return (float) $this->closedTrades()->sum('profit_loss');
    }

    /**
     * Growth percentage relative to initial balance
     */
    public function getGrowthPercentageAttribute(): float
    {
        if ($this->initial_balance <= 0) return 0.0;
        return round(($this->net_profit / $this->initial_balance) * 100, 2);
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
     * Break-even trades count (pnl == 0)
     */
    public function getBreakEvenTradesAttribute(): int
    {
        return $this->closedTrades()->where('profit_loss', 0)->count();
    }

    /**
     * Average Win ($)
     */
    public function getAverageWinAttribute(): float
    {
        $winning = $this->closedTrades()->where('profit_loss', '>', 0);
        $count = $winning->count();
        if ($count === 0) return 0.0;
        return round((float) $winning->avg('profit_loss'), 4);
    }

    /**
     * Average Loss ($)
     */
    public function getAverageLossAttribute(): float
    {
        $losing = $this->closedTrades()->where('profit_loss', '<', 0);
        $count = $losing->count();
        if ($count === 0) return 0.0;
        return round(abs((float) $losing->avg('profit_loss')), 4);
    }

    /**
     * Profit factor = sum(profits) / abs(sum(losses))
     */
    public function getProfitFactorAttribute(): float
    {
        $totalProfit = (float) $this->closedTrades()->where('profit_loss', '>', 0)->sum('profit_loss');
        $totalLoss = abs((float) $this->closedTrades()->where('profit_loss', '<', 0)->sum('profit_loss'));
        if ($totalLoss == 0) return $totalProfit > 0 ? 999.0 : 0.0;
        return round($totalProfit / $totalLoss, 2);
    }

    /**
     * Expectancy ($ expected gain/loss per trade)
     * Formula: (Win Rate * Avg Win) - (Loss Rate * Avg Loss)
     */
    public function getExpectancyAttribute(): float
    {
        $total = $this->total_trades;
        if ($total === 0) return 0.0;

        $winRateFrac = $this->winning_trades / $total;
        $lossRateFrac = $this->losing_trades / $total;

        $expectancy = ($winRateFrac * $this->average_win) - ($lossRateFrac * $this->average_loss);
        return round($expectancy, 4);
    }

    /**
     * Best trade (highest profit)
     */
    public function getBestTradeAttribute(): ?Trade
    {
        return $this->closedTrades()->orderByDesc('profit_loss')->first();
    }

    /**
     * Worst trade (lowest loss)
     */
    public function getWorstTradeAttribute(): ?Trade
    {
        return $this->closedTrades()->orderBy('profit_loss')->first();
    }

    /**
     * Equity curve data: cumulative balance after each trade (for Chart.js)
     */
    public function getEquityCurveDataAttribute(): array
    {
        $trades = $this->closedTrades()
            ->orderBy('exit_time', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id', 'exit_time', 'profit_loss', 'pair']);

        $balance = (float) $this->initial_balance;
        $labels = ['Mulai (' . $this->start_date->format('d M') . ')'];
        $data = [$balance];

        foreach ($trades as $trade) {
            $balance += (float) $trade->profit_loss;
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
        $trades = $this->closedTrades()->get();
        if ($trades->isEmpty()) return [];

        $grouped = $trades->groupBy('pair');
        $performance = [];

        foreach ($grouped as $pair => $pairTrades) {
            $totalPnl = $pairTrades->sum('profit_loss');
            $wins = $pairTrades->where('profit_loss', '>', 0)->count();
            $losses = $pairTrades->where('profit_loss', '<', 0)->count();
            $count = $pairTrades->count();

            $performance[] = [
                'pair'        => $pair,
                'trade_count' => $count,
                'total_pnl'   => round($totalPnl, 4),
                'win_count'   => $wins,
                'loss_count'  => $losses,
                'win_rate'    => $count > 0 ? round(($wins / $count) * 100, 1) : 0,
            ];
        }

        usort($performance, fn($a, $b) => $b['total_pnl'] <=> $a['total_pnl']);

        return $performance;
    }

    /**
     * Performance per Strategy Tag
     */
    public function getStrategyPerformanceAttribute(): array
    {
        $closedTrades = $this->closedTrades()->with('tags')->get();
        if ($closedTrades->isEmpty()) return [];

        $stats = [];

        foreach ($closedTrades as $trade) {
            $strategyTags = $trade->tags->where('type', 'strategy');
            if ($strategyTags->isEmpty()) {
                $strategyTags = collect([(object)['name' => 'Tanpa Tag']]);
            }

            foreach ($strategyTags as $tag) {
                $name = $tag->name;
                if (!isset($stats[$name])) {
                    $stats[$name] = [
                        'name'        => $name,
                        'trade_count' => 0,
                        'wins'        => 0,
                        'losses'      => 0,
                        'total_pnl'   => 0.0,
                    ];
                }

                $stats[$name]['trade_count']++;
                $stats[$name]['total_pnl'] += (float) $trade->profit_loss;
                if ($trade->profit_loss > 0) {
                    $stats[$name]['wins']++;
                } elseif ($trade->profit_loss < 0) {
                    $stats[$name]['losses']++;
                }
            }
        }

        foreach ($stats as $key => $item) {
            $stats[$key]['win_rate'] = $item['trade_count'] > 0
                ? round(($item['wins'] / $item['trade_count']) * 100, 1)
                : 0;
            $stats[$key]['total_pnl'] = round($item['total_pnl'], 4);
        }

        usort($stats, fn($a, $b) => $b['total_pnl'] <=> $a['total_pnl']);

        return array_values($stats);
    }

    /**
     * Daily P/L for Heatmap / Calendar
     * Returns indexed array grouped by date 'Y-m-d'
     */
    public function getDailyPnlAttribute(): array
    {
        $trades = $this->closedTrades()
            ->whereNotNull('exit_time')
            ->orderBy('exit_time')
            ->get();

        $daily = [];

        foreach ($trades as $trade) {
            $date = $trade->exit_time->format('Y-m-d');
            if (!isset($daily[$date])) {
                $daily[$date] = [
                    'date'   => $date,
                    'pnl'    => 0.0,
                    'trades' => 0,
                    'wins'   => 0,
                    'losses' => 0,
                ];
            }

            $daily[$date]['pnl'] += (float) $trade->profit_loss;
            $daily[$date]['trades']++;
            if ($trade->profit_loss > 0) $daily[$date]['wins']++;
            if ($trade->profit_loss < 0) $daily[$date]['losses']++;
        }

        foreach ($daily as $date => $val) {
            $daily[$date]['pnl'] = round($val['pnl'], 4);
        }

        return $daily;
    }
}
