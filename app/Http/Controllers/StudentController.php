<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentValidator;
use Exception;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private $student_service;
    private $validator;

    public function __construct()
    {
        $this->student_service = new StudentService();
        $this->validator = new StudentValidator(); 
    }

    /**
     * Creación de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        try {
            $validation =  $this->validator->store($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $item = $this->student_service->store((object) $request->all());
            
            return response()->json(['message' => 'Operación exitosa', 'item' => $item], 201);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function update(Request $request){
        try {
            
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function get(Request $request){
        try {
            
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function delete(Request $request){
        try {
            
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function find(Request $request){
        try {
            
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

}
