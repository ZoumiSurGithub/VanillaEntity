<?php

namespace Zoumi\VanillaEntity\entity\type\trait;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;

trait UtilsTrait
{

    /**
     * @param string $name
     * @return void
     */
    public function broadcastViewersSound(string $name): void
    {
        $pos = $this->getPosition();
        $pk = PlaySoundPacket::create($name, $pos->getX(), $pos->getY(), $pos->getZ(), 0.5, 1);
        $this->getWorld()->broadcastPacketToViewers($pos, $pk);
    }

}