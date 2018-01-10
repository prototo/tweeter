<?php

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__ . '/api.php');

$api = new API(
    '944941371043078144-o7SIi678ZOylCRiEZMTqOYzLeKDvzPO',
    'Yt1GWf3BiXojGL9TgwJv3CNvssWaohqnZzqZprVOiE6jj',
    'jqnrl4SF79JNO9wJlMhTkYuvv',
    'ZAFrIUmpoD4VK0nln35Km6ZnBGV2oZGHQLkYbPpEvkmET6hz1f'
);

$api->tweet("You've Got Mail - Shit http://localhost/?post_type=post&p=49");
