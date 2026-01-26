<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $fillable = [
        'product_id',
        'certifications',
        'features',
        'spec_dimensions',
        'surface_finish',
        'tensile_strength',
        'application_temperature',
        'elongation',
        'service_temperature',
        'storage',
        'dimensional_stability',
        'release_paper',
        'adhesive',
        'adhesive_strength',
        'shelf_life',
        'warranty',
    ];

    protected $casts = [
        'certifications' => 'array',
        'features' => 'array',
    ];

    public function getCertificationsAttribute($value)
    {
        return $this->normalizeStringArrayAttribute($value);
    }

    public function getFeaturesAttribute($value)
    {
        // Return only the feature names as strings, matching the GraphQL schema
        $items = $this->normalizeFeaturesItems($value);
        if (!is_array($items)) return [];
        $names = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item['name'])) {
                $names[] = $item['name'];
            } elseif (is_string($item)) {
                $names[] = $item;
            }
        }
        return $names;
    }

    public function getFeaturesItemsAttribute()
    {
        $raw = $this->attributes['features'] ?? null;
        return $this->normalizeFeaturesItems($raw) ?? [];
    }

    private function normalizeFeaturesItems($value): ?array
    {
        if ($value === null) return null;

        $decoded = $value;
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (!is_array($decoded)) return [];

        $out = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $name = trim($item);
                if ($name !== '') {
                    $out[] = ['name' => $name, 'image' => null];
                }
                continue;
            }

            if (is_array($item)) {
                $name = isset($item['name']) && is_string($item['name']) ? trim($item['name']) : '';
                $image = isset($item['image']) && is_string($item['image']) ? trim($item['image']) : null;
                if ($name !== '') {
                    $out[] = ['name' => $name, 'image' => ($image !== '' ? $image : null)];
                }
            }
        }

        return $out;
    }

    private function normalizeStringArrayAttribute($value)
    {
        if ($value === null) return null;

        $decoded = $value;
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (!is_array($decoded)) return [];

        $out = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $t = trim($item);
                if ($t !== '') $out[] = $t;
                continue;
            }

            if (is_array($item)) {
                if (isset($item['name']) && is_string($item['name'])) {
                    $t = trim($item['name']);
                    if ($t !== '') $out[] = $t;
                } else {
                    $out[] = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                continue;
            }

            if (is_object($item)) {
                if (isset($item->name) && is_string($item->name)) {
                    $t = trim($item->name);
                    if ($t !== '') $out[] = $t;
                } else {
                    $out[] = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }

        return $out;
    }

    /**
     * Get the product that owns the specification.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
