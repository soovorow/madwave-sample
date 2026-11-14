<?php

/** @var $order \common\models\Order */

$track_code = $order->track_code;

?>

<div>
    <h2>Благодарим Вас за покупку. Ваш заказ #<?= $order->getId() ?> на сайте madwave.ru!</h2>

    <p>Отследить отправление вы можете на сайте - <a href="https://gdeposylka.ru/">https://gdeposylka.ru/</a></p>

    <p>Номер отправления: <?= $track_code; ?></p>

    <br/>
    E-mail: managerinternet@madwave.ru <br>
    Skype ID: shalaginaolga <br>
    C уважением, Компания Mad Wave. <br>
    www.madwave.ru <br>
</div>


<tr>
    <td valign="top" id="templateBody"></td>
</tr>