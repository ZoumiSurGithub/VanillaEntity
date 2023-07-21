<?php

namespace Zoumi\VanillaEntity\entity\passif\animal;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Zoumi\VanillaEntity\entity\type\PassiveEntity;
use Zoumi\VanillaEntity\items\ExtraVanillaItems;

class Pig extends PassiveEntity
{

    public function __construct(Location $location, ?CompoundTag $nbt = null, bool $is_baby = false)
    {
        parent::__construct($location, $nbt, $is_baby);
        $this->setDistracted(true, VanillaItems::CARROT());
        $this->setNeedsSaddle(true);
        $this->setItemRidingFollow(ExtraVanillaItems::CARROT_ON_A_STICK());
        $this->setSpeed(2);
        $this->setMaxHealth(10);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.9, 0.9);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::PIG;
    }

    public function getName(): string
    {
        return "Pig";
    }

    public function getXpDropBaby(): int
    {
        return mt_rand(1, 7);
    }

    public function canBeMounted(): bool
    {
        return true;
    }

    public function getRidingPositions(): array
    {
        return [new Vector3(0, 1.8, 0), new Vector3(0, 1.8, 0)];
    }

    public function getDrops(): array
    {
        if ($this->isOnFire()){
            return [VanillaItems::COOKED_PORKCHOP()->setCount(mt_rand(1, 3))];
        }else{
            return [VanillaItems::RAW_PORKCHOP()->setCount(mt_rand(1, 3))];
        }
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}