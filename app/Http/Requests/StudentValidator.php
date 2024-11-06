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

    public function store(Request $request, string $action = 'store'){
        $params = [
            'nombres'   => 'required|string',
            'apellidos' => 'required|string',
            'matricula' => 'required|string',
            'promedio'  => 'required|numeric|between:0,10'
        ];

        // if($action == 'update'){
        //     $params = array_merge($params, [
        //         'id'        => 'required|integer'
        //     ]);
        // }
        $validator = Validator::make($request->all(), $params);
        
        
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        return true;
    }
}
