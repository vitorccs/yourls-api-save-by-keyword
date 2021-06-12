<?php

/*
 Plugin Name: Save by Keyword
 Plugin URI: https://github.com/vitorccs/yourls-api-save-by-keyword
 Description: Define a custom API action 'save_by_keyword' which either creates a new short URL or update an existing one by a giving keyword
 Version: 1.0.1
 Author: Vitor Siqueira
 Author URI: https://github.com/vitorccs
 */

yourls_add_filter('api_action_save_by_keyword', 'vccs_api_save_by_keyword');

function vccs_api_save_by_keyword()
{
    $keyword = $_REQUEST['keyword'] ?? '';
    $url = $_REQUEST['url'] ?? '';
    $title = $_REQUEST['title'] ?? '';

    if (empty($keyword)) {
        return [
            'statusCode' => 400,
            'status' => 'fail',
            'simple' => 'error: missing "keyword" param',
            'message' => 'error: missing "keyword" param',
        ];
    }

    if (empty($url)) {
        return [
            'statusCode' => 400,
            'status' => 'fail',
            'simple' => 'error: missing "url" param',
            'message' => 'error: missing "url" param',
        ];
    }

    $keyword = yourls_get_protocol($keyword)
        ? yourls_get_relative_url($keyword)
        : $keyword;

    // Prevent fail status for zero rows affected
    if (vccs_no_parameter_changes($url, $keyword, $title)) {
        $response = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => yourls__('The keyword already exists with same title and long URL')
        ];
    } else {
        $response = yourls_keyword_is_free($keyword)
            ? yourls_add_new_link($url, $keyword, $title)
            : yourls_edit_link($url, $keyword, $keyword, $title);

        // normalize response fields
        $response = array_intersect_key($response, [
            'statusCode' => '',
            'status' => '',
            'message' => ''
        ]);

        // supply if missing field
        if (!isset($response['statusCode'])) {
            $response['statusCode'] = $response['status'] === 'success'
                ? 200
                : 400;
        }
    }

    $response['keyword'] = $keyword;
    $response['shorturl'] = yourls_get_yourls_site() . '/' . $keyword;

    krsort($response);

    return $response;
}

function vccs_no_parameter_changes($url, $keyword, $title)
{
    global $ydb;

    $table = YOURLS_DB_TABLE_URL;
    $url = yourls_sanitize_url($url);
    $keyword = yourls_sanitize_string($keyword);
    $title = yourls_sanitize_title($title);

    $bindings = ['url' => $url, 'keyword' => $keyword, 'title' => $title];
    $sql = "SELECT COUNT(keyword) FROM `$table` WHERE `url` = :url AND `title` = :title AND `keyword` = :keyword;";
    $count = intval($ydb->fetchValue($sql, $bindings));

    return $count > 0;
}

?>
