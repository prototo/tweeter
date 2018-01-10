<?php

/**
 * Post options meta box
 */
function tweeter_post_metabox($post) {
    add_meta_box(
        'tweeter_post',
        'Tweeter',
        'tweeter_post_metabox_cb',
        'post',
        'normal'
    );
}
function tweeter_post_metabox_cb($post, $metabox) {
    // doesn't look like a new post, don't show the metabox
    $tweeted_post = get_post_meta($post->ID, TWEETER_TWEETED_META_KEY, true);
    if ($tweeted_post === TWEETER_TWEET_POS) {
        $tweet_post = get_post_meta($post->ID, TWEETER_TWEET_META_KEY, true);
        if ($tweet_post === TWEETER_TWEET_POS) {
            $tweet_id = get_post_meta($post->ID, TWEETER_TWEET_ID_KEY, true);
            echo "<p>This post was already published. <a href=\"https://twitter.com/statuses/${tweet_id}\">Click here to see the tweet.</a></p>";
        } else {
            echo '<p>This post was already published. The post was not tweeted.</p>';
        }
        return $post;
    }

    echo '<label>Tweet this post? <input type="checkbox" name="' . TWEETER_TWEET_POST_KEY . '" checked="checked" value="' . TWEETER_TWEET_POST_VALUE . '"></input></label>';
    wp_nonce_field(basename( __FILE__ ), TWEETER_TWEET_POST_NONCE_KEY);
}
function tweeter_post_metabox_save($id, $post) {
    $app_post = !isset($_POST[TWEETER_TWEET_POST_NONCE_KEY]);

    if ($app_post) {
        $tweet_post = TWEETER_TWEET_POS;
    } else {
        $nonce = $_POST[TWEETER_TWEET_POST_NONCE_KEY];
        $nonce_verify = wp_verify_nonce($nonce, basename( __FILE__));

        if (!$nonce_verify) {
            $tweet_post = TWEETER_TWEET_NEG;
        } else {
            $tweet_post_value = $_POST[TWEETER_TWEET_POST_KEY];
            $tweet_post = ($tweet_post_value == TWEETER_TWEET_POST_VALUE)
                ? TWEETER_TWEET_POS
                : TWEETER_TWEET_NEG;
        }
    }

    add_post_meta(
        $id,
        TWEETER_TWEET_META_KEY,
        $tweet_post,
        true
    );
}
add_action('add_meta_boxes', 'tweeter_post_metabox');
add_action('publish_post', 'tweeter_post_metabox_save', 9, 2);
