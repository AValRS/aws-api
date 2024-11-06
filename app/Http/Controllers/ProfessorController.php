<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfessorValidator;
use App\Services\ProfessorService;
use Exception;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    private $professor_service;
    private $validator;

    public function __construct()
    {
        $this->professor_service = new ProfessorService();
        $this->validator = new ProfessorValidator(); 
    }

    /**
     * Creación de profesor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        try {
            $validation =  $this->validator->store($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $item = $this->professor_service->store((object) $request->all());
            
            return response()->json(['message' => 'Operación exitosa', 'item' => $item], 201);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Actualización de profesor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id){
        try {
            $validation =  $this->validator->store($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $data = array_merge($request->all(), ['id' => $id]);
            $response = $this->professor_service->update((object) $data);
            
            return response()->json($response['message'], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Obtener profesor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request){
        try {
            $response = $this->professor_service->get($request->id);
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
     * Eliminar profesor
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        try {
            $response = $this->professor_service->delete($request->id);
            return response()->json($response['message'], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Listado de profesores
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request){
        try {
            $items = $this->professor_service->find((object) $request->all());
            return response()->json(['data' => $items], 200);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }
}
