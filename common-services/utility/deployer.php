<?php
include_once('dynamodb.php');
include_once('mustache.php');

class Deployer
{
    private $dynamodb;
    private $marshaler;
    function __construct()
    {
        $ddb = new DynamoDB();
        $this->dynamodb = $ddb->dynamodb;
        $this->marshaler = $ddb->marshaler;
    }
    public function getInstances()
    {

        $tableName = 'OxygenComponents';

        $tmp = OxygenOES::get(['_FFFFFFFFFFFFFF00001582827226869011_']);
        $ffttidPrefix = substr($tmp, 0, strlen($tmp) - 1);

        $params = [
            'TableName' => $tableName,
            'IndexName' => 'MetaType-FFTTID-index',
            'KeyConditions' => [
                'MetaType' => [
                    'ComparisonOperator' => 'EQ',
                    'AttributeValueList' => [['S' => 'Type']]
                ],
                'FFTTID' => [
                    'ComparisonOperator' => 'BEGINS_WITH',
                    'AttributeValueList' => [['S' => $ffttidPrefix]]
                ]
            ]
        ];


        try {
            $result = $this->dynamodb->query($params);
            $finalResult = [];
            foreach ($result['Items'] as $value) {
                $finalResult[] = $this->marshaler->unmarshalItem($value);
            }
            return $finalResult;
        } catch (DynamoDbException $e) {
            return $e->getMessage();
        }
    }
    public function updateConfig($params, $rsn)
    {
        unlink('/UserData/_FFFFFFFFFFFFFF00001579713184157445_/_FFFFFFFFFFFFFF00001579713184157445_/Organizations/' . $params['DisplayName'] . '/wp-config.php');
        $m = new Mustache_Engine(array('entity_flags' => ENT_QUOTES));
        $ts = file_get_contents("/Code/github/OxygenWordPressFolder-0081/wp-config.template.php");
        $s = $m->render($ts, [
            'TABLE_PREFIX' => $rsn['ShortName'] + '_',
            'ORGANIZATION_NAME' => $params['DisplayName']
        ]);
        file_put_contents('/UserData/_FFFFFFFFFFFFFF00001579713184157445_/_FFFFFFFFFFFFFF00001579713184157445_/Organizations/' . $params['DisplayName'] . '/wp-config.php', $s);
    }
}
