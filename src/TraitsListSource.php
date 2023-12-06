<?php

namespace PHPWatch\SymbolData;

use ReflectionClass;

class TraitsListSource extends DataSourceBase implements DataSource {
    const NAME = 'trait';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function addDataToOutput(Output $output): void
    {
        static::handleTraitList($this->data, $output);
    }

    private static function handleTraitList(array $traitList, Output $output)
    {
        $output->addData('trait', $traitList);

        foreach ($traitList as $name) {
            $reflection = new ReflectionClass($name);

            // Handle namespaces
            $filename = str_replace('\\', '/', $name);
            $metafile = realpath(__DIR__ . '/../meta/traits/' . $filename . '.php');

            // maybe embed custom meta data
            if ($metafile !== false && file_exists($metafile)) {
                $meta = include($metafile);
            } else {
                // embed generic meta data
                $meta = [
                    'type' => 'trait',
                    'name' => $reflection->getName(),
                    'description' => '',
                    'keywords' => [],
                    'added' => '0.0',
                    'deprecated' => null,
                    'removed' => null,
                    'resources' => [],
                ];
            }

            $output->addData('traits/' . $filename, [
                'type' => 'trait',
                'name' => $reflection->getName(),
                'meta' => $meta,
                'interfaces' => $reflection->getInterfaceNames(),
                'constants' => $reflection->getConstants(),
                'properties' => static::generateDetailsAboutProperties($reflection),
                'methods' => static::generateDetailsAboutMethods($reflection),
                'traits' => $reflection->getTraitNames(),
                'is_abstract' => $reflection->isAbstract(),
                'is_anonymous' => $reflection->isAnonymous(),
                'is_cloneable' => $reflection->isCloneable(),
                'is_final' => $reflection->isFinal(),
                'is_read_only' => (method_exists($reflection, 'isReadOnly')) ? $reflection->isReadOnly() : false,
            ]);
        }
    }
}
