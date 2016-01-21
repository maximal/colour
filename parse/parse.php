<?php
/**
 * Парсим цвета из колдунщика Яндекса.
 *
 * @link https://yandex.ru/search/?text=красный
 *
 * @author MaximAL
 * @link http://maximals.ru
 * @link http://sijeko.ru
 * @copyright © MaximAL, Sijeko 2015
 **/


//$initialValue = 'ff0000';
$initialValue = 'fffafa';  // Белоснежный, чтобы начать с белого
$saveColours = false;      // Сохранять цвета в кеше?

$value = $initialValue;
$result = [];

// Создать папку кеша, если она нужна, и в ней игнор-файл
if ($saveColours && !is_dir('cache')) {
	mkdir('cache');
	file_put_contents(
		'cache' . DIRECTORY_SEPARATOR . '.gitignore',
		"*\n!.gitignore\n"
	);
}


// Поехали!
while (true) {
	$colors = getColors($value);
	$break = false;
	foreach ($colors as $color) {
		echo "\t", $color->value, ' — ', $color->name, PHP_EOL;
		if ($saveColours) {
			saveColor($color);
		}
		$result []= $color;
		$value = $color->value;
		if ($value === $initialValue) {
			$break = true;
			break;
		}
	}
	if ($break) {
		break;
	}
}

// Сохраняем цвета и выходим
file_put_contents('..' . DIRECTORY_SEPARATOR . 'colours.json', json_encode($result, JSON_UNESCAPED_UNICODE));

echo 'Всего цветов: ', count($result), PHP_EOL;


exit(0);



/**
 * Получить цвета Яндекса
 * @param string $value Начальное значение
 * @return \stdClass[]
 */
function getColors($value)
{
	$res = json_decode(file_get_contents('https://yandex.ru/search/wizardsjson?type=colors&text=' . $value));
	
	if (!isset($res[0])) {
		return [];
	}

	$res = $res[0];
	if (!isset($res->next)) {
		return [];
	}
	
	return $res->next;
}


/**
 * Сохранить цвет в кеше
 * @param \stdClass $color Цвет
 */
function saveColor($color)
{
	file_put_contents(
		'cache' . DIRECTORY_SEPARATOR . $color->value . ' — ' . $color->name,
		json_encode($color, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
	);
}
