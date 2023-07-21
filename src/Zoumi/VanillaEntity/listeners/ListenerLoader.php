<?php

namespace Zoumi\VanillaEntity\listeners;

use Zoumi\VanillaEntity\VanillaEntity;

class ListenerLoader
{

    public static function onInit(): void
    {
        $plugin = VanillaEntity::getInstance();
        $plugin->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $plugin);
        $plugin->getServer()->getPluginManager()->registerEvents(new PacketListener(), $plugin);
    }

}