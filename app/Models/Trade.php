<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_account_id',
        'pair',
        'direction',
        'quantity',
        'entry_price',
        'exit_price',
        'stop_loss',
        'take_profit',
        'entry_time',
        'exit_time',
        'profit_loss',
        'profit_loss_percent',
        'screenshot_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'quantity' => 'float',
        'entry_price' => 'float',
        'exit_price' => 'float',
        'stop_loss' => 'float',
        'take_profit' => 'float',
        'profit_loss' => 'float',
        'profit_loss_percent' => 'float',
    ];

    // ─── Boot: Auto-calculate P/L on save ────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (Trade $trade) {
            $trade->calculateProfitLoss();
        });
    }

    /**
     * Crypto P/L Calculation:
     * Long:  (exit_price - entry_price) * quantity
     * Short: (entry_price - exit_price) * quantity
     */
    public function calculateProfitLoss(): void
    {
        if ($this->status === 'closed' && $this->exit_price && $this->entry_price && $this->quantity) {
            if ($this->direction === 'long') {
                $this->profit_loss = ($this->exit_price - $this->entry_price) * $this->quantity;
            } else {
                $this->profit_loss = ($this->entry_price - $this->exit_price) * $this->quantity;
            }

            // P/L % relative to cost (entry_price * quantity)
            $cost = $this->entry_price * $this->quantity;
            if ($cost > 0) {
                $this->profit_loss_percent = ($this->profit_loss / $cost) * 100;
            }
        } elseif ($this->status === 'open') {
            $this->profit_loss = null;
            $this->profit_loss_percent = null;
            $this->exit_price = null;
            $this->exit_time = null;
        }
    }

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function tradingAccount(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'trade_tag');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function isProfitable(): bool
    {
        return $this->profit_loss > 0;
    }

    public function isLoss(): bool
    {
        return $this->profit_loss < 0;
    }

    public function getFormattedPnlAttribute(): string
    {
        if ($this->profit_loss === null) return '—';
        $sign = $this->profit_loss >= 0 ? '+' : '';
        return $sign . number_format($this->profit_loss, 4) . ' ' . ($this->tradingAccount->currency ?? 'USDT');
    }

    public function getRiskRewardAttribute(): ?float
    {
        if (!$this->stop_loss || !$this->take_profit) return null;
        $risk = abs($this->entry_price - $this->stop_loss);
        $reward = abs($this->take_profit - $this->entry_price);
        return $risk > 0 ? round($reward / $risk, 2) : null;
    }
}
