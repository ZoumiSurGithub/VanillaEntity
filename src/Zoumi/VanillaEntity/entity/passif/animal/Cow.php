<?php

namespace Zoumi\VanillaEntity\entity\passif\animal;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use Zoumi\VanillaEntity\entity\type\PassiveEntity;

class Cow extends PassiveEntity
{
    public function __construct(Location $location, ?CompoundTag $nbt = null, bool $baby = false)
    {
        parent::__construct($location, $nbt, $baby);
        // DEFAULT SETTINGS
        $this->setSpeed(1.5);
        $this->setDistracted(true, VanillaItems::WHEAT());
        $this->setMaxHealth(10);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.4, 0.9);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::COW;
    }

    public function getName(): string
    {
        return "Cow";
    }

    public function getDrops(): array
    {
        $array = [];
        $array[] = VanillaItems::LEATHER()->setCount(mt_rand(0, 2));
        if ($this->isOnFire()) {
            $array[] = VanillaItems::STEAK()->setCount(mt_rand(1, 3));
        } else {
            $array[] = VanillaItems::RAW_BEEF()->setCount(mt_rand(1, 3));
        }
        return $array;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        $item = $player->getInventory()->getItemInHand();
        if ($item->getTypeId() === VanillaItems::BUCKET()->getTypeId()) {
            $this->broadcastViewersSound("mob.cow.milk");
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->getInventory()->addItem(VanillaItems::MILK_BUCKET());
            return true;
        } elseif ($item->getTypeId() === VanillaItems::WHEAT()->getTypeId()) {
            if (!$this->isBaby()) {
                if (!$this->isInLove()) {
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand($item);
                    $this->setInLove(true);
                    return true;
                }
            } else {
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                $this->tickToAdult = $this->tickToAdult - ($this->tickToAdult * 0.1);
            }
        }
        return parent::onInteract($player, $clickPos);
    }

    public function getXpDropBaby(): int
    {
        return mt_rand(1, 7);
    }

    public function canBeMounted(): bool
    {
        return false;
    }
}