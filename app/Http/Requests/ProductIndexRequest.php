<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'catalog_id' => 'nullable|integer|exists:catalogs,id',
            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort_by' => 'nullable|string|in:name,price,created_at,updated_at,popularity',
            'sort_order' => 'nullable|string|in:asc,desc',
            // 'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'with_relations' => 'nullable|array',
            'with_relations.*' => 'nullable|string|in:category,catalog,specification',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'catalog_id.exists' => 'The selected catalog does not exist.',
            'max_price.gte' => 'The maximum price must be greater than or equal to the minimum price.',
            // 'per_page.max' => 'You cannot request more than 100 items per page.',
        ];
    }

    /**
     * Get the validated data with defaults.
     *
     * @return array
     */
    public function validatedWithDefaults()
    {
        return array_merge([
            'search' => null,
            'category_id' => null,
            'catalog_id' => null,
            'is_featured' => null,
            'is_popular' => null,
            'is_active' => true, // Default to active products only
            'min_price' => null,
            'max_price' => null,
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
            // 'per_page' => 15,
            'with_relations' => [],
        ], $this->validated());
    }
}
