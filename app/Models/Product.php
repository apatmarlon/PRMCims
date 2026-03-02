<?php

namespace App\Models;

use App\Enums\TaxType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;


    public $fillable = [
        'name',
        'slug',
        'code',
        'quantity',
        'quantity_alert',
        'buying_price',
        'selling_price',
        'brand_id',
        'margin_percent',
        'margin_amount',
        'supplier_id',
        'tax',
        'tax_type',
        'notes',
        'product_image',
        'category_id',
        'unit_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
         'margin_amount' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_type' => TaxType::class
    ];

    /*
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function purchases()
    {
        return $this->hasMany(\App\Models\PurchaseDetails::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\Models\OrderDetails::class);
    }

    public function returns()
    {
        return $this->hasMany(\App\Models\OrderReturnDetail::class);
    }


    // protected function buyingPrice(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $value / 100,
    //         set: fn ($value) => $value * 100,
    //     );
    // }

    // protected function sellingPrice(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $value / 100,
    //         set: fn ($value) => $value * 100,
    //     );
    // }


    public function scopeSearch($query, $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {

            $q->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%")

            ->orWhereHas('category', function ($cat) use ($term) {
                $cat->where('name', 'like', "%{$term}%");
            })

            ->orWhereHas('supplier', function ($sup) use ($term) {
                $sup->where('name', 'like', "%{$term}%");
            })

            ->orWhereHas('brand', function ($brand) use ($term) {
                $brand->where('name', 'like', "%{$term}%");
            })

            ->orWhereHas('unit', function ($unit) use ($term) {
                $unit->where('name', 'like', "%{$term}%");
            });

        });
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function returnDetails()
    {
        return $this->hasMany(OrderReturnDetail::class);
    }
    protected static function boot()
{
    parent::boot();

    static::creating(function ($product) {
        $lastCode = Product::max('code');

        if (!$lastCode) {
            $product->code = '0001';
        } else {
            $nextNumber = (int) $lastCode + 1;
            $product->code = str_pad($nextNumber, 4, STR_PAD_LEFT);
        }
    });
}
}
