<?php

$this->title = 'Способы доставки | Mad Wave';

$this->registerMetaTag(['name' => 'description', 'content' => '  Самовывоз –  бесплатно  *Бесплатный самовывоз доступен только через фирменную сеть розничных магазинов Mad Wave.  Срок доставки: от 2 до 30 рабочих дней, в зависимости от города доставки. Для просмотра всех доступных пунктов самовывоза перейдите по ссылке.']);

$this->params['breadcrumbs'][] = 'Способы доставки';
?>

<div class="container">
    <div class="row mb60">
        <div id="variant">
            <div class="col-xs-12 top-caption">
                <h1>Способы доставки</h1>
                <div class="alert alert-warning">
                    Срок комплектации заказов от 2 до 7 дней на складе в городе Санкт-Петербург. После того как заказ упакован, он передается в службу доставки.
                </div>

                <br>
            </div>
            <div class="col-xs-12">

                <div class="delivery-wrapper active">
                    <div class="delivery-item-toggle active"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Самовывоз</div>
                    <div class="delivery-content">
                        <p>бесплатно</p>
                        <div class="delivery-content-hidden active">
                            <p>
                                *Бесплатный самовывоз доступен только через фирменную сеть розничных магазинов Mad Wave.
                                Срок доставки: от 2 до 30 рабочих дней, в зависимости от города доставки.
                                Для просмотра всех доступных пунктов самовывоза перейдите по <?= \yii\helpers\Html::a('ссылке', ['/site/stores']) ?>.
                                Срок хранения заказа в пункте самовывоза: 10 календарных дней с момента поступления заказа в фирменный магазин.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Бесплатная доставка</div>
                    <div class="delivery-content">
                        <div class="delivery-content-hidden">
                            <p>Осуществляется Почтой России в любой город РФ при условии оформления заказа на сумму от 4000р</p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Доставка Почтой России в любой город РФ</div>
                    <div class="delivery-content">
                        <p>
                            280 р. - обычным отправлением.
                            <br>
                            430 р. - отправлением 1 класса.
                            <br>
                            Возможна оплата в почтовом отделении Получателя.
                        </p>
                        <div class="delivery-content-hidden">
                            <p>
                                Внимание! При получении и оплате заказа, доставленного наложенным платежом, взимается плата по тарифу на пересылку внутренних простых почтовых переводов. Процент оплаты зависит от пересылаемой суммы (не менее 4% от суммы заказа). При 100% предоплате комиссия не взимается
                            </p>
                            <p>
                                Срок доставки: от 1 до 3 недель.
                            </p>
                            <p>
                                Сроки доставки могут отличаться в зависимости от удаленности региона доставки. Точные сроки вы можете расчитать на сайте Почты России.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Доставка Курьером в любой город РФ</div>
                    <div class="delivery-content">
                        <p>от 800 руб.</p>
                        <div class="delivery-content-hidden">
                            <p>
                                Сумма высчитывается с учетом удаленности города относительно Санкт-Петербурга, объема и веса заказа. Срок доставки: от 2 до 7 рабочий дней в зависимости от отдаленности региона.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Доставка Курьером по СПб</div>
                    <div class="delivery-content">
                        <p>в пределах КАД – 200 руб., за пределами – от 350 руб.;</p>
                        <div class="delivery-content-hidden">
                            <p>
                                Срок доставки: до 2 рабочих дней.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Доставка Курьером по Москве</div>
                    <div class="delivery-content">
                        <p>в пределах МКАД - 400 руб., за пределами - от 500 руб.;</p>
                        <div class="delivery-content-hidden">
                            <p>
                                Срок доставки: до 2 рабочих дней.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="delivery-wrapper">
                    <div class="delivery-item-toggle"><i class="fa fa-caret-down"></i></div>
                    <div class="delivery-name">Адрес для возвратов</div>
                    <div class="delivery-content">
                        <div class="delivery-content-hidden">
                            <p>
                                ООО "Суперсвим.ру" <br>
                                195030, г. Санкт-Петербург, ул.Химиков, 10 к2 <br>
                                <br>
                                ИНН  7806150861
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>