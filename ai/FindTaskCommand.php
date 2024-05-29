<?php

namespace app\ai;

class FindTaskCommand extends Command
{
    public string $title = 'Найди задачу из списка активных по сообщению';
    public string $determine = 'Если в сообщении есть такие фразы или вопросы "найди, поищи задачу" найди ID задачи из списка активных задач';
    public string $prompt = 'Только если сообщение начинается с system:find-task, найди ID задачи из списка активных задач по тексту сообщения';
}