<?php

namespace Zoumi\VanillaEntity\items\utils;

use pocketmine\item\FishingRod;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class CarrotOnAStick extends FishingRod
{


    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), "Carrot on a stick");
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }

}