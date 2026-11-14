<?php

namespace common\components;

use urmaul\yii2\log\slack\Target;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class SlackTarget extends Target
{
    protected function formatMessages()
    {
        $text = ($this->prefixText ? $this->prefixText . "\n" : '');
        $attachments = [];

        $currentUrl = null;
        try {
            $currentUrl = Url::to('', true);
            $text .= 'Current URL: <' . $currentUrl . '>' . "\n";
        } catch (\Exception $exc) {}

        try {
            $text .= $this->getMessagePrefix(null) . "\n";
        } catch (\Exception $exc) {}

        foreach ($this->messages as $message) {
            $attachments[] = $this->formatAttachment($message) + [
                    'color' => $this->getColor($message[1]),
                    'title_link' => $currentUrl,
                ];
        }

        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        foreach ($context as $key => $value) {

            $attachments[] =  [
                'fallback' => '',
                'title' => '',
                'text' =>
                    '```' . "\n" . "\${$key} = " . VarDumper::dumpAsString($value) . "\n" . '```',
                'mrkdwn_in' => ['text'],
            ];
        }

        return [$text, $attachments];
    }

}