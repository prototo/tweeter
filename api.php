<?php

define('TWITTER_API_BASE', 'https://api.twitter.com/');

class API {
    protected $access_token;
    protected $access_token_secret;
    protected $consumer_key;
    protected $consumer_secret;

    protected $bearer_token;

    public function __construct(
        $access_token,
        $access_token_secret,
        $consumer_key,
        $consumer_secret
    ) {
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
    }

    /**
     * Make a remote request. Uses wp_remote_* function if they're available,
     * otherwise falls back to the native curl handler.
     */
    private function makeRequest($method = 'POST', $url, $body = null, $headers = []) {
        $url = TWITTER_API_BASE . $url;

        if (function_exists('wp_remote_get')) {
            $args = [
                'method' => strtoupper($method),
                'headers' => $headers,
                'body' => $body
            ];
            $response = wp_remote_request($url, $args);
            return wp_remote_retrieve_body($response);
        }
        return $this->makeCurl($method, $url, $body, $headers);
    }

    /**
     * Make a curl request with the given params, supports GET or POST
     */
    private function makeCurl($method = 'POST', $url, array $body = [], array $headers = []) {
        $ch = curl_init();

        $curl_headers = [];
        foreach ($headers as $key => $val) {
            $curl_headers []= sprintf('%s: %s', $key, $val);
        }

        $is_post = strtoupper($method) == 'POST';
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        if ($body) {
            $param_string = $this->toParameterString($body);
            if ($is_post) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param_string);
            } else {
                $url .= '?' . $param_string;
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * Create a parameter string from an associative array, with configurable
     * delimeters. Default string format matches a URL query string.
     */
    private function toParameterString($params, $delim='&', $order=true, $wrap=false) {
        if ($order) {
            ksort($params);
        }
        $param_string = '';
        $wrapper = $wrap ? '"' : '';
        foreach ($params as $key => $val) {
            if (strlen($param_string) > 0) {
                $param_string .= $delim;
            }
            $param_string .= rawurlencode($key);
            $param_string .= '=';
            $param_string .= $wrapper . rawurlencode($val) . $wrapper;
        }
        return $param_string;
    }

    /**
     * Generate the OAuth signature for the given request parameters. OAuth values
     * should all be present, besides the signature.
     */
    private function getOAuthSignature($method, $url, $oauth_values, $request_params) {
        $params = array_merge($oauth_values, $request_params);
        $param_string = $this->toParameterString($params);
        $signature_base = sprintf(
            '%s&%s&%s',
            strtoupper($method),
            rawurlencode($url),
            rawurlencode($param_string)
        );
        $signing_key = sprintf(
            '%s&%s',
            rawurlencode($this->consumer_secret),
            rawurlencode($this->access_token_secret)
        );
        return base64_encode(hash_hmac('sha1', $signature_base, $signing_key, true));
    }

    private function makeSignedRequest($method='POST', $url, $request_params) {
        $full_url = TWITTER_API_BASE . $url;
        $oauth_values = [
            'oauth_consumer_key' => $this->consumer_key,
            'oauth_nonce' => '5a524c0242fdf', // uniqid(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->access_token,
            'oauth_timestamp' => 1515342850, // time(),
            'oauth_version' => '1.0'
        ];
        $signature = $this->getOAuthSignature($method, $full_url, $oauth_values, $request_params);
        $oauth_values['oauth_signature'] = $signature;
        $headers = [
            'Authorization' => 'OAuth ' . $this->toParameterString($oauth_values, ", ", true, true)
        ];
        $output = $this->makeRequest($method, $url, $request_params, $headers);
        $response = is_string($output) ? json_decode($output, true) : $output;
        return $response;
    }

    /**
     * Make a tweet with the given status and return the tweet URL
     */
    public function tweet($status) {
        $url = '1.1/statuses/update.json';
        $response = $this->makeSignedRequest('POST', $url, [
            'status' => $status
        ]);

        if (!isset($response['id'])) {
            return false;
        }
        return $response['id'];
    }
}
