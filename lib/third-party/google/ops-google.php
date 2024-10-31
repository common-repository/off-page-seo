<?php

/*
 * Based on:
 * https://github.com/googleapis/google-api-php-client
 *
 * https://myaccount.google.com/u/0/permissions?pli=1
 * query
 * page
 *
 */

class ops_google
{

    var $client;
    var $tokens;

    function __construct()
    {


        include_once('vendor/autoload.php');

        $this->client = new Google_Client();
        //        $this->client->setScopes('https://www.googleapis.com/auth/analytics.readonly');
        $this->client->setScopes([
            'https://www.googleapis.com/auth/webmasters.readonly'
        ]);
        $this->client->setAccessType('offline');
        $this->client->setApplicationName('Off Page SEO');
        $this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        $this->client->setClientId('547283294552-qdtvvpcadvdomga6dh0re3q185t5v4gv.apps.googleusercontent.com');
        $this->client->setClientSecret('7X8yC_eTlMhjFZLXhqgv2kOh');


        //AUTHORIZE THIS SHIT
        global $ops;
        $auth_code = $ops->get_settings('ops_google_auth_code');

        // not empty code?
        if (!empty($auth_code)) {
            $tokens = $ops->get_settings('ops_google_tokens');

            if (empty($tokens)) {
                // first time getting tokens

                try {
                    $this->client->fetchAccessTokenWithAuthCode($auth_code); // authorize myself
                } catch (Exception $e) {
                }

                $access_tokens = $this->client->getAccessToken();
                if (!empty($access_tokens)) {
                    $ops->save_settings($access_tokens, 'ops_google_tokens');
                    $this->tokens = $access_tokens;
                    $ops->create_log_entry('google', 'api', '', 'First time Google tokens obtained.');
                } else {
                    $ops->create_log_entry('google', 'api', '', 'We could not obtain Google access tokens.');
                }
            } else {
                // check refresh tokens

                $refresh_token = $tokens['refresh_token'];

                if ($tokens['created'] + $tokens['expires_in'] - 10 < time()) {
                    // tokens had expired, refresh!

                    $this->client->refreshToken($refresh_token);
                    $tokens = $this->client->getAccessToken();
                    $tokens['refresh_token'] = $refresh_token;

                    $ops->create_log_entry('google', 'api', '', 'Google tokens refreshed.');

                    $ops->save_settings($tokens, 'ops_google_tokens');
                }

                $this->tokens = $tokens;

            }


        }
    }

    function get_auth_url()
    {
        return $this->client->createAuthUrl(); // set auth url for later use
    }


    function get_google_data($dimension, $start, $end, $limit = '100')
    {

        if (empty($this->tokens)) {
            return false;
        }

        $transient_key = 'ops_google_' . sanitize_title($dimension) . sanitize_title($start) . sanitize_title($end);

        delete_transient($transient_key);


        if (false === ($output = get_transient($transient_key))) {


            $this->client->setAccessToken($this->tokens);

            $service = new Google_Service_Webmasters($this->client);
            $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest;
            $request->setStartDate($start);
            $request->setEndDate($end);
            $request->setDimensions(array($dimension)); // page, query, country, device, https://developers.google.com/webmaster-tools/v3/searchanalytics/query
            $request->setRowLimit($limit);
            //                    $request->setSearchType('web');

            try {
                $qsearch = $service->searchanalytics->query(ops_get_home_url(), $request);

            } catch (Exception $e) {
                global $ops;
                $ops->create_log_entry('error', 'google', 'search_console', $e->getMessage());

                return [];
            }

            $response = $qsearch->getRows();

            $output = $this->process_query_response($response);

            set_transient($transient_key, $output, (60 * 60));


        }

        return $output;


    }

    function process_query_response($response)
    {
        $output = array();
        foreach ($response as $row) {
            $output[$row->keys[0]]['clicks'] = $row->clicks;
            $output[$row->keys[0]]['ctr'] = $row->ctr;
            $output[$row->keys[0]]['position'] = $row->position;
            $output[$row->keys[0]]['impressions'] = $row->impressions;
        }
        return $output;
    }
}
