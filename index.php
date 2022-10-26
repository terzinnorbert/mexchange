<?php

if ('POST' !== $_SERVER['REQUEST_METHOD']) {
    echo file_get_contents('index.html');

    return;
}

if (!file_exists('cache.json')) {
    $cache = [
        'date' => strtotime('-1 day'),
        'usd'  => 0,
        'eur'  => 0,
    ];
} else {
    $cache = json_decode(file_get_contents('cache.json'), true, 512, JSON_THROW_ON_ERROR);
}

function getExchange($type)
{
    $data = file_get_contents('https://www.mastercard.us/settlement/currencyrate/conversion-rate?fxDate=0000-00-00&transCurr=' . $type . '&crdhldBillCurr=HUF&bankFee=0&transAmt=1');
    $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

    return $data['data']['conversionRate'];
}

if ($cache['date'] < strtotime('-6 hours')) {
    $cache['usd'] = getExchange('USD');
    $cache['eur'] = getExchange('EUR');
    $cache['date'] = time();
    file_put_contents('cache.json', json_encode($cache, JSON_THROW_ON_ERROR));
}

echo json_encode($cache, JSON_THROW_ON_ERROR);