<?php

namespace Zoumi\VanillaEntity\entity\passif\animal;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Zoumi\VanillaEntity\entity\type\PassiveEntity;

class Turtle extends PassiveEntity
{

    public function __construct(Location $location, ?CompoundTag $nbt = null, bool $is_baby = false)
    {
        parent::__construct($location, $nbt, $is_baby);
        $this->setSpeed(0.5);
        $this->setDistracted(false); //NOT IMPLEMENTED YET
        $this->setMaxHealth(30);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.4, 1.2);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::TURTLE;
    }

    public function getName(): string
    {
        return "Turtle";
    }

    public function getXpDropBaby(): int
    {
        return mt_rand(1, 7);
    }

    public function getDrops(): array
    {
        return []; // not yet implemented by pmmp
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }

    public function canBeMounted(): bool
    {
        return false;
    }

}