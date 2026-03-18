<?php

/*
 *
 *      █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
 *     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
 *     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
 *     ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
 *     ██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
 *     ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
 *
 *                  The divine secure, lightweight PHP framework
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://aetherphp.net
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
$root = realpath(__DIR__ . '/..');

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$header = <<<'HEADER'
/*
 *
 *      █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
 *     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
 *     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
 *     ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
 *     ██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
 *     ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
 *
 *                  The divine secure, lightweight PHP framework
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://aetherphp.net
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/

HEADER;

/** @var SplFileInfo $fileInfo */
foreach ($iterator as $fileInfo) {
    if ($fileInfo->getExtension() !== 'php') {
        continue;
    }

    $path = $fileInfo->getRealPath();

    // Skip some directories
    $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
    if (preg_match('#^(vendor|storage|node_modules|.git|.cursor)(/|\\\\)#', $relative)) {
        continue;
    }

    $code = file_get_contents($path);
    if ($code === false) {
        continue;
    }

    // Ensure starts with PHP tag
    if (strpos($code, '<?php') !== 0) {
        continue;
    }

    $rest = substr($code, strlen('<?php'));

    // Preserve leading whitespace after <?php
    $restLtrim = ltrim($rest);
    $leadingWs = substr($rest, 0, strlen($rest) - strlen($restLtrim));
    $rest = $restLtrim;

    // Optionally preserve declare(strict_types=1); if it's the first statement
    $declare = '';
    if (preg_match('/^(declare\s*\(strict_types\s*=\s*1\);\s*)/i', $rest, $m)) {
        $declare = $m[1];
        $rest = substr($rest, strlen($declare));
    }

    // If there is an existing block comment immediately after, strip it
    $restTrim = ltrim($rest);
    $wsAfterDeclare = substr($rest, 0, strlen($rest) - strlen($restTrim));
    $rest = $restTrim;

    if (strpos($rest, '/*') === 0) {
        $endPos = strpos($rest, '*/');
        if ($endPos !== false) {
            $rest = substr($rest, $endPos + 2);
        }
    }

    $newCode = '<?php' . $leadingWs . $declare . $wsAfterDeclare . $header . ltrim($rest);

    if ($newCode !== $code) {
        file_put_contents($path, $newCode);
        echo "Updated header in: {$relative}\n";
    }
}

echo "Done.\n";

