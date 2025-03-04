<?php

namespace App\Game\Messages\Values;

class MapChatColor {

    CONST SURFACE   = '#ffffff';
    CONST LABYRINTH = '#ffad47';
    const DUNGEONS  = '#ccb9a5';
    const SHP       = '#ababab';
    const HELL      = '#ff7d8e';
    const PURGATORY = '#639cff';

    /**
     * MapChatColor constructor.
     *
     * @param string $mapName
     */
    public function __construct(string $mapName) {
        $this->mapName = $mapName;
    }


    /**
     * Gets the chat color.
     *
     * @return string
     */
    public function getColor(): string {
        switch($this->mapName) {
            case 'Labyrinth':
                return self::LABYRINTH;
            case 'Dungeons':
                return self::DUNGEONS;
            case 'Shadow Plane':
                return self::SHP;
            case 'Hell':
                return self::HELL;
            case 'Purgatory':
                return self::PURGATORY;
            case 'Surface':
            default:
                return self::SURFACE;
        }
    }
}
