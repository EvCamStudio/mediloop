<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'sku',
        'category',
        'dosage_form',
        'unit',
        'unit_price_idr',
        'requires_cold_chain',
        'storage_min_temp',
        'storage_max_temp',
        'handling_instructions',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_idr' => 'decimal:2',
            'requires_cold_chain' => 'boolean',
            'storage_min_temp' => 'float',
            'storage_max_temp' => 'float',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}
