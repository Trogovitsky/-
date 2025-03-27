<?php
$url = 'https://weather.rambler.ru/v-moskve/now/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$htmlContent = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

// Проверка, что HTML-контент загружен
if (empty($htmlContent)) {
    echo json_encode(['error' => 'HTML content is empty']);
    exit;
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($htmlContent);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Парсинг температуры
$gyfkElement = $xpath->query("//div[contains(@class, 'GyfK')]")->item(0);
$temperature = '';
if ($gyfkElement) {
    $gyfkText = $gyfkElement->textContent;
    // Удаляем символ °
    $temperature = str_replace('°', '', $gyfkText);
}

// Парсинг давления
$pressureElement = $xpath->query("//div[contains(@class, 'hjtR') and contains(@class, 'pressure') and contains(@class, 'HbwD') and contains(@class, 'aT_0')]")->item(0);
$pressure = '';
if ($pressureElement) {
    $pressureText = $pressureElement->textContent;
    // Извлекаем числовое значение давления
    preg_match('/(\d+)\s*мм/', $pressureText, $matches);
    if (isset($matches[1])) {
        $pressure = $matches[1];
    } else {
        echo json_encode(['error' => 'Pressure value not found in text: ' . $pressureText]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Pressure element not found']);
    exit;
}

// Сохранение данных в JSON
$jsonData = json_encode([
    'temperature' => $temperature,
    'pressure' => $pressure
], JSON_PRETTY_PRINT);

$filePath = '/home/a1024444/domains/ssir-team.ru/public_html/tweather/weather_data.json';
file_put_contents ($filePath, $jsonData);

echo 'Data saved to weather_data.json';
?>
