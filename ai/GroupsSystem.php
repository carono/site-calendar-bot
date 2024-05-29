<?php

namespace app\ai;

class GroupsSystem extends System
{

    public function getMessage()
    {
        if (!$this->data) {
            return false;
        }

        return [
            "role" => "system",
            "content" => 'Доступные группы для распределения задач: ' . implode("\r\n", $this->data)
        ];
    }
}