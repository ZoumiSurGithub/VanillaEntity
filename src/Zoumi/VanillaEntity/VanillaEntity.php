<?php

namespace Zoumi\VanillaEntity;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Zoumi\VanillaEntity\blocks\BlockLoader;
use Zoumi\VanillaEntity\entity\EntityLoader;
use Zoumi\VanillaEntity\items\ItemLoader;
use Zoumi\VanillaEntity\listeners\ListenerLoader;

class VanillaEntity extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        EntityLoader::onInit();
        ListenerLoader::onInit();
        ItemLoader::onInit();
    }

}