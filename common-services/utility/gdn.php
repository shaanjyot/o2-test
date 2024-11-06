<?php
include_once('dynamodb.php');

class OxygenGDN {
  private $dynamodb;
  private $marshaler;

  function __construct()
  {
    $ddb = new DynamoDB();
    $this->dynamodb = $ddb->dynamodb;
    $this->marshaler = $ddb->marshaler;
  }

  private function getID($dn) {
    
    $args = [
      'TableName' => 'GlobalDisplayNames',
      'IndexName' => 'GDN-OxygenID-index',
      'KeyConditions' => [
        'GDN' => [
          'ComparisonOperator' => 'EQ',
          'AttributeValueList' => [['S' => $dn]]
        ]
      ]
    ];
    try {
      //code...
      $result = $this->dynamodb->query($args);
      if ($result['Count'] > 0) {
        $value = $result['Items'][0];
        $item = $this->marshaler->unmarshalItem($value);
        return $item['OxygenID'];
      }
      return null;
    } catch (\Throwable $th) {
      throw $th;
    }
  } 
  public function getOxygenID($dn) {
    // $url = '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/gdn/api/getOxygenID';
    // $res = oxygen_http_post($url, [
    //   'GDN' => $dn
    // ]);
    // return $res;
    $result = $this->getID(strtolower($dn));
    if (is_null($result)) {
      $result = $this->getID($dn);
    }
    return $result;


  }
}