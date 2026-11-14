<?php

/** @var $order \common\models\Order */

$orderItems = $order->getOrderItems();

$count = count($orderItems);

foreach ($orderItems as $i => $oi) {
    /** @var $oi \common\models\OrderItem */
    $v = $oi->getProductVariant();
    $p = $v->product;

    $vUrl = $v->getUrl();
    $vTestimonialsFormUrl = $vUrl;
    $vTitle = $p->name;
    $vImgSrc = $v->getImage(1, $v::IMG_MEDIUM, true);
    $vPrice = $v->getPrices()['new']

    ?>

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnProductList mcnProductRec">
        <tbody>
        <tr>
            <td align="left" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%;">
                    <tbody>
                    <tr>
                        <td align="left" valign="top" style="font-size:0; padding-bottom:9px;">
                            <!--[if mso]>
                            <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                                <tr>
                                    <td align="left" valign="top" width="300.00000381469727" style="width:300.00000381469727px;">
                            <![endif]-->
                            <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnRecContentContainer" style="max-width:300.00000381469727px;">
                                <tbody>
                                <tr>
                                    <td align="left" valign="top" class="mcnImageContent" style="padding-top:9px; padding-right:18px; padding-left:18px;">
                                        <a href="<?= $vUrl ?>" title="<?= $vTitle ?>" target="_blank">
                                            <img alt="<?= $vTitle ?>" src="<?= $vImgSrc ?>" width="150" style="max-width:100%;" class="mcnImage">
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!--[if mso]>
                            </td>
                            <td align="left" valign="top" width="300.00000381469727" style="width:300.00000381469727px;">
                            <![endif]-->
                            <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnRecContentContainer" style="max-width:300.00000381469727px;">
                                <tbody><tr>
                                    <td align="left" valign="top" class="mcnTextContent" style="padding-top:9px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">

                                            <tbody>
                                            <tr>
                                                <td valign="top" class="mcnProductTitle" style="padding-top: 9px;padding-left: 18px;padding-right: 18px;color: #202020;font-family: Helvetica;font-size: 16px;font-style: normal;font-weight: bold;line-height: 100%;text-align: left;">
                                                    <?= $vTitle ?><br>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td valign="top" class="mcnProductPrice" style="padding-top: 9px; padding-left: 18px; padding-right: 18px; font-family: Helvetica; font-size: 14px; font-style: normal; font-weight: normal; line-height: 100%; text-align: left;">
                                                    <?= $vPrice ?> руб.<br>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td valign="top" class="mcnProductButton" style="padding-top:18px">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock" style="min-width:100%;">
                                                        <tbody class="mcnButtonBlockOuter">
                                                        <tr>
                                                            <td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="left" class="mcnButtonBlockInner">
                                                                <table border="0" cellpadding="0" cellspacing="0" class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 3px;background-color: #2BAADF;">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td align="center" valign="middle" class="mcnButtonContent" style="font-family: Helvetica; font-size: 16px; padding: 15px;">
                                                                            <a class="mcnButton " title="Оставить отзыв" href="<?= $vTestimonialsFormUrl ?>" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;">Оставить отзыв</a>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>

                                            </tbody></table>
                                    </td>
                                </tr>
                                </tbody></table>
                            <!--[if mso]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php  if ($i + 1 !== $count) { ?>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
            <tbody class="mcnDividerBlockOuter">
            <tr>
                <td class="mcnDividerBlockInner" style="min-width:100%; padding:18px;">
                    <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top: 2px solid #EAEAEA;">
                        <tbody><tr>
                            <td>
                                <span></span>
                            </td>
                        </tr>
                        </tbody></table>
                    <!--
                                    <td class="mcnDividerBlockInner" style="padding: 18px;">
                                    <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
                    -->
                </td>
            </tr>
            </tbody>
        </table>
    <?php }
}

?>


