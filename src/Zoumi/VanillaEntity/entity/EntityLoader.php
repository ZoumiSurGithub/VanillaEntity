<?php

namespace Zoumi\VanillaEntity\entity;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use Zoumi\VanillaEntity\entity\passif\animal\Cow;
use Zoumi\VanillaEntity\entity\passif\animal\Pig;
use Zoumi\VanillaEntity\entity\passif\animal\Sheep;
use Zoumi\VanillaEntity\entity\passif\animal\Turtle;
use Zoumi\VanillaEntity\VanillaEntity;

class EntityLoader
{

    /**
     * @return void
     */
    public static function onInit(): void
    {
        $factory = EntityFactory::getInstance();
        $factory->register(Cow::class, function (World $world, CompoundTag $nbt): Cow {
            return new Cow(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Cow', 'minecraft:cow']);
        $factory->register(Turtle::class, function (World $world, CompoundTag $nbt): Turtle {
            return new Turtle(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Turtle', 'minecraft:turtle']);
        $factory->register(Sheep::class, function (World $world, CompoundTag $nbt): Sheep {
            return new Sheep(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Sheep', 'minecraft:sheep']);
        $factory->register(Pig::class, function (World $world, CompoundTag $nbt): Pig {
            return new Pig(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Pig', 'minecraft:pig']);
    }

}