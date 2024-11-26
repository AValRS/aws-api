<?php

namespace App\Services;

use App\Models\Models\Student;

class StudentService {

    public function __construct() {   

    }

    /**
     * Creación de alumno
     * @param object $data
     * @return array $array
     */
    public function store(object $data){
        $student = new Student();

        $student->nombres     = $data->nombres;
        $student->apellidos   = $data->apellidos;
        $student->matricula   = $data->matricula;
        $student->promedio    = $data->promedio;
        $student->password    = isset($data->password) ? bcrypt($data->password) : bcrypt(123456);
        $student->save();

        return $student;
    }

    /**
     * Actualizar alumno
     * @param object $data
     * @return array $response
     */
    public function update(object $data){
        $response = ['message' => ''];

        $element = Student::find($data->id);

        if(is_null($element)){
            $response['message'] = ['message' =>'No se encontró el elemento solicitado'];
            $response['code']    = 404;
            return $response;
        }

        $element->nombres     = $data->nombres;
        $element->apellidos   = $data->apellidos;
        $element->matricula   = $data->matricula;
        $element->promedio    = $data->promedio;
        if(isset($data->password))
            $element->password    =  bcrypt($data->password);
        
        $element->save();

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
        $response = ['message' => ''];

        $element = Student::find($id);

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
        $response = ['message' => ''];

        $element = Student::find($id);
        
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
     * Listado de alumnos
     * @param object $data
     * @return array $students
     */
    public function find(object $data){
        $students = Student::all();
        
        return $students;
    }
}