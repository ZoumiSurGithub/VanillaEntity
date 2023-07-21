<?php

namespace Zoumi\VanillaEntity\items;

use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class ItemLoader
{

    public static function onInit(): void
    {
        self::registerSimpleItem(ItemTypeNames::COW_SPAWN_EGG, ExtraVanillaItems::COW_SPAWN_EGG(), ['cow_spawn_egg']);
        self::registerSimpleItem(ItemTypeNames::TURTLE_SPAWN_EGG, ExtraVanillaItems::TURTLE_SPAWN_EGG(), ['turtle_spawn_egg']);
        self::registerSimpleItem(ItemTypeNames::SHEEP_SPAWN_EGG, ExtraVanillaItems::SHEEP_SPAWN_EGG(), ['sheep_spawn_egg']);
        self::registerSimpleItem(ItemTypeNames::PIG_SPAWN_EGG, ExtraVanillaItems::PIG_SPAWN_EGG(), ['pig_spawn_egg']);
        self::registerSimpleItem(ItemTypeNames::SADDLE, ExtraVanillaItems::SADDLE(), ['saddle']);
        self::registerSimpleItem(ItemTypeNames::CARROT_ON_A_STICK, ExtraVanillaItems::CARROT_ON_A_STICK(), ['carrot_on_a_stick']);
    }

    /**
     * @param string[] $stringToItemParserNames
     */
    private static function registerSimpleItem(string $id, Item $item, array $stringToItemParserNames): void
    {
        GlobalItemDataHandlers::getDeserializer()->map($id, fn() => clone $item);
        GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($id));

        foreach ($stringToItemParserNames as $name) {
            StringToItemParser::getInstance()->register($name, fn() => clone $item);
        }
    }

}