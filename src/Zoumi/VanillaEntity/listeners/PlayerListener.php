<?php

namespace Zoumi\VanillaEntity\listeners;

use pocketmine\block\Wheat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use Zoumi\VanillaEntity\entity\passif\animal\Cow;

class PlayerListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getTypeId() === VanillaItems::WHEAT()->getTypeId()) {
        }
    }

}