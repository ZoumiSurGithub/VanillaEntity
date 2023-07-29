<?php

namespace Zoumi\VanillaEntity\entity\type;

use pocketmine\entity\Ageable;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\world\particle\HeartParticle;
use Ramsey\Uuid\Uuid;
use Zoumi\VanillaEntity\entity\passif\animal\Cow;
use Zoumi\VanillaEntity\entity\type\trait\LoveTrait;
use Zoumi\VanillaEntity\entity\type\trait\MovableTrait;
use Zoumi\VanillaEntity\entity\type\trait\RidingTrait;
use Zoumi\VanillaEntity\entity\type\trait\SeaAnimalTrait;
use Zoumi\VanillaEntity\entity\type\trait\UtilsTrait;

abstract class PassiveEntity extends Living implements Ageable
{
    use MovableTrait;
    use LoveTrait;
    use UtilsTrait;
    use RidingTrait;
    use SeaAnimalTrait;

    public int $tick = 20; // FOR CALL whenSecondLeft()
    public bool $is_baby = false;
    public ?Entity $damager = null;
    public int $tickBeforeCalm = 20 * 5;
    private int $tickForNextMove = 20 * 5;
    public int $tickToAdult = -(20 * 60 * 20);
    private ?Player $targetMounted = null;
    private bool $hasSaddle = false;

    public function __construct(Location $location, ?CompoundTag $nbt = null, bool $is_baby = false)
    {
        parent::__construct($location, $nbt);
        $this->is_baby = $is_baby;
        if ($is_baby) {
            $this->setScale(0.5);
        }
    }

    /**
     * @return bool
     */
    public function isHasSaddle(): bool
    {
        return $this->hasSaddle;
    }

    /**
     * @param bool $hasSaddle
     */
    public function setHasSaddle(bool $hasSaddle): void
    {
        $this->hasSaddle = $hasSaddle;
    }

    protected function initEntity(CompoundTag $nbt): void
    {
        $this->setIsBaby($nbt->getByte("IsBaby", 0));
        if ($this->isBaby()) {
            $this->tickToAdult = $nbt->getInt("Age", 0);
            if ($nbt->getInt("Age", 0) < 0) {
                $this->setIsBaby(true);
            }
        }
        if ($this->canBeMounted()) {
            if (!empty($nbt->getByte("Owner", 0))) {
                $this->setOwningEntity($nbt->getByte("Owner", 0) !== 0 ? Uuid::fromBytes($nbt->getByte("Owner", 0)) : null);
            }
        }
        $this->setHasSaddle($nbt->getByte("Saddled", 0));
        parent::initEntity($nbt);
    }

    /**
     * @param EntityDamageEvent $source
     * @return void
     */
    public function attack(EntityDamageEvent $source): void
    {
        parent::attack($source);
        if ($source instanceof EntityDamageByEntityEvent) {
            $this->setAttacked(true);
            $this->setDamager($source->getDamager());
        }
    }

    /**
     * @return Entity|null
     */
    public function getDamager(): ?Entity
    {
        return $this->damager;
    }

    /**
     * @param Entity|null $damager
     */
    public function setDamager(?Entity $damager): void
    {
        $this->damager = $damager;
    }

    abstract public function getXpDropBaby(): int;

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isClosed()) {
            return false;
        }
        if ($this->isSeaAnimal()) {
            $inWater = $this->isUnderwater();
            $this->setHasGravity(!$inWater);
        }
        if ($this->isBaby()) {
            if (++$this->tickToAdult >= 0) {
                $this->setIsBaby(false);
                $this->tickToAdult = -(20 * 60 * 20);
            }
        }
        // to avoid unnecessary lags, we perform moves and functions only if players see the entity.
        if (!empty($this->getViewers())) {
            // whenSecondLeft calling
            if (--$this->tick <= 0) {
                $this->whenSecondLeft();
                $this->tick = 20;
            }
            // check whether the entity is attacked or not
            if (!$this->isAttacked()) {
                // love system
                if ($this->isInLove()) {
                    // If the entity is in love, we'll look for an entity of the same species, checking if it's an adult, etc.
                    if (empty($this->getTargetLove())) {
                        $pos = $this->getPosition();
                        foreach ($this->getWorld()->getNearbyEntities(new AxisAlignedBB($pos->getX() - 16, $pos->getY() - 16, $pos->getZ() - 16, $pos->getX() + 16, $pos->getY() + 16, $pos->getZ() + 16)) as $entity) {
                            if ($entity instanceof $this && $entity->getId() !== $this->getId()) {
                                if ($entity->isInLove() && !$entity->isBaby()) {
                                    $this->setTargetLove($entity);
                                    break;
                                }
                            }
                        }
                    } else {
                        // If you want to find your soul mate
                        $x = $this->getTargetLove()->location->x - $this->location->x;
                        $y = $this->getTargetLove()->location->y - $this->location->y;
                        $z = $this->getTargetLove()->location->z - $this->location->z;

                        $diff = abs($x) + abs($z);
                        $d = $x ** 2 + $z ** 2;

                        $this->location->yaw = -atan2($x / ($diff <= 0 ? 1 : $diff), $z / ($diff <= 0 ? 1 : $diff)) * 180 / M_PI;
                        $this->location->pitch = $y == 0 ? 0 : rad2deg(-atan2($y, sqrt($x ** 2 + $z ** 2)));

                        if ($d > 4) {
                            $this->motion->x = $this->getSpeed() * 0.15 * ($x / $diff);
                            $this->motion->z = $this->getSpeed() * 0.15 * ($z / $diff);
                            if (!$this->isSeaAnimal()) {
                                $this->moveY();
                            } else {
                                if ($this->isUnderwater()) {
                                    $this->motion->y = $this->getSpeed() * 0.15 * ($y / $diff);
                                } else {
                                    $this->moveY();
                                }
                            }
                            $this->getWorld()->onEntityMoved($this);
                        } else {
                            $targetLove = $this->getTargetLove();
                            if ($targetLove instanceof $this) {
                                $targetLove->setInLove(false);
                            }
                            $targetLove->setTargetLove(null);
                            $this->setTargetLove(null);
                            $this->setInLove(false);
                            $entity = new $this($this->getLocation(), null, true);
                            $entity->spawnToAll();
                            $entity->getWorld()->dropExperience($entity->getPosition(), $this->getXpDropBaby());
                        }
                    }
                }

                // distraction system
                if ($this->canBeDistracted()) {
                    if (empty($this->getTargetDistracted())) {
                        if (!empty($this->getItemDistracted())) {
                            // search for nearby players to check if the player in question has the distraction item | to optimize the whole, we look only at players viewing the entity
                            foreach ($this->getViewers() as $viewer) {
                                $item = $viewer->getInventory()->getItemInHand();
                                if ($item->getTypeId() === $this->getItemDistracted()->getTypeId()) {
                                    // the player owns the item that distracts the entity
                                    $this->setTargetDistracted($viewer);
                                    break;
                                }
                            }
                        }
                    } else {
                        // we'll move the entity to the player, checking that the player is still in the world where the entity is, that the player is still alive, that the distance between the player and the entity is not greater than 10, and that the player still has the distraction item in his hand.
                        $targetDistracted = $this->getTargetDistracted();
                        $item = $targetDistracted->getInventory()->getItemInHand();
                        if ($targetDistracted->isAlive() &&
                            $targetDistracted->getWorld()->getFolderName() === $this->getWorld()->getFolderName() &&
                            $this->getPosition()->distance($targetDistracted->getPosition()) <= 10 &&
                            $item->getTypeId() === $this->getItemDistracted()->getTypeId()) {
                            $x = $targetDistracted->location->x - $this->location->x;
                            $y = $targetDistracted->location->y - $this->location->y;
                            $z = $targetDistracted->location->z - $this->location->z;

                            $diff = abs($x) + abs($z);
                            $d = $x ** 2 + $z ** 2;

                            $this->location->yaw = -atan2($x / ($diff <= 0 ? 1 : $diff), $z / ($diff <= 0 ? 1 : $diff)) * 180 / M_PI;
                            $this->location->pitch = $y == 0 ? 0 : rad2deg(-atan2($y, sqrt($x ** 2 + $z ** 2)));

                            if ($d > 5) {
                                $this->motion->x = $this->getSpeed() * 0.15 * ($x / $diff);
                                $this->motion->z = $this->getSpeed() * 0.15 * ($z / $diff);
                                if (!$this->isSeaAnimal()) {
                                    $this->moveY();
                                } else {
                                    if ($this->isUnderwater()) {
                                        $this->motion->y = $this->getSpeed() * 0.15 * ($y / $diff);
                                    } else {
                                        $this->moveY();
                                    }
                                }
                                $this->getWorld()->onEntityMoved($this);
                            }
                        } else {
                            $this->setTargetDistracted(null);
                        }
                        return true;
                    }
                }
                // default AI
                if (empty($this->getDestination())) {
                    if (--$this->tickForNextMove <= 0) {
                        $this->generatePath(-7, 7);
                        $this->tickForNextMove = 20 * 5;
                    }
                } else {
                    if ($this->canContinue()) {
                        $x = intval($this->getDestination()->getX() - $this->getPosition()->getX());
                        $y = intval($this->getDestination()->getY() - $this->getPosition()->getY());
                        $z = intval($this->getDestination()->getZ() - $this->getPosition()->getZ());
                        $diff = abs($x) + abs($z);
                        $d = $x ** 2 + $z ** 2;
                        if ($d > 1) {
                            $this->motion->x = $this->getSpeed() * 0.15 * ($x / $diff);
                            $this->motion->z = $this->getSpeed() * 0.15 * ($z / $diff);
                            $yaw = atan2(-$x, $z) * 180 / M_PI;
                            $this->setRotation($yaw, 0);
                            if (!$this->isSeaAnimal()) {
                                if (!$this->moveY()) {
                                    $this->setDestination(null);
                                }
                            } else {
                                if ($this->isUnderwater()) {
                                    $this->motion->y = $this->getSpeed() * 0.15 * ($y / $diff);
                                } else {
                                    if (!$this->moveY()) {
                                        $this->setDestination(null);
                                    }
                                }
                            }
                            $this->getWorld()->onEntityMoved($this);
                        } else {
                            $this->setDestination(null);
                        }
                    }
                }
            } else {
                if (--$this->tickBeforeCalm <= 0) {
                    $this->setAttacked(false);
                    $this->setDamager(null);
                    $this->tickBeforeCalm = 20 * 5;
                }
                // if attacked, the entity will run around faster
                // check whether a target position has been generated
                if (empty($this->getDestination())) {
                    if (--$this->tickForNextMove <= 0) {
                        $this->generatePath(-16, 16);
                        $this->tickForNextMove = 20 * 2;
                    }
                } else {
                    // we check whether it's physically possible to continue the route
                    if ($this->canContinue()) {
                        $x = intval($this->getDestination()->getX() - $this->getPosition()->getX());
                        $z = intval($this->getDestination()->getZ() - $this->getPosition()->getZ());
                        $diff = abs($x) + abs($z);
                        $d = $x ** 2 + $z ** 2;
                        if ($d > 1) {
                            $this->motion->x = $this->getSpeed() * 0.15 * ($x / $diff);
                            $this->motion->z = $this->getSpeed() * 0.15 * ($z / $diff);
                            $yaw = atan2(-$x, $z) * 180 / M_PI;
                            $this->setRotation($yaw, 0);
                            if (!$this->moveY()) {
                                // If it's physically impossible to go up, then we stop moving the entity forward.
                                $this->setDestination(null);
                            }
                            $this->getWorld()->onEntityMoved($this);
                        } else {
                            $this->setDestination(null);
                        }
                    } else {
                        $this->setDestination(null);
                    }
                }
            }
        }

        return parent::entityBaseTick($tickDiff);
    }

    /**
     * @param bool $is_baby
     */
    public function setIsBaby(bool $is_baby): void
    {
        $this->is_baby = $is_baby;
        if (!$is_baby) {
            $this->setScale(1);
        } else {
            $this->setScale(0.5);
        }
    }

    public function isBaby(): bool
    {
        return $this->is_baby;
    }

    public function whenSecondLeft(): void
    {
        if ($this->isInLove()) {
            // PARTICLE LOVE RANDOM
            for ($i = 0; $i <= 4; $i++) {
                $randomOffset = new Vector3(mt_rand(-1, 1), mt_rand(0, 1), mt_rand(-1, 1));
                $this->getWorld()->addParticle($this->getPosition()->add($randomOffset->getX(), $randomOffset->getY(), $randomOffset->getZ()), new HeartParticle());
            }
        }
    }

    abstract public function canBeMounted(): bool;

    protected function syncNetworkData(EntityMetadataCollection $properties): void
    {
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::BABY, $this->isBaby());
        $properties->setGenericFlag(EntityMetadataFlags::SADDLED, $this->isHasSaddle());
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setByte("IsBaby", $this->isBaby() ? 1 : 0);
        $nbt->setInt("Age", $this->tickToAdult >= 0 ? 0 : $this->tickToAdult);
        if ($this->canBeMounted()) {
            $owner = $this->getOwningEntity();
            $nbt->setByte("Owner", $owner instanceof Player ? $owner->getUniqueId()->getBytes() : 0);
        }
        $nbt->setByte("Saddled", $this->isHasSaddle() ? 1 : 0);
        return $nbt;
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if ($this->canBeMounted()) {
            $item = $player->getInventory()->getItemInHand();
            if (!$this->isNeedsSaddle()) {
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    // IMPLEMENT ADOPTION HERE
                    $this->addPassenger($player);
                }
            } else {
                if ($this->isHasSaddle()) {
                    if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                        $this->addPassenger($player);
                    }
                }
            }
        }
        return parent::onInteract($player, $clickPos);
    }

}