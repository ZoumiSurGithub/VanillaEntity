<?php

namespace Zoumi\VanillaEntity\items\egg;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\Location;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\SpawnEgg;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use Zoumi\VanillaEntity\entity\passif\animal\Cow;
use Zoumi\VanillaEntity\entity\passif\animal\Pig;

class PigEgg extends SpawnEgg
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), "Spawn Egg Pig");
    }

    protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity
    {
        return new Pig(new Location($pos->getX(), $pos->getY(), $pos->getZ(), $world, $yaw, $pitch));
    }

}