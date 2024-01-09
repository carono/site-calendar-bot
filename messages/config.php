<?php

return [
    'sourcePath' => __DIR__ . DIRECTORY_SEPARATOR . '..',
    'languages' => ['ru'],
    'translator' => 'Yii::t',
    'sort' => false,
    'removeUnused' => false,
    'markUnused' => false,
    'only' => ['*.php'],
    'except' => [
        '.svn',
        '.git',
        '/vendor',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/web',
        '/migrations'
    ],
    'format' => 'php',
    'messagePath' => __DIR__,
    'overwrite' => true,
    /*
    // Message categories to ignore
    'ignoreCategories' => [
        'yii',
    ],
     */

    /*
    // 'db' output format is for saving messages to database.
    'format' => 'db',
    // Connection component to use. Optional.
    'db' => 'db',
    // Custom source message table. Optional.
    // 'sourceMessageTable' => '{{%source_message}}',
    // Custom name for translation message table. Optional.
    // 'messageTable' => '{{%message}}',
    */

    /*
    // 'po' output format is for saving messages to gettext po files.
    'format' => 'po',
    // Root directory containing message translations.
    'messagePath' => __DIR__ . DIRECTORY_SEPARATOR . 'messages',
    // Name of the file that will be used for translations.
    'catalog' => 'messages',
    // boolean, whether the message file should be overwritten with the merged messages
    'overwrite' => true,
    */
];
