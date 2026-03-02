<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
     protected static function booted()
    {
        static::addGlobalScope('withRelations', function ($query) {
            $query->with([
                'supplier',
                'details.product',
                'createdBy',
                'updatedBy',
            ]);
        });
    }
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'supplier_id',
        'date',
        'purchase_no',
        'status',
        'purchase_status',
        'payment_status',
        'payterm',
        'discount_percentage',
        'discount_amount',
        'total_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date'       => 'date',
        'discount_percentage' => 'float',
        'discount_amount'     => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status'     => PurchaseStatus::class
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function scopeSearch($query, $value): void
    {
        $query->where('purchase_no', 'like', "%{$value}%")
            ->orWhere('status', 'like', "%{$value}%")
        ;
    }
   
}
