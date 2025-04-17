<?php
function camelAndSnakeCaseToSentenceCaseConverter($string) {
    $result = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
    $result = preg_replace('/[_]/', ' ', $result);
    return ucfirst(strtolower($result));
}


function extractObjectPropertiesToKeyPairValues($input) {
    $keyValuePairs = [];

    foreach ($input as $key => $value) {
        $keyValuePairs[] = [
            'details_key' => camelAndSnakeCaseToSentenceCaseConverter($key),
            'details_value' => $value
        ];
    }

    return $keyValuePairs;
}