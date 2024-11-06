<?php


/**
 * Signup using email or phone number
 */
function oxygen_signup($arr)
{
  // $urlSign = get_site_url(null, 'LivingScript_0070/SignUp', 'https');
  return oxygen_http_post("/LivingScript_0070/SignUp", $arr);
}
