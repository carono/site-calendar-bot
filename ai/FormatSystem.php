<?php

namespace app\ai;

class FormatSystem extends System
{
    protected $params = [
        'type' => 'Тип команды',
        'title' => 'текст задачи',
        'description' => 'примерное описание задачи',
        'approximate' => 'примерный срок выполнения задачи (на днях, в течение недели, потом)',
        'group' => 'группа, к которой относится задача',
        'planned_at' => 'дата и время выполнения в формате php:Y-m-d H:i:00',
    ];

    public function getMessage()
    {

        $prompts = [];
        foreach ($this->params as $key => $description) {
            $prompts[] = $key . ' = ' . $description;
        }

        return [
            "role" => "system",
            "content" => "Отвечай только в JSON формате, доступные параметры: " . implode("; ", $prompts)
        ];
    }
}