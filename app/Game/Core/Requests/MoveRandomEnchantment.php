<?php

namespace App\Game\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveRandomEnchantment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selected_slot_id'           => 'required|integer|exists:inventory_slots,id',
            'selected_secondary_slot_id' => 'required|integer|exists:inventory_slots,id',
            'selected_affix'             => 'required|string|in:prefix,suffix,all-enchantments',
        ];
    }

    public function messages() {
        return [
            'selected_slot_id.required'           => 'Invalid input.',
            'selected_secondary_slot_id.required' => 'Invalid Input.',
            'selected_affix.required'             => 'Invalid input.',
        ];
    }
}
