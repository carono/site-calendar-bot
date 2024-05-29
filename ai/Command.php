<?php

namespace app\ai;

abstract class Command
{
    public string $title;
    public string $determine;
    public string $prompt;
}