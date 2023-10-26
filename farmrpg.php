<?php
function _retriever($url, $data = NULL, $header = NULL, $method = 'GET')
{
    $cookie_file_path = dirname(__FILE__) . "/cookie/farmrpg.txt";
    $datas['http_code'] = 0;
    if ($url == "")
        return $datas;
    $data_string = '';
    if ($data != NULL) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data_string .= $key . '=' . $value . '&';
            }
        } else {
            $data_string = $data;
        }
    }

    $ch = curl_init();
    if ($header != NULL)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
    curl_setopt(
        $ch,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36"
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

    if ($data != NULL) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        // curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    }

    $html = curl_exec($ch);
    //echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //echo $html;
    if (!curl_errno($ch)) {
        $datas['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($datas['http_code'] == 200) {
            $datas['result'] = $html;
        }
    }
    curl_close($ch);
    return $datas;
}

function plantall(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=plantall&id=341041', NULL, $header, 'POST');
    return $html;
}

function harvestall(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=harvestall&id=341041', NULL, $header, 'POST');
    return $html;
}

function buyseed(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=buyitem&id=12&qty=8', NULL, $header, 'POST');
    return $html;
}

function sellcrop(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=sellitem&id=11&qty=8', NULL, $header, 'POST');
    return $html;
}

function fishing(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=fishcaught&id=1&r=460180', NULL, $header, 'POST');
    return $html;
}

function sellfish(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $response1 = _retriever('https://farmrpg.com/worker.php?go=sellitem&id=17&qty=17', NULL, $header, 'POST');
    $response2= _retriever('https://farmrpg.com/worker.php?go=sellitem&id=24&qty=91', NULL, $header, 'POST');
    
    return array(
        'first_fish_sale' => $response1,
        'second_fish_sale' => $response2
    );
}

function bait(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=buyitem&id=18&qty=52', NULL, $header, 'POST');
    return $html;
}

function getWormCount($html) {
    preg_match('/Worms: <strong>(\d+)<\/strong>/', $html, $matches);
    if (isset($matches[1])) {
        return (int) $matches[1];
    }
    return 0;
}

function explore(){
    $header = array(
        'Origin: https://farmrpg.com',
        'Referer: https://farmrpg.com/index.php',
    );
    $html = _retriever('https://farmrpg.com/worker.php?go=explore&id=1', NULL, $header, 'POST');
    return $html;
}

function auto(){
    $data = array();
    $data['harvest'] = harvestall();
    $data['sell'] = sellcrop();
    $data['buy'] = buyseed();
    $data['plant'] = plantall();

    return $data;
}

function autofishing(){
    $data = array();

    $data['fishing'] = fishing();

    $wormPage = _retriever('https://farmrpg.com/worker.php?cachebuster=144075&go=baitarea&id=1');
    $wormCount = getWormCount($wormPage['result']);

    if($wormCount <= 50) {
        $data['buyBait'] = bait();
    }

    $data['sell'] = sellfish();

    return $data;
}

function autoExplore(){
    $data = array();
    $data['explore'] = explore();
    return $data;
}

$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($action == "farm") {
    $result = auto();
} elseif ($action == "fish") {
    $result = autofishing();
} elseif ($action == "explore") {
    $result = autoExplore();
} else {
    $result = array('error' => 'Invalid action');
}

echo json_encode($result);