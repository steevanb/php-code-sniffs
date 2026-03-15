<?php

declare(strict_types=1);

$projectPath = __DIR__ . '/../..';

return [
    'composerJsonPath' => $projectPath . '/composer.json',
    'vendorPath' => $projectPath . '/vendor/',
    'scanDirectories' => [
        $projectPath . '/config/',
        $projectPath . '/src/',
        $projectPath . '/tests/',
    ],
    'requireDev' => false,
];
