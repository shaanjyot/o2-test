<?php

/**
 * Channel subscribe and publish
 */
class OxygenChannel
{
  function oxygen_id_get($service)
  {
    $url = '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/gdn/api/getOxygenID';
    $res = oxygen_http_post($url, [
      'GDN' => $service
    ]);
    return $res;
  }
  function oxygen_channel_name_create($oxygen_id, $query)
  {
    $url = '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/oes/api/encodeStrings';
    $res = oxygen_http_post($url, [$oxygen_id, $query]);
    return $res;
  }

  function channel_port_create($oxygen_id, $hook_url)
  {
    $url = '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/propagator/api/addPort';
    $res = oxygen_http_post($url, [
      'ComponentOID' => $oxygen_id,
      'PortType'    => 'Input',
      'WebhookURL'  => $hook_url
    ]);
    return $res;
  }

  function channel_subscribe($channel, $port)
  {
    $url = '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/propagator/api/subscribeToChannel';
    $res = oxygen_http_post($url, [
      'Channel'   => $channel,
      'InputPort' => $port
    ]);
    return $res;
  }


  public function subscribe($gdn, $hook)
  {
    $gdn = new OxygenGDN();
    $oxygen_id = $gdn->getOxygenID($gdn);
    $channel_name = OxygenOES::get([$oxygen_id, 'INSERT']);
    $port1 = $this->channel_port_create($oxygen_id, $hook . '&mode=INSERT');
    $sub = $this->channel_subscribe($channel_name, $port1);
  }
}
