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

    /**
     * Actualización de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){
        try {
            $validation =  $this->validator->store($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $response = $this->student_service->update((object) $request->all());
            
            return response()->json($response['message'], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Obtener alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request){
        try {
            $response = $this->student_service->get($request->id);
            if($response['code'] == 404){
                return response()->json(['message' => $response['message']], $response['code']);
            } else {
                return response()->json($response['item'], $response['code']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Eliminar alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        try {
            $response = $this->student_service->delete($request->id);
            return response()->json($response['message'], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Listado de alumnos
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request){
        try {
            $items = $this->student_service->find((object) $request->all());
            return response()->json(['data' => $items], 200);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

}
