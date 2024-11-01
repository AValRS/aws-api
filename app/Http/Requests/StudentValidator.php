<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StudentValidator extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string',
            'surnames'      => 'required|string',
            'enrollment'    => 'required|string',
            'average'       => 'required|numeric|between:0,10'
        ]);
       
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        return true;
    }
}
