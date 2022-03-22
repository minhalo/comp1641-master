<?php

namespace App\Http\Requests\Api\Category;

use App\Constants\StatusConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
//            'name' => ['required', 'string', 'min:6', 'max:255', 'unique:categories'],
//            'description' => ['nullable','string','min:6','max:255'],
//            'status' => ['required', 'integer',  Rule::in([StatusConst::PUBLISHED, StatusConst::UNPUBLISHED , StatusConst::DRAFT])],
        ];
    }
}
