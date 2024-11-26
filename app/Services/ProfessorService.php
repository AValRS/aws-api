<?php

namespace App\Services;

use App\Models\Models\Professor;

class ProfessorService {

    public function __construct() {
    }

    /**
     * Creación de profesor
     * @param object $data
     * @return array $array
     */
    public function store(object $data){
        $professor = new Professor();

        $professor->nombres           = $data->nombres;
        $professor->apellidos         = $data->apellidos;
        $professor->numeroEmpleado    = $data->numeroEmpleado;
        $professor->horasClase        = $data->horasClase;
        $professor->save();

        return $professor;
    }

    /**
     * Actualizar profesor
     * @param object $data
     * @return array $response
     */
    public function update(object $data){
        $response = ['message' => ''];

        $element = Professor::find($data->id);

        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }

        $element->nombres           = $data->nombres;
        $element->apellidos         = $data->apellidos;
        $element->numeroEmpleado    = $data->numeroEmpleado;
        $element->horasClase        = $data->horasClase;
        $element->save();

        $response['message'] = ['message' =>'Operación exitosa'];
        $response['code']    = 200;

        return $response;
    }

    /**
     * Obtener profesor
     * @param int $id
     * @return mixed $element
     */
    public function get(int $id){
        $response = ['message' => ''];

        $element = Professor::find($id);

        if(is_null($element)){
            $response['message'] = 'No se encontró el elemento solicitado';
            $response['item']    = null;
            $response['code']    = 404;
            return $response;
        }

        $response['message']    = 'Operación exitosa';
        $response['item']       = $element;
        $response['code']       = 200;

        return $response;
    }
    
    /**
     * Eliminación de profesor
     * @param int $id
     * @return array $response
     */
    public function delete(int $id){
        $response = ['message' => ''];

        $element = Professor::find($id);
        
        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }

        $element->delete();
        
        $response['message'] = ['message' =>'Operación exitosa'];
        $response['code']    = 200;
        return $response;
    }

    /**
     * Listado de profesores
     * @param object $data
     * @return array $professors
     */
    public function find(object $data){
        $professors = Professor::all();
        
        return $professors;
    }
}