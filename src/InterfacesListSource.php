<?php

namespace PHPWatch\SymbolData;

class InterfacesListSource extends DataSourceBase {
    const NAME = 'interface';
    protected function gatherData() {
        $interfaces = get_declared_interfaces();
        $return = [];

        foreach ($interfaces as $interface) {
            if (strpos($interface, 'Composer\\') === 0) {
                continue;
            }

            if (strpos($interface, 'PHPWatch\\') === 0) {
                continue;
            }

            $return[] = $interface;
        }

        return $return;
    }
}
