<?php

class OxygenGrant
{

  /**
   * Sync with Services in Oxygen
   */
  public static function tip_create($arr)
  {
    return oxygen_http_post('/CommonServices-0025/Tips', $arr);
  }

  /**
   * Get Guid for Grant
   */
  public static function get_guid()
  {
    $servBody = oxygen_http_post('/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/guid/api/generate', []);

    return $servBody['GUID'];
  }


  /**
   * Create Grant through API
   * 
   */

  public static function create($arr)
  {
    return oxygen_http_post('/CommonServices-0025/Grants', $arr);
  }

  /**
   * Revoke Grant by TransactionID
   */
  public static function revoke($id)
  {
    return oxygen_http_post('/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/grants/api/revoke', ['TransactionID' => $id]);
  }

  /**
   * Update Grant
   */
  public static function update($arr)
  {
    return oxygen_http_post('/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/grants/api/update', $arr);
  }

  /**
   * Get attributionID using email or phone number
   * 
   */
  public static function get_attribution($arr, $type = 'PersonalAccount')
  {
    $servBody = oxygen_http_post('/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/attribution/api/get', $arr);
    $attribute = null;
    foreach ($servBody as $value) {
      if ($value['AccountType'] == $type) {
        $attribute = $value;
        break;
      }
    }

    return $attribute;
  }
}
