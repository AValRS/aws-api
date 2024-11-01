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
}