<?php
if (!function_exists( 'Aws\parse_ini_file' )) {
    include_once('aws.phar');
}
date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

class DynamoDB
{
    public $dynamodb;
    public $marshaler;

    public function __construct()
    {
        $sdk = new Aws\Sdk([
            'profile' => 'default',
            'region'   => 'us-east-1',
            'version'  => 'latest'
        ]);
        $this->dynamodb = $sdk->createDynamoDb();
        $this->marshaler = new Marshaler();
    }
}
