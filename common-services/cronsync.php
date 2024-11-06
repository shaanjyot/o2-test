<?php

function oxygen_cron_sync($event) {
  try {
    // wp_remote_post(
    //   'https://enqfsx7p6stxm.x.pipedream.net',
    //   array(
    //     'method'  => 'POST',
    //     'headers' => [
    //       'Content-Type' => 'application/json',
    //     ],
    //     'body'    => json_encode(['Time' => date('c', $event->timestamp + 60),
    //     'Hook' => $event->hook,
    //     'Action' => 'get',
    //     'Arguments' => [
    //       'URL' => get_home_url().'/wp-cron.php'
    //     ]])
    //   )
    // );
      $exclude_list = ["action_scheduler_run_queue"];
      if (!in_array($event->hook, $exclude_list)) {
        wp_remote_post(
            'http://localhost/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/timer/api/addTimer',
            [
              'method'  => 'POST',
              'headers' => [
                'Content-Type' => 'application/json',
              ],
              'body'    => json_encode([
                'Time' => date('c', $event->timestamp + 60),
                'Action' => 'get',
                'Arguments' => [
                  'URL' => get_home_url().'/wp-cron.php'
                ]
              ])
            ]
          );
      }

  
  } catch (\Throwable $th) {
    throw $th;
  }
  return $event;
}

add_filter('schedule_event', 'oxygen_cron_sync', 10, 1);