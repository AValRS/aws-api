<?php

namespace App\Services;

class StudentService {

    private $file_path;
    public function __construct() {
        $this->file_path = storage_path('app/students.json');
        
    }

    public function store(object $data){
        $file_content = file_get_contents($this->file_path);
        $json_content = json_decode($file_content);
        $students = collect($json_content);

        if($students->count() == 0){
            $id = 0;
        } else {
            $students_desc = $students->sortByDesc('id');
            $students_desc = $students_desc->values();
            $last_student  = $students_desc->first();
            $id            = $last_student->id + 1;
        }
        
        $student_array = [
            'id'            => $id,
            'name'          => $data->name,
            'surnames'      => $data->surnames,
            'enrollment'    => $data->enrollment,
            'average'       => $data->average,
        ];
        $students->push($student_array);

        file_put_contents($this->file_path, $students->toJson());

        return $student_array;
    }

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

        $element->name       = $data->name;
        $element->surnames   = $data->surnames;
        $element->enrollment = $data->enrollment;
        $element->average    = $data->average;
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