<?php

namespace app\telegram\crypto_signal\determine;

use app\helpers\AIHelper;
use app\telegram\crypto_signal\components\Determine;

class OrderRequest extends Determine
{
    public function getSystem(): array
    {
        return [
            [
                "role" => "system",
                "content" => 'Ты утилита, которая обрабатывает текстовые запросы пользователя в формат для сервиса по торговле на крипто бирже'
            ],
        ];
    }

    function check($message): bool
    {
        $system = array_merge($this->getSystem(), [
            [
                "role" => "system",
                "content" => 'Пиши только 1, если ответ положительный, или 0, если ответ отрицательный'
            ],
            [
                'role' => 'system',
                'content' => 'Является ли следующее сообщение, просьбой пользователя совершить сделку на бирже'
            ],
            [
                'role' => 'system',
                'content' => 'Является ли следующее сообщение, просьбой пользователя совершить сделку на бирже'
            ]
        ]);

        $response = AIHelper::start()->ask($message, $system);
        $result = $response->choices[0]->message->content;

        return is_numeric($result) && (bool)$result;
    }

    function process($message)
    {
        $system = array_merge($this->getSystem(), [
            [
                "role" => "system",
                "content" => 'Отвечай только в формате JSON, любые числовые значения, цены и т.д. пиши только цифрами, десятичное разделение через точку. Диапазон цен записывай как массив цифр'
            ],
            [
                'role' => 'system',
                'content' => 'Формат ответа: type: покупка или продажа (LONG или SHORT); token:монета; buy:цена или диапазон цены покупки; target: это массив числовых целей, stop: стоп-лосс или цена продажи'
            ]
        ]);
        $response = AIHelper::start()->ask($message, $system);
        $content = $response->choices[0]->message->content;
        return json_decode($content, true);
    }
}