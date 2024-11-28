<?php

namespace App\Services;

use Carbon\Carbon;
use Aws\Sns\SnsClient;
use App\Models\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentService {

    protected $main_path;
    protected $dynamo_service;
    public function __construct() {   
        $this->main_path        = 'https://aws-api-project.s3.us-east-1.amazonaws.com/';
        $this->dynamo_service   = new DynamoDbService();
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

    /**
     * Actualización de foto de alumno
     * @param object $data
     * @return array $response
     */
    public function picture(object $data){
        $response = ['message' => 'Ocurrió un error, verifique las credenciales para S3', 'code' => 403, 'url' => null];

        $element = Student::find($data->id);
        if(is_null($element)){
            $response = [
                'message' => 'No se encontró al alumno solicitado',
                'code'    => 404,
                'url'     => null,
            ];
            return $response;
        }

        $date = Carbon::now()->format('YdmHi');
        $new_name_file = str_replace(' ', '', $element->matricula) . '_' . $date . '_' . uniqid() . '.' .$data->foto->getClientOriginalExtension();
        $new_path_file = 'public/' . $new_name_file;
        
        $result = Storage::disk('s3')->putFileAs('public', $data->foto, $new_name_file);

        if($result != false){
            $url = Storage::url($new_path_file);
            $response = [
                'message' => 'Operación exitosa',
                'code'    => 200,
                'url'     => $url
            ];
            if(!is_null($element->fotoPerfilUrl)){
                $old_path_file = str_replace(env('AWS_URL'), '', $element->fotoPerfilUrl);
                $old_path_file = ltrim($old_path_file, '/');
                Storage::disk('s3')->delete($old_path_file);
            }

            $element->fotoPerfilUrl = $url;
            $element->save();
        }

        return $response;
    }

    public function sendEmails(object $data){
        $response = [
            'message' => 'Notificaciones enviadas con exito',
            'code'    => 200,
        ];
        $element = Student::find($data->alumno_id);
        if(is_null($element)){
            $response = [
                'message' => 'No se encontró al alumno solicitado',
                'code'    => 404,
            ];
            return $response;
        }
        
        $client = new SnsClient([
            'region'    => 'us-east-1', // Cambia a tu región
            'version'   => 'latest',
            'credentials' => [
                'key'     => env('AWS_ACCESS_KEY_ID'),
                'secret'  => env('AWS_SECRET_ACCESS_KEY'),
                'token'   => env('AWS_SESSION_TOKEN'),
            ],
        ]);

        $message =  'Matrícula: ' . $element->matricula . "\n" . 
                    'Alumno: ' . $element->nombres . ' ' . $element->apellidos . "\n" .
                    'Promedio: ' . $element->promedio; 

        $result = $client->publish([
            'TopicArn'  => 'arn:aws:sns:us-east-1:486865110317:api-project-notifications',
            'Message'   => $message,
            'Subject'   => 'Calificaciones alumno',
        ]);
        
        if($result['@metadata']['statusCode'] != 200){
            $response['message'] = 'Algo salió mal durante el envió de notificaciones';
            $response['code']    = 403;
        }
        
        return $response;
    }

    public function login(object $data){
        $response = ['message' => 'Ocurrió un error, al intentar iniciar sesión', 'code' => 403, 'item' => null];
        $element = Student::find($data->alumno_id);
        if(is_null($element)){
            $response = [
                'message' => 'No se encontró al alumno solicitado',
                'code'    => 404,
                'item'    => null,
            ];
            return $response;
        }

        if(Hash::check($data->password, $element->password)){
            $active_session = $this->dynamo_service->scanTable($data);
            if(count($active_session)){
                $item = reset($active_session);
                foreach ($item as $key => $value) {
                    $item[$key] = reset($value);
                }
                $response['message'] = 'El alumno cuenta con sesión activa';
                $response['code']    = 200;
                $response['item']    = $item;
            } else {
                $result = $this->dynamo_service->putItem($data);
                if($result['aws_response']['@metadata']['statusCode'] == '200'){
                    $response = [
                        'message' => 'Registro creado correctamente',
                        'code'    => 200,
                        'item'    => $result['item'],
                    ];
                }
            }
        } else {
            $response = [
                'message' => 'Las credenciales proporcionadas no son válidas',
                'code'    => 400,
                'item'    => null,
            ]; 
        }

        return $response;
    }

    public function verify(object $data){
        $response = ['message' => 'No hay sesiones activas que coincidan con la información proporcionada', 'code' => 400];
        $element = Student::find($data->alumno_id);
        if(is_null($element)){
            return $response;
        }

        $result = $this->dynamo_service->scanTable($data);
        if(count($result) > 0){
            $response['message'] = 'Sesión activa para el alumno ' . $element->matricula . ' - ' . $element->nombres . ' ' . $element->apellidos;
            $response['code']    = 200;
        }

        return $response;
    }

    public function logout(object $data){
        $response = ['message' => 'Operación exitosa', 'code' => 200];
        $element = Student::find($data->alumno_id);
        if(is_null($element)){
            $response['message'] = 'No hay sesiones que coincidan con la información proporcionada';
            $response['code']    = 400;
            return $response;
        }

        $result = $this->dynamo_service->scanTable($data);
        if(count($result) > 0){
            $item = reset($result);
            foreach ($item as $key => $value) {
                $item[$key] = reset($value);
            }
    
            $result = $this->dynamo_service->deleteItem($item['id']);
    
            if($result['@metadata']['statusCode'] != 200){
                $response['message'] = 'Falló la actualización del registro';
                $response['code']    = 403;
            }
        }
        return $response;
    }
        
}