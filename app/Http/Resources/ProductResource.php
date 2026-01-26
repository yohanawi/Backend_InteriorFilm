<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'catalog_id' => $this->catalog_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            
            // Pricing information
            'price' => [
                'amount' => (float) $this->price,
                'formatted' => number_format($this->price, 2),
                'currency' => 'AED', // Adjust based on your needs
            ],
            
            // Discount information
            'discount' => $this->when($this->discount_value > 0, [
                'type' => $this->discount_type,
                'value' => (float) $this->discount_value,
                'final_price' => $this->calculateFinalPrice(),
            ]),
            
            // Images
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'images' => $this->images ? collect($this->images)->map(function($image) {
                return asset('storage/' . $image);
            })->values()->toArray() : [],
            
            // Description
            'description' => $this->description,
            'short_description' => $this->when($this->description, \Illuminate\Support\Str::limit(strip_tags($this->description), 150)),                    
            
            // Status flags
            'is_featured' => (bool) $this->is_featured,
            'is_popular' => (bool) $this->is_popular,
            'is_active' => (bool) $this->is_active,
            'status' => $this->status,
            
            // Physical properties
            'physical_properties' => $this->when($this->is_physical, [
                'weight' => $this->weight,
                'dimensions' => [
                    'width' => $this->width,
                    'height' => $this->height,
                    'length' => $this->length,
                ],
            ]),
            
            // Variations and tags
            'variations' => $this->variations ?? [],
            'tags' => $this->tags ?? [],
            
            // Tax information
            'tax' => [
                'class_id' => $this->tax_class_id,
                'vat' => (float) $this->vat,
            ],
            
            // Relationships
            'category' => new CategoryResource($this->whenLoaded('category')),
            'catalog' => new CatalogResource($this->whenLoaded('catalog')),
            'specification' => new ProductSpecificationResource($this->whenLoaded('specification')),
            
            // Meta information
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ],
            
            // Timestamps
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
    
    /**
     * Calculate the final price after discount
     *
     * @return float|null
     */
    private function calculateFinalPrice()
    {
        if (!$this->discount_value || $this->discount_value <= 0) {
            return null;
        }
        
        if ($this->discount_type === 'percentage') {
            $discountAmount = ($this->price * $this->discount_value) / 100;
            return (float) ($this->price - $discountAmount);
        }
        
        if ($this->discount_type === 'fixed') {
            return (float) max(0, $this->price - $this->discount_value);
        }
        
        return null;
    }
}
