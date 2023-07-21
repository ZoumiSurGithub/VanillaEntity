<?php

namespace Zoumi\VanillaEntity\blocks;

use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\item\StringToItemParser;
use pocketmine\world\format\io\GlobalBlockStateHandlers;

class BlockLoader {

    public static function onInit(): void{
        self::registerSimpleBlock(BlockTypeNames::SCULK_CATALYST, ExtraVanillaBlocks::SCULK_CATALYST(), ["sculk_catalyst"]);
    }

    /**
     * @param string[] $stringToItemParserNames
     */
    private static function registerSimpleBlock(string $id, Block $block, array $stringToItemParserNames) : void{
        RuntimeBlockStateRegistry::getInstance()->register($block);

        GlobalBlockStateHandlers::getDeserializer()->mapSimple($id, fn() => clone $block);
        GlobalBlockStateHandlers::getSerializer()->mapSimple($block, $id);

        foreach($stringToItemParserNames as $name){
            StringToItemParser::getInstance()->registerBlock($name, fn() => clone $block);
        }
    }

}