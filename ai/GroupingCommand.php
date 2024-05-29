<?php

namespace app\ai;

class GroupingCommand extends Command
{
    public string $title = 'Определение группы для задачи';
    public string $determine = 'Если в сообщении есть такие фразы или вопросы как "определи группу", "куда добавить", "распредели" или подобные';
    public string $prompt = 'Только если сообщение начинается с system:grouping';
}