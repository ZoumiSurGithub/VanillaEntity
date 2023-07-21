<?php

namespace Zoumi\VanillaEntity\entity\type\trait;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\Server;
use Ramsey\Uuid\Uuid;

trait RidingTrait
{

    private array $riders = [];
    private array $passengers = [];
    private ?Item $itemRidingFollow = null;
    private bool $needsSaddle;

    /**
     * @return bool
     */
    public function isNeedsSaddle(): bool
    {
        return $this->needsSaddle;
    }

    /**
     * @param bool $needsSaddle
     */
    public function setNeedsSaddle(bool $needsSaddle): void
    {
        $this->needsSaddle = $needsSaddle;
    }

    /**
     * @return Item|null
     */
    public function getItemRidingFollow(): ?Item
    {
        return $this->itemRidingFollow;
    }

    /**
     * @param Item|null $itemRidingFollow
     */
    public function setItemRidingFollow(?Item $itemRidingFollow): void
    {
        $this->itemRidingFollow = $itemRidingFollow;
    }

    public function hasOwner(): bool
    {
        return (bool)$this->getOwningEntity();
    }

    public function hasPassengers(): bool
    {
        return count($this->passengers) > 0;
    }

    public function hasRiders(): bool
    {
        return count($this->riders) > 0;
    }

    public function isEmpty(): bool
    {
        return count($this->passengers) < 1;
    }

    public function getRidingPositions(): array
    {
        return [];
    }

    public function setPassenger(Entity $entity, int $index): bool
    {
        $pos = $this->getRidingPositions();
        if (!isset($pos[$index])) {
            return false;
        }

        if (isset($this->passengers[$index])) {
            $this->removePassenger($this->passengers[$index]);
        }

        $this->passengers[$index] = $entity;
        $this->riders[$entity->getId()] = $this;

        $networkProperties = $entity->getNetworkProperties();
        $networkProperties->setGenericFlag(EntityMetadataFlags::RIDING, true);
        $networkProperties->setGenericFlag(EntityMetadataFlags::SITTING, true);
        $networkProperties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, $pos[$index]);
        $this->broadcastLink($entity, $index === 0 ? EntityLink::TYPE_RIDER : EntityLink::TYPE_PASSENGER);
        return true;
    }

    public function addPassenger(Entity $entity): bool
    {
        $index = null;
        $pos = $this->getRidingPositions();
        for ($i = 0, $len = count($pos); $i < $len; ++$i) {
            if (!isset($this->passengers[$i])) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return false;
        }

        $this->setPassenger($entity, $index);
        return true;
    }

    public function removePassenger(Entity $entity): bool
    {
        $index = array_search($entity, $this->passengers, true);
        if ($index === false) {
            return false;
        }

        unset($this->passengers[$index]);
        unset($this->riders[$entity->getId()]);
        $this->passengers = array_values($this->passengers);

        $networkProperties = $entity->getNetworkProperties();
        $networkProperties->setGenericFlag(EntityMetadataFlags::RIDING, false);
        $networkProperties->setGenericFlag(EntityMetadataFlags::SITTING, false);

        $this->broadcastLink($entity, EntityLink::TYPE_REMOVE);
        return true;
    }

    protected function broadcastLink(Entity $player, int $type): void
    {
        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($this->getId(), $player->getId(), $type, true, true);
        NetworkBroadcastUtils::broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$pk]);
    }

}