<?php
/**
 * Plugin Name: Tweeter
 */
define('__ROOT__', dirname(__FILE__));

const TWEETER_TWEET_POST_KEY = 'tweeter_tweet_post';
const TWEETER_TWEET_POST_NONCE_KEY = 'tweeter_tweet_post_nonce';
const TWEETER_TWEET_POST_VALUE = 'Tweet';
const TWEETER_TWEET_META_KEY = 'tweeter_tweet';
const TWEETER_TWEETED_META_KEY = 'tweeter_tweeted';
const TWEETER_TWEET_POS = 'Yes';
const TWEETER_TWEET_NEG = 'No';
const TWEETER_TWEET_ID_KEY = 'tweeter_tweet_url_key';

require_once(__ROOT__ . '/init.php');
require_once(__ROOT__ . '/menu.php');
require_once(__ROOT__ . '/tweet.php');
require_once(__ROOT__ . '/post.php');
