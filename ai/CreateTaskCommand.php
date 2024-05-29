<?php

namespace app\ai;

class CreateTaskCommand extends Command
{
    public string $title = 'Если точно определить команду не удается, и в списке активных задач такой нет, то это создание новой задачи';
    public string $determine = 'Если в сообщении есть такие фразы как "добавь, запиши или добавь задачу"';
    public string $prompt = 'Нужно сформировать задачу по тексту пользователя. По сообщению пользователя, ты должен определить название, описание и время выполнения задачи';
}