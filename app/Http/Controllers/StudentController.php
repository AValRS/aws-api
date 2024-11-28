<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\StudentService;
use App\Http\Requests\StudentValidator;
use Illuminate\Support\Facades\Storage;

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
            
            return response()->json($item, 201);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Actualización de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id){
        try {
            $validation =  $this->validator->store($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $data = array_merge($request->all(), ['id' => $id]);
            $response = $this->student_service->update((object) $data);
            
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

    /**
     * Reemplaza imagen de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function picture(Request $request, int $id){
        try {
            $validation =  $this->validator->picture($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $data = array_merge(['id' => $id], $request->all());
            $response = $this->student_service->picture((object) $data);

            return response()->json(['message' => $response['message'], 'fotoPerfilUrl' => $response['url']], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Envío de correos electrónicos
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmails(Request $request, int $id){
        try {
            $data = array_merge(['alumno_id' => $id], $request->all());
            $response = $this->student_service->sendEmails((object) $data);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Inicio de sesión de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, int $id){
        try {
            $validation =  $this->validator->login($request);
            if($validation !== true)
                return response()->json(['error'=> $validation->original], 400);

            $data = array_merge(['alumno_id' => $id], $request->all());
            $response = $this->student_service->login((object) $data);

            return response()->json($response['item'], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Verificar inicio de sesión de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, int $id){
        try {
            $data = array_merge(['alumno_id' => $id], $request->all());
            $response = $this->student_service->verify((object) $data);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * Cerrar sesión de alumno
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request, int $id){
        try {
            $data = array_merge(['alumno_id' => $id], $request->all());
            $response = $this->student_service->logout((object) $data);

            return response()->json(['message' => $response['message']], $response['code']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }

}
