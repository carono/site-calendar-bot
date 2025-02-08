<?php

namespace app\telegram\crypto_signal\determine;

use app\helpers\AIHelper;
use app\telegram\crypto_signal\components\Determine;

class SignalDetermine extends Determine
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

    public function check($message): bool
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

    public function process($message)
    {
        $content1 = <<<HTML
 Формат ответа: 
 Все числа это дробные значения, если в тексте указано число без десятичных значений, добавь точку;
 
 
 type: текст, покупка или продажа (LONG или SHORT); 
 token: текст, монета;
 buy: число, массив цены покупки; 
 target: массив числовых целей;
 stop: число, стоп-лосс или цена продажи, если стоп не установлен указывай NULL;
 
 Если не сможешь определить данные, указывать NULL
 HTML;

        $system = array_merge($this->getSystem(), [
            [
                "role" => "system",
                "content" => 'Отвечай только в формате JSON, любые числовые значения, цены и т.д. пиши только цифрами, десятичное разделение через точку. Диапазон цен записывай как массив цифр'
            ],
            [
                'role' => 'system',
                'content' => $content1
            ]
        ]);
        $response = AIHelper::start()->ask($message, $system);
        $content = $response->choices[0]->message->content;
        return json_decode($content, true);
    }
}