<?php

namespace PHPWatch\SymbolData;

class ConstantsSource extends DataSourceBase {
    const NAME = 'const';

    public static function handleGroupedConstantList(array $groupedContstList, Output $output)
    {
        foreach ($groupedContstList as $groupname => $constList) {
            static::handleConstantList($groupname, $constList, $output);
        }
    }

    private static function handleConstantList(string $groupname, array $constList, Output $output)
    {
        foreach ($constList as $name => $value) {
            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/constants/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'constant',
                    'name' => $name,
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => static::generateResources($groupname, $name),
                ];
            }

            $output->addData('constants/' . $filename, [
                'type' => 'constant',
                'name' => $name,
                'meta' => $meta,
                'value' => $value,
                'extension' => $groupname,
            ]);
        }
    }

    private static function generateResources(string $groupname, string $name): array
    {
        $urls = [
            'Core' => 'https://www.php.net/manual/reserved.constants.php',
            'curl' => 'https://www.php.net/manual/curl.constants.php',
            'date' => 'https://www.php.net/manual/class.datetimeinterface.php',
        ];

        if (! array_key_exists($groupname, $urls)) {
            return [];
        }

        $url = $urls[$groupname];
        $anchorName = 'constant.' . $name;

        if ($groupname === 'date' && substr($name, 0, 5) === 'DATE_') {
            $anchorName = 'datetimeinterface.constants.' . substr($name, 5);
        }

        if ($groupname === 'date' && substr($name, 0, 9) === 'SUNFUNCS_') {
            $url = 'https://www.php.net/manual/function.date-sunrise.php';
            $anchorName = 'refsect1-function.date-sunrise-parameters';
        }

        $anchorName = str_replace('_', '-', strtolower($anchorName));

        return [
            [
                'name' => $name . ' constant (php.net)',
                'url' => $url . '#' . $anchorName,
            ],
        ];
    }

    protected function gatherData() {
        return get_defined_constants(true);
    }
}
