<?php
$string = $_GET['string'];

$return_body = [];
$pattern = '/^(?:(?:.*?)(?:FALP\d{6}))?(?:C)?(?:QR[^N]*(?=N))?(.*?)(?:\s+)(.*?)(\d{5})\s*$/';
if (preg_match_all($pattern, $string, $matches)) {
    $return_body['matches'] = $matches;

    $return_body['first'] = $matches[1][0];
    $return_body['second'] = $matches[2][0];
    $return_body['third'] = $matches[3][0];
} 

echo json_encode($return_body);