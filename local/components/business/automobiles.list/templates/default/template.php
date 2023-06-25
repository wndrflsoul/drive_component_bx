<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>

<h3>Выберите время поездки:</h3>
<form action="<?= $APPLICATION->GetCurPage() ?>" method="GET">
    <label for="start_time">Время начала:</label>
    <input type="datetime-local" name="start_time" id="start_time" required><br>
    <label for="end_time">Время окончания:</label>
    <input type="datetime-local" name="end_time" id="end_time" required><br>
    <input type="submit" value="Показать свободные автомобили">
</form>

<?php if (!empty($arResult['CARS_LIST'])): ?>
    <h3>Свободные автомобили:</h3>
    <ul>
        <?php foreach ($arResult['CARS_LIST'] as $car): ?>
            <li>
                <b>Модель:</b> <?= $car['NAME'] ?><br>
                <b>Класс:</b> <?= $car['PROPERTY_CLASS_VALUE'] ?><br>
                <b>Имя водителя:</b> <?= $car['PROPERTY_DRIVER_NAME_VALUE'] ?>
            </li>
        <?php endforeach; ?>
        <?php
        
        ?>
    </ul>
<?php else: ?>
    <p>К сожалению, на выбранное время нет свободных автомобилей.</p>
<?php endif; ?>
