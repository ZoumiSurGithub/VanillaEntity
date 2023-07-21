<?php

namespace Zoumi\VanillaEntity\items;

use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\item\Item;
use pocketmine\item\SpawnEgg;
use pocketmine\utils\CloningRegistryTrait;
use Zoumi\VanillaEntity\items\egg\CowEgg;
use Zoumi\VanillaEntity\items\egg\PigEgg;
use Zoumi\VanillaEntity\items\egg\SheepEgg;
use Zoumi\VanillaEntity\items\egg\TurtleEgg;
use Zoumi\VanillaEntity\items\utils\CarrotOnAStick;
use Zoumi\VanillaEntity\items\utils\Saddle;

/**
 * @method static SpawnEgg COW_SPAWN_EGG()
 * @method static SpawnEgg TURTLE_SPAWN_EGG()
 * @method static SpawnEgg SHEEP_SPAWN_EGG()
 * @method static SpawnEgg PIG_SPAWN_EGG()
 * @method static Item SADDLE()
 * @method static Item CARROT_ON_A_STICK()
 */
final class ExtraVanillaItems
{
    use CloningRegistryTrait;

    private function __construct()
    {
        //NOOP
    }

    protected static function register(string $name, Item $item): void
    {
        self::_registryRegister($name, $item);
    }

    /**
     * @return Item[]
     * @phpstan-return array<string, Item>
     */
    public static function getAll(): array
    {
        //phpstan doesn't support generic traits yet :(
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup(): void
    {
        self::register('cow_spawn_egg', new CowEgg());
        self::register('turtle_spawn_egg', new TurtleEgg());
        self::register('sheep_spawn_egg', new SheepEgg());
        self::register('pig_spawn_egg', new PigEgg());
        self::register('saddle', new Saddle());
        self::register('carrot_on_a_stick', new CarrotOnAStick());
    }

}