<?php
namespace App\Services;

use Ramsey\Uuid\Uuid;
use Aws\DynamoDb\DynamoDbClient;

class DynamoDbService
{
    protected $dynamoDbClient;

    public function __construct()
    {
        $this->dynamoDbClient = new DynamoDbClient([
            'region' => config('services.dynamodb.region'),
            'version' => 'latest',
            'credentials' => [
                'key'     => config('services.dynamodb.key'),
                'secret'  => config('services.dynamodb.secret'),
                'token'  => config('services.dynamodb.token'),
            ],
        ]);
    }

    /**
     * Método para obtener un item
     * @param string $key
     * @param string $table_name
     * @return \Aws\Result $result
     */
    public function getItem(string $key, string $table_name = 'sesiones-alumnos')
    {
        $result = $this->dynamoDbClient->getItem([
            'TableName' => $table_name,
            'Key' => [
                'id' => ['S' => $key],
            ],
        ]);
        foreach ($result['Item'] as $key => $value) {
            $item[$key] = reset($value);
        }

        return $item;
    }

    /**
     * Método para insertar un item
     * @param object $data
     * @param string $table_name
     * @return array $result
     */
    public function putItem(object $data, string $table_name = 'sesiones-alumnos')
    {
        $item = [
            'id'            => ['S' => Uuid::uuid4()->toString()],
            'fecha'         => ['N' => (string) time()],
            'alumnoId'      => ['N' => (string) $data->alumno_id],
            'active'        => ['BOOL' => true],
            'sessionString' => ['S' => bin2hex(random_bytes(64))],
        ]; 
        $aws_response = $this->dynamoDbClient->putItem([
            'TableName' => $table_name,
            'Item' => $item,
        ]);

        $transformed_item = [];

        foreach ($item as $key => $value) {
            $transformed_item[$key] = reset($value);
        }

        $result = ['aws_response' => $aws_response, 'item' => $transformed_item];

        return $result;
    }
    
    /**
     * Método para poner como inactivo un registro
     * @param object $data
     * @param string $table_name
     * @return \Aws\Result $result
     */
    public function deleteItem(string $key, string $table_name = 'sesiones-alumnos')
    {
        $params = [
            'TableName' => $table_name,
            'Key' => [
                'id' => ['S' => $key],
            ],
            'UpdateExpression' => 'SET #active = :new_active',
            'ExpressionAttributeNames' => [
                '#active' => 'active',
            ],
            'ExpressionAttributeValues' => [
                ':new_active' => ['BOOL' => false],
            ],
            'ReturnValues' => 'UPDATED_NEW',
        ];

        $result = $this->dynamoDbClient->updateItem($params);

        return $result;
    }

    /**
     * Recuperación de elementos que cumplan condición para el verify
     * @param object $data
     * @param string $table_name
     * @return \Aws\Result $result
     */
    public function scanTable(object $data, string $table_name = 'sesiones-alumnos')
    {   
        $filter_expression = 'alumnoId = :alumnoId AND active = :active';
        $expression_attribute_values = [
            ':alumnoId'         => ['N' => (string) $data->alumno_id],
            ':active'           => ['BOOL' => true],
        ];

        if(isset($data->sessionString)){
            $filter_expression .= ' AND sessionString = :sessionString';
            $expression_attribute_values = array_merge($expression_attribute_values, [':sessionString'    => ['S' => $data->sessionString]]);
        }

        $query = [
            'TableName'         => $table_name,
            'FilterExpression'  => $filter_expression,
            'ExpressionAttributeValues' => $expression_attribute_values
        ];

        $result = $this->dynamoDbClient->scan($query);
        $items = $result['Items'];

        return $items;
    }
}