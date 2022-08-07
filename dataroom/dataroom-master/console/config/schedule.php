<?php

$schedule->command('dataroom/publish-rooms')->dailyAt('00:00');
$schedule->command('dataroom/expire-rooms')->everyMinute();
$schedule->command('dataroom/archive-rooms')->dailyAt('00:10');

$schedule->command('dataroom/notify-publication')->hourly();
$schedule->command('dataroom/notify-expiration')->hourly();
$schedule->command('dataroom/notify-hearing')->dailyAt('00:15');

$schedule->command('notify/notify/send')->everyMinute();

// Deprecated
//$schedule->command('dataroom/notify-updates')->dailyAt('00:30');