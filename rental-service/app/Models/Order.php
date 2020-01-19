<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Uuid;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'downpayment',
        'status',
        'discount',
        'delivery_fee',
        'late_fee',
        'total',
        'balance',
        'order_date',
        'return_date',
    ];

    protected $casts = [
        'return_date' => 'date',
        'order_date' => 'date',
        'total' => 'float',
        'discount' => 'float'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotal()
    {
        $total = $this->itemsTotal() + $this->late_fee + $this->delivery_fee - $this->discount;

        return $total;
    }

    public function itemsTotal()
    {
        $totalItems = 0;
        foreach ($this->items as $item) {
            $totalItems += $item->product->price * $item->qty;
        }

        return $totalItems;
    }

    public function totalPayments()
    {
        $total = 0;

        foreach ($this->payments as $payment) {
            $total += $payment->amount;
        }

        return $total;
    }

    public function adjustBalance()
    {
        if ($this->balance != $this->getTotal() - $this->totalPayments()) {
            $this->balance = $this->getTotal() - $this->totalPayments();
            $this->save();
        }
    }

    public function adjustTotal()
    {
        if ($this->total != $this->getTotal()) {
            $this->total = $this->getTotal();
            $this->save();
        }
    }
}
