<?php declare(strict_types=1);

// Usage:
//   php rewrite-namespace.php NEW_NAMESPACE [OLD_NAMESPACE]
//
// The script iterates over src/ and tests/ directories and rewrites the
// namespace declarations by replacing the OLD namespace root with the NEW one.
// If OLD_NAMESPACE is omitted, it will be auto-detected from the first
// namespace declaration found in the src/ directory.

(function (): int {
    $argv = $_SERVER['argv'] ?? [];
    $argc = $_SERVER['argc'] ?? 0;

    $script = $argv[0] ?? 'rewrite-namespace.php';

    $printUsage = function () use ($script): void {
        fwrite(STDERR, sprintf("Usage: php %s NEW_NAMESPACE [OLD_NAMESPACE]\n", $script));
        fwrite(STDERR, "Examples:\n");
        fwrite(STDERR, sprintf("  php %s Acme\\\\Project\n", $script));
        fwrite(STDERR, sprintf("  php %s Acme\\\\Project spriebsch\\\\eventSourcing\\\\bankAccount\n", $script));
    };

    if ($argc < 2 || $argc > 3) {
        $printUsage();
        return 1;
    }

    $newRoot = trim((string) $argv[1]);
    if ($newRoot === '') {
        fwrite(STDERR, "Error: NEW_NAMESPACE must not be empty.\n");
        $printUsage();
        return 1;
    }

    $paths = [__DIR__ . '/src', __DIR__ . '/tests'];

    $findFirstNamespaceIn = function (string $directory): ?string {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }
            $contents = @file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            if (preg_match('/^\s*namespace\s+([^;]+);/m', $contents, $m) === 1) {
                return trim($m[1]);
            }
        }
        return null;
    };

    $oldRoot = $argv[2] ?? null;
    if ($oldRoot === null) {
        // autodetect from src directory first
        $oldRoot = $findFirstNamespaceIn(__DIR__ . '/src');
        if ($oldRoot === null) {
            fwrite(STDERR, "Error: Could not auto-detect existing namespace in src/. Provide OLD_NAMESPACE explicitly.\n");
            return 1;
        }
    }

    $oldRoot = trim((string) $oldRoot);
    if ($oldRoot === '') {
        fwrite(STDERR, "Error: OLD_NAMESPACE must not be empty.\n");
        return 1;
    }

    // Normalize backslashes (avoid leading/trailing backslashes inconsistencies)
    $normalizeNs = function (string $ns): string {
        $ns = trim($ns);
        $ns = ltrim($ns, '\\');
        $ns = rtrim($ns, '\\');
        return $ns;
    };

    $oldRoot = $normalizeNs($oldRoot);
    $newRoot = $normalizeNs($newRoot);

    $changedFiles = 0;
    $scannedFiles = 0;

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }
            $scannedFiles++;
            $filename = $file->getPathname();
            $contents = file_get_contents($filename);
            if ($contents === false) {
                fwrite(STDERR, sprintf("Warning: Could not read %s\n", $filename));
                continue;
            }

            if (preg_match('/^\s*namespace\s+([^;]+);/m', $contents, $m) !== 1) {
                // No namespace declaration, skip
                continue;
            }

            $currentNs = $normalizeNs($m[1]);

            // Only process if current namespace matches or is within old root
            $matchesOld = $currentNs === $oldRoot || str_starts_with($currentNs, $oldRoot . '\\');
            if (!$matchesOld) {
                continue;
            }

            $suffix = substr($currentNs, strlen($oldRoot));
            if ($suffix !== '' && $suffix[0] !== '\\') {
                // Ensure suffix starts with a backslash if present
                $suffix = '\\' . $suffix;
            }
            $newNs = $newRoot . $suffix;

            if ($newNs === $currentNs) {
                continue; // nothing to change
            }

            $newContents = preg_replace('/^(\s*namespace\s+)([^;]+);/m', sprintf('$1%s;', $newNs), $contents, 1);
            if ($newContents === null) {
                fwrite(STDERR, sprintf("Warning: Regex replace failed for %s\n", $filename));
                continue;
            }

            if ($newContents !== $contents) {
                $ok = file_put_contents($filename, $newContents);
                if ($ok === false) {
                    fwrite(STDERR, sprintf("Error: Could not write %s\n", $filename));
                    continue;
                }
                $changedFiles++;
                fwrite(STDOUT, sprintf("Rewrote namespace in %s: %s -> %s\n", $filename, $currentNs, $newNs));
            }
        }
    }

    fwrite(STDOUT, sprintf("Scanned %d PHP files. Changed %d file(s).\n", $scannedFiles, $changedFiles));

    return 0;
})();
