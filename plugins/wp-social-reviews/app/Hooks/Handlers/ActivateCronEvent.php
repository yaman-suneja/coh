<?php

namespace WPSocialReviews\App\Hooks\Handlers;

use WPSocialReviews\Framework\Support\Arr;

class ActivateCronEvent
{
	public function activate()
    {
		if (!wp_next_scheduled('wpsr_cron_job')) {
			wp_schedule_event(time(), 'hourly', 'wpsr_cron_job');
		}

        $twicedailyHook = 'wpsr_scheduled_twicedaily';
        if (!wp_next_scheduled($twicedailyHook)) {
            wp_schedule_event(time(), 'twicedaily', $twicedailyHook);
        }
    }
}
