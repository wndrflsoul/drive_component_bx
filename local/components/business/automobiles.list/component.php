<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

// Подключение необходимых модулей
use Bitrix\Main\Loader;

Loader::IncludeModule("iblock");
Loader::includeModule("highloadblock");

$highloadBlockId = 1;
$carsIblockId = 1;
$startDateTime = new DateTime($_GET['start_time']);
$endDateTime = new DateTime($_GET['end_time']);

// Поиск пользователя в БД с необходимой должностью
$userDbResult = \Bitrix\Main\UserTable::getList(
    array(
        'select' => array('ID', 'NAME', 'WORK_POSITION'),
        'filter' => array('ID' => $USER->GetID()),
    )
);
$userData = $userDbResult->fetch();

// Выборка автомобилей в соответствии с должностью
if ($userData['WORK_POSITION'] == "chief_admin") {
    $filter = array(
        "IBLOCK_ID" => $carsIblockId,
        "PROPERTY_CLASS_VALUE" => ['Премиум'],
    );
} elseif ($userData['WORK_POSITION'] == "admin") {
    $filter = array(
        "IBLOCK_ID" => $carsIblockId,
        "PROPERTY_CLASS_VALUE" => ['Комфорт'],
    );
} elseif ($userData['WORK_POSITION'] == "manager") {
    $filter = array(
        "IBLOCK_ID" => $carsIblockId,
        "PROPERTY_CLASS_VALUE" => 'Эконом',
    );
} else {
    echo "Список недоступен";
    return;
}

// Получение списка автомобилей
$carsResult = CIBlockElement::GetList(
    array(),
    $filter,
    false,
    false,
    ['ID', 'NAME', 'PROPERTY_CLASS', 'PROPERTY_DRIVER_NAME']
);

$carsList = [];

while ($car = $carsResult->Fetch()) {
    $carsList[] = $car;
}

// Связь с ХЛ-блоком и проверка занятости автомобилей
$highloadData = Bitrix\Highloadblock\HighloadBlockTable::getById($highloadBlockId)->fetch();
$highloadEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highloadData);
$entityDataClass = $highloadEntity->getDataClass();

foreach ($carsList as $key => $car) {
    $reservationDataResult = $entityDataClass::getList(
        array(
            "select" => array("*"),
            "filter" => array("UF_CAR" => $car['ID']),
        )
    );

    while ($reservationData = $reservationDataResult->Fetch()) {
        $bookedTripStart = new DateTime($reservationData["UF_DRIVE_START"]);
        $bookedTripFinish = new DateTime($reservationData["UF_DRIVE_FINISH"]);

        if (
            ($startDateTime >= $bookedTripStart && $startDateTime <= $bookedTripFinish) ||
            ($endDateTime >= $bookedTripStart && $endDateTime <= $bookedTripFinish) ||
            ($startDateTime <= $bookedTripStart && $endDateTime >= $bookedTripFinish)
        ) {
            unset($carsList[$key]);
        }
    }
}

$arResult['CARS_LIST'] = $carsList;

$this->IncludeComponentTemplate();
?>