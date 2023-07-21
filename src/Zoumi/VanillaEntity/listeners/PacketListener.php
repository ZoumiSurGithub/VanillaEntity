<?php

namespace Zoumi\VanillaEntity\listeners;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use Zoumi\VanillaEntity\entity\type\PassiveEntity;

class PacketListener implements Listener
{

    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     * @priority HIGHEST
     */
    public function onReceive(DataPacketReceiveEvent $event): void
    {
        $pk = $event->getPacket();
        if ($pk instanceof InteractPacket && $pk->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
            $event->cancel();
            $player = $event->getOrigin()->getPlayer();
            $entity = $player->getWorld()->getEntity($pk->targetActorRuntimeId);
            if ($entity instanceof PassiveEntity && $entity->canBeMounted()) {
                $entity->removePassenger($player);
            }
        }
    }

}