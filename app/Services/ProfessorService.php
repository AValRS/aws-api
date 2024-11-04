<?php

namespace App\Services;

class ProfessorService {

    private $file_path;
    public function __construct() {
        $this->file_path = storage_path('app/professors.json');
        
    }

    /**
     * Creación de profesor
     * @param object $data
     * @return array $array
     */
    public function store(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $professors   = collect($json_content);

        if($professors->count() == 0){
            $id = 0;
        } else {
            $professors_desc = $professors->sortByDesc('id');
            $professors_desc = $professors_desc->values();
            $last_professor  = $professors_desc->first();
            $id              = $last_professor->id + 1;
        }
        
        $professor_array = [
            'id'            => $id,
            'name'          => $data->name,
            'surnames'      => $data->surnames,
            'number'        => $data->number,
            'class_hours'   => $data->class_hours,
        ];
        $professors->push($professor_array);

        file_put_contents($this->file_path, $professors->toJson());

        return $professor_array;
    }

    /**
     * Actualizar profesor
     * @param object $data
     * @return array $response
     */
    public function update(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $professors = collect($json_content);
        $response = ['message' => ''];

        $element = $professors->where('id', $data->id)->first();

        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }

        $element->name          = $data->name;
        $element->surnames      = $data->surnames;
        $element->number        = $data->number;
        $element->class_hours   = $data->class_hours;
        file_put_contents($this->file_path, $professors->toJson());

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
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content);
        $response = ['message' => ''];

        $element = $students->where('id', $id)->first();

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
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $professors = collect($json_content);
        $response = ['message' => ''];

        $element = $professors->where('id', $id)->first();
        
        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }
        $professors = $professors->reject(function ($item) use ($element){
            return $item->id == $element->id;
        })->values();


        file_put_contents($this->file_path, $professors->toJson());
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
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $professors = collect($json_content)->toArray();
        
        return $professors;
    }
}