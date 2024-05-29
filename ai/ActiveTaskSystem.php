<?php

namespace app\ai;

class ActiveTaskSystem extends System
{
    public function getMessage()
    {
        if (empty($this->data)) {
            return [
                'role' => 'system',
                'content' => "Активных задач нет"
            ];
        }
        $taskPrompt = array_map(function ($title, $id) {
            return 'ID=' . $id . ' ' . trim($title);
        }, $this->data, array_keys($this->data));

        return [
            'role' => 'system',
            'content' => "Активные задачи: \r\n" . implode("\r\n", $taskPrompt)
        ];
    }
}