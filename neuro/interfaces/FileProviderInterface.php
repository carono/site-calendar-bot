<?php

namespace app\neuro\interfaces;

interface FileProviderInterface
{
    public function responseToFilePath($response);
}