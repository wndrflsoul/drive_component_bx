<?php
use Bitrix\Main\Loader;
use Bitrix\Iblock;

// Проверяем доступность модуля "iblock"
if (!Loader::includeModule('iblock')) {
    return;
}

// Получаем список автомобилей из инфоблока
$arCars = [];
$rsCars = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    ['IBLOCK_ID' => $arParams['IBLOCK_ID']],
    false,
    false,
    ['ID', 'NAME']
);
while ($arCar = $rsCars->Fetch()) {
    $arCars[$arCar['ID']] = $arCar['NAME'];
}

// Добавляем параметр списка автомобилей в .parameters.php
$arComponentParameters = array(
    // ...
    'PARAMETERS' => array(
        // ...
        'CAR_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Доступные автомобили:',
            'TYPE' => 'LIST',
            'VALUES' => $arCars,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y',
        ),
        // ...
    ),
    // ...
);
?>