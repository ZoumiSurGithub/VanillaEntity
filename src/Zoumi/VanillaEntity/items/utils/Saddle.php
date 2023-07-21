<?php

namespace Zoumi\VanillaEntity\items\utils;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use Zoumi\VanillaEntity\entity\passif\animal\Pig;
use Zoumi\VanillaEntity\entity\type\PassiveEntity;

class Saddle extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), "Saddle");
    }

    public function onInteractEntity(Player $player, Entity $entity, Vector3 $clickVector): bool
    {
        if ($entity instanceof Pig) {
            if (!$entity->isHasSaddle()) {
                $player->getInventory()->setItemInHand(VanillaItems::AIR());
                $entity->setHasSaddle(true);
            }
        }
        return parent::onInteractEntity($player, $entity, $clickVector);
    }

}