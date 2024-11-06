<?php

namespace App\Services;

class StudentService {

    private $file_path;
    public function __construct() {
        $this->file_path = storage_path('app/students.json');
        
    }

    /**
     * Creación de alumno
     * @param object $data
     * @return array $array
     */
    public function store(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content);
    
        $student_array = [
            'id'            => $data->id,
            'nombres'       => $data->nombres,
            'apellidos'     => $data->apellidos,
            'matricula'     => $data->matricula,
            'promedio'      => $data->promedio,
        ];
        $students->push($student_array);

        file_put_contents($this->file_path, $students->toJson());

        return $student_array;
    }

    /**
     * Actualizar alumno
     * @param object $data
     * @return array $response
     */
    public function update(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content);
        $response = ['message' => ''];

        $element = $students->where('id', $data->id)->first();

        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }

        $element->nombres     = $data->nombres;
        $element->apellidos   = $data->apellidos;
        $element->matricula   = $data->matricula;
        $element->promedio    = $data->promedio;
        file_put_contents($this->file_path, $students->toJson());

        $response['message'] = ['message' =>'Operación exitosa'];
        $response['code']    = 200;

        return $response;
    }

    /**
     * Obtener alumno
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
     * Eliminación de alumno
     * @param int $id
     * @return array $response
     */
    public function delete(int $id){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content);
        $response = ['message' => ''];

        $element = $students->where('id', $id)->first();
        
        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }
        $students = $students->reject(function ($item) use ($element){
            return $item->id == $element->id;
        })->values();


        file_put_contents($this->file_path, $students->toJson());
        $response['message'] = ['message' =>'Operación exitosa'];
        $response['code']    = 200;
        return $response;
    }

    /**
     * Listado de alumnos
     * @param object $data
     * @return array $students
     */
    public function find(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content)->toArray();
        
        return $students;
    }
}