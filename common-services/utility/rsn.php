<?php
include_once('dynamodb.php');


class OxygenOES
{

  public static function get($arr)
  {
    $stringCode = "1";
    $response = '';
    foreach ($arr as $s) {
      # code...
      $response = $response . $stringCode . $s . chr(127);
    }
    return $stringCode . $response . chr(127);
  }
}

class OxygenGUID
{
  public function isValid($params)
  {
    if (is_string($params)) {
      $guid = $params;
    } else if ($params && $params['GUID'] && is_string($params['GUID'])) {
      $guid = $params['GUID'];
    } else {
      return false;
    }
    // GUID should allow all Alphanumeric
    if (preg_match('/^_[0-9A-Fa-f]{34}_$/', $guid) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z[0-9A-Fa-f]{3}-[0-9A-Fa-f]{4}Z$/', $guid)) {
      return true;
    } else {
      return false;
    }
  }
}

class OxygenRSN
{
  private $sdk;
  private $dynamodb;
  private $marshaler;

  private $FirstTelNo = '0000000000';
  private $LastTelNo = '9999999999';
  private $FirstShortName = '000000';
  private $LastShortName = 'zzzzzz';

  function __construct()
  {
    $ddb = new DynamoDB();
    $this->dynamodb = $ddb->dynamodb;
    $this->marshaler = $ddb->marshaler;
  }

  function _nextShortName($sn)
  {
    if ($sn === $this->LastShortName) {
      return '';
    }
    for ($i = strlen($sn) - 1; $i >= 0; $i--) {
      if ($sn[$i] === 'z') {
        $sn = substr($sn, 0, $i) + '0' + substr($sn, $i + 1);
      } else {
        $sn = substr($sn, 0, $i) + $this->_nextCharacter($sn[$i]) + substr($sn, $i + 1);
        break;
      }
    }
    return $sn;
  }
  function _nextTelephoneNumber($tn)
  {
    if ($tn === $this->LastTelNo) {
      return '';
    }
    for ($i = strlen($tn) - 1; $i >= 0; $i--) {
      if ($tn[$i] === '9') {
        $tn = substr($tn, 0, $i) + '0' + substr($tn, $i + 1);
      } else {
        $tn = substr($tn, 0, $i) + $this->_nextCharacter($tn[$i]) + substr($tn, $i + 1);
        break;
      }
    }
    return $tn;
  }
  function _previousShortName($sn)
  {
    if ($sn === $this->FirstShortName) {
      return '';
    }
    for ($i = strlen($sn) - 1; $i >= 0; $i--) {
      if ($sn[$i] === '0') {
        $sn = substr($sn, 0, $i) + 'z' + substr($sn, $i + 1);
      } else {
        $sn = substr($sn, 0, $i) + $this->_previousCharacter($sn[$i]) + substr($sn, $i + 1);
        break;
      }
    }
    return $sn;
  }
  function _previousTelephoneNumber($tn)
  {
    if ($tn === $this->FirstTelNo) {
      return '';
    }
    for ($i = strlen($tn) - 1; $i >= 0; $i--) {
      if ($tn[$i] === '0') {
        $tn = substr($tn, 0, $i) + '9' + substr($tn, $i + 1);
      } else {
        $tn = substr($tn, 0, $i) + $this->_previousCharacter($tn[$i]) + substr($tn, $i + 1);
        break;
      }
    }
    return $tn;
  }
  function charCodeAt($str, $index)
  {
    $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');
    return ord($utf16[$index * 2]) + (ord($utf16[$index * 2 + 1]) << 8);
  }
  function _previousCharacter($c)
  {
    if ($c > 'a' && $c <= 'z') {
      return chr($this->charCodeAt($c, 0) - 1);
    } else if ($c === 'a') {
      return '9';
    } else if ($c > '0' && $c <= '9') {
      return chr($this->charCodeAt($c, 0) - 1);
    } else {
      return false;
    }
  }

  function _nextCharacter($c)
  {
    if ($c >= '0' && $c < '9') {
      return chr($this->charCodeAt($c, 0) + 1);
    } else if ($c === '9') {
      return 'a';
    } else if ($c >= 'a' && $c < 'z') {
      return chr($this->charCodeAt($c, 0) + 1);
    } else {
      return false;
    }
  }
  function _createMetadataForTTL($params)
  {
    if ($params['RSNType'] === 'ShortName') {
      $lastShortName = $this->FirstShortName;
    } else if ($params['RSNType'] === 'TelephoneNumber') {
      $lastShortName = $this->FirstTelNo;
    }
    $now = date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')));
    $metadata = [
      'Node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
      'ShortName' => '0',
      'LastShortName' => $lastShortName,
      'CreationTime' => $now,
      'UpdateTime' => $now
    ];
    $this->dynamodb->putItem([
      'TableName' => 'RSN',
      'Item' => $this->marshaler->marshalItem($metadata),
      'ConditionExpression' => 'attribute_not_exists(#node) OR attribute_not_exists(#shortname)',
      'ExpressionAttributeNames' => [
        '#node' => 'Node',
        '#shortname' => 'ShortName'
      ]
    ]);
    return $metadata;
  }
  function _createMetadataForHold($params)
  {
    if ($params['RSNType'] === 'ShortName') {
      $lastShortName = $this->LastShortName;
    } else if ($params['RSNType'] === 'TelephoneNumber') {
      $lastShortName = $this->LastTelNo;
    }
    $now = date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')));
    $metadata = [
      'Node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
      'ShortName' => '1',
      'LastShortName' => $lastShortName,
      'CreationTime' => $now,
      'UpdateTime' => $now
    ];
    $this->dynamodb->putItem([
      'TableName' => 'RSN',
      'Item' => $this->marshaler->marshalItem($metadata),
      'ConditionExpression' => 'attribute_not_exists(#node) OR attribute_not_exists(#shortname)',
      'ExpressionAttributeNames' => [
        '#node' => 'Node',
        '#shortname' => 'ShortName'
      ]
    ]);
    return $metadata;
  }
  function _getRSN($params)
  {
    // do {
    //   // statement
    // } while (true);
    $data = $this->dynamodb->getItem([
      'TableName' => 'RSN',
      'Key' => [
        'Node' => ['S' => OxygenOES::get([$params['Registrant'], $params['RSNType']])],
        'ShortName' => ['S' => '0']
      ]
    ]);
    $result = [];
    if ($data && $data['Item']) {
      $metadata = $this->marshaller->unmarshalItem($data['Item']);
      if ($params['RSNType'] === 'ShortName') {
        $nextShortName = $this->_nextShortName($metadata['LastShortName']);
      } else if ($params['RSNType'] === 'TelephoneNumber') {
        $nextShortName = $this->_nextTelephoneNumber($metadata['LastShortName']);
      }
      $result = $this->dynamodb->updateItem([
        'TableName' => 'RSN',
        'Key' => [
          'Node' => ['S' => OxygenOES::get([$params['Registrant'], $params['RSNType']])],
          'ShortName' => ['S' => '0']
        ],
        'UpdateExpression' => 'SET LastShortName = :nextShortName, UpdateTime = :updateTime',
        'ConditionExpression' => 'LastShortName = :lastShortName',
        'ExpressionAttributeValues' => [
          ':nextShortName' => ['S' => $nextShortName],
          ':updateTime' => ['S' => date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')))],
          ':lastShortName' => ['S' => $metadata['LastShortName']]
        ],
        'ReturnValues' => 'UPDATED_NEW'
      ]);
      $result = $data['Attributes'];
    } else {
      $result = $this->_createMetadataForTTL($params);
    }
    if ($result['LastShortName']) {
      $ttl = time() + $params['TTL'];
      $ttl = round($ttl / 1000);
      $now = date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')));
      $rsnItem = [
        'Node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
        'ShortName' => $result['LastShortName'],
        'OxygenID' => $params['OxygenID'],
        'Availability' => 'Used',
        'TTL' => $ttl,
        'CreationTime' => $now,
        'UpdateTime' => $now
      ];
      $this->dynamodb->putItem([
        'TableName' => 'RSN',
        'Item' => $this->marshaler->marshalItem($rsnItem)
      ]);
      return $rsnItem;
    }
  }

  function _holdRSN($params)
  {
    $data = $this->dynamodb->getItem([
      'TableName' => 'RSN',
      'Key' => [
        'Node' => ['S' => OxygenOES::get([$params['Registrant'], $params['RSNType']])],
        'ShortName' => ['S' => '1']
      ]
    ]);
    $result = [];
    if ($data && $data['Item']) {
      $metadata = $data['Item'];
      if ($params['RSNType'] === 'ShortName') {
        $previousShortName = $this->_previousShortName($metadata['LastShortName']);
      } else if ($params['RSNType'] === 'TelephoneNumber') {
        $previousShortName = $this->_previousTelephoneNumber($metadata['LastShortName']);
      }
      $result = $this->dynamodb->updateItem([
        'TableName' => 'RSN',
        'Key' => [
          'Node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
          'ShortName' => '1'
        ],
        'UpdateExpression' => 'SET LastShortName = :previousShortName, UpdateTime = :updateTime',
        'ConditionExpression' => 'LastShortName = :lastShortName',
        'ExpressionAttributeValues' => [
          ':previousShortName' => ['S' => $previousShortName],
          ':updateTime' => ['S' => date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')))],
          ':lastShortName' => ['S' => $metadata['LastShortName']]
        ],
        'ReturnValues' => 'UPDATED_NEW'
      ]);
      $result = $data['Attributes'];
    } else {
      $result = $this->_createMetadataForHold($params);
    }
    if ($result['LastShortName']) {
      $now = date(DateTime::ISO8601, strtotime(date('Y-m-d H:m:s')));
      $rsnItem = [
        'Node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
        'ShortName' => $result['LastShortName'],
        'OxygenID' => $params['OxygenID'],
        'Availability' => 'Used',
        'CreationTime' => $now,
        'UpdateTime' => $now
      ];
      $this->dynamodb->putItem([
        'TableName' => 'RSN',
        'Item' => $this->marshaler->marshalItem($rsnItem)
      ]);
      return $rsnItem;
    }
  }


  function get($params = [])
  {
    $guid = new OxygenGUID();
    $eav = $this->marshaler->marshalItem([

      ':node' => OxygenOES::get([$params['Registrant'], $params['RSNType']]),
      ':oxygenID' => $params['OxygenID']
    ]);
    $data = $this->dynamodb->query([
      'TableName'  => 'RSN',
      'IndexName'  => 'Node-OxygenID-index',
      'KeyConditionExpression'  => 'Node = :node and OxygenID = :oxygenID',
      'ExpressionAttributeValues'  => $eav
    ]);

    if (count($data['Items']) > 0) {
      $darr = [];
      foreach ($data['Items'] as $value) {
        $darr[] = $this->marshaler->unmarshalItem($value);
      }
      return $darr;
    } else {

      if (!$guid->isValid($params['Registrant'])) {
        return false;
      }
      if (!($params['RSNType'] === 'ShortName' || $params['RSNType'] === 'TelephoneNumber')) {
        // callback(null, {})
        return false;
      }
      if (!$guid->isValid($params['OxygenID'])) {
        // callback(null, {})
        return false;
      }
      if ($params['TTL']) {
        if (!is_numeric($params['TTL'])) {
          return false;
        }
      }
      $params['TTL'] = $params['TTL'] || 24 * 60 * 60 * 1000;
      if ($params['TTL'] > 4 * 7 * 24 * 60 * 60 * 1000) {
        $params['TTL'] = 4 * 7 * 24 * 60 * 60 * 1000;
      }
      if ($params['Hold']) {
        return $this->_holdRSN($params);
      } else {
        return $this->_getRSN($params);
      }
    }
  }
}
