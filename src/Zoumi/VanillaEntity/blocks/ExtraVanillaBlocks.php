<?php

namespace Zoumi\VanillaEntity\blocks;

use pocketmine\block\Block;
use pocketmine\block\Opaque;
use pocketmine\utils\CloningRegistryTrait;
use Zoumi\VanillaEntity\blocks\sculk\SculkCatalyst;

/**
 * @method static Opaque SCULK_CATALYST()
 */
final class ExtraVanillaBlocks
{
    use CloningRegistryTrait;

    private function __construct()
    {
        //NOOP
    }

    protected static function register(string $name, Block $block): void
    {
        self::_registryRegister($name, $block);
    }

    /**
     * @return Block[]
     * @phpstan-return array<string, Block>
     */
    public static function getAll(): array
    {
        //phpstan doesn't support generic traits yet :(
        /** @var Block[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup(): void
    {
        self::register("sculk_catalyst", new SculkCatalyst());
    }
}