<?php
/**
 * Tweeter tweet
 */
require_once(__ROOT__ . '/api.php');

function tweeter_tweet($id, $post) {
    // if tweet_post is false, or if we've already tweeted about this post, return
    $tweet_post = get_post_meta($id, TWEETER_TWEET_META_KEY, true) === TWEETER_TWEET_POS;
    $tweeted_post = get_post_meta($id, TWEETER_TWEETED_META_KEY, true) === TWEETER_TWEET_POS;

    if ($tweeted_post) {
        return;
    }
    add_post_meta($id, TWEETER_TWEETED_META_KEY, TWEETER_TWEET_POS);
    if (!$tweet_post) {
        return;
    }

    $options = get_option('tweeter_settings');

    $api = new API(
        $options['tweeter_oauth_access_token'],
        $options['tweeter_oauth_access_token_secret'],
        $options['tweeter_consumer_key'],
        $options['tweeter_consumer_secret']
    );

    $format = $options['tweeter_message_format'];
    $variables = $options['tweeter_message_variables'];
    $status = tweeter_get_status($id, $post, $format, $variables);

    if (empty($status)) {
        throw new Exception(sprintf(
            '$status is empty. Format: "%s" Variables: "%s"',
            $format,
            $variables
        ));
    }

    $tweet_id = $api->tweet($status);
    if ($tweet_id) {
        add_post_meta($id, TWEETER_TWEET_ID_KEY, $tweet_id);
    }
}
add_action('publish_post', 'tweeter_tweet', 10, 2);

function tweeter_get_status($id, $post, $format, $variables) {
    $attributes = array_map(function($attr) use ($id, $post) {
        list($attr, $func) = explode('#', $attr);

        switch ($attr) {
            case 'link':
                $val = get_permalink($id);
                break;
            case 'category':
                $categories = wp_get_post_categories($id);
                $val = get_category(current($categories))->name;
                break;
            default;
                $val = $post->$attr;
        }

        if ($func) {
            return call_user_func($func, $val);
        }
        return $val;
    }, explode(',', $variables));
    $args = array_merge(
        [$format],
        $attributes
    );
    $status = call_user_func_array('sprintf', $args);
    return str_replace(['\r', '\n'], "\n", $status);
}
