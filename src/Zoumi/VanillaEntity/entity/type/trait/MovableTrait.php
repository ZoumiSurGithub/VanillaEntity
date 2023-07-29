<?php

namespace Zoumi\VanillaEntity\entity\type\trait;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\Cobweb;
use pocketmine\block\Fire;
use pocketmine\block\Lava;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Water;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

trait MovableTrait
{

    private bool $distracted;
    private ?Item $itemDistracted = null;
    private bool $isAttacked = false;
    private int $tickToForgetAttack = 20 * 3; // 3 SECONDES
    private float $speed;
    private ?Vector3 $destination = null;
    private ?Player $targetDistracted = null;

    /**
     * @return Player|null
     */
    public function getTargetDistracted(): ?Player
    {
        return $this->targetDistracted;
    }

    /**
     * @param Player|null $targetDistracted
     */
    public function setTargetDistracted(?Player $targetDistracted): void
    {
        $this->targetDistracted = $targetDistracted;
    }

    /**
     * @return Item|null
     */
    public function getItemDistracted(): ?Item
    {
        return $this->itemDistracted;
    }

    /**
     * @return Vector3|null
     */
    public function getDestination(): ?Vector3
    {
        return $this->destination;
    }

    /**
     * @param Vector3|null $destination
     */
    public function setDestination(?Vector3 $destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @param float $speed
     */
    public function setSpeed(float $speed): void
    {
        $this->speed = $speed;
    }

    /**
     * @return float
     */
    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * @return bool
     */
    public function isAttacked(): bool
    {
        return $this->isAttacked;
    }

    /**
     * @param bool $isAttacked
     */
    public function setAttacked(bool $isAttacked): void
    {
        $this->isAttacked = $isAttacked;
    }

    /**
     * @param bool $distracted
     * @param Item|null $item
     */
    public function setDistracted(bool $distracted, ?Item $item = null): void
    {
        $this->distracted = $distracted;
        $this->itemDistracted = $item;
    }

    /**
     * @return bool
     */
    public function canBeDistracted(): bool
    {
        return $this->distracted;
    }

    /**
     * @return bool|null
     */
    public function moveY(): null|bool
    {
        $direction = $this->getDirectionVector();
        $position = new Vector3($this->location->x + $direction->x, $this->location->y, $this->location->z + $direction->z);
        $block = $this->getWorld()->getBlock($position);
        if ($block->isSolid() || $this->isCollidedHorizontally || $this->isUnderwater()) {
            $block2 = $this->getWorld()->getBlock($position->add(0, 1, 0));
            if ($block2->canBeFlowedInto()) {
                if ($block instanceof Slab || $block instanceof Stair) {
                    if ($this->location->y - round($this->location->y) === 0) {
                        return false;
                    }
                }
                $this->motion->y = 0.25;
            } else {
                $this->motion->y = 0;
            }
        } else {
            $block = $this->getWorld()->getBlock($position->add(0, -1, 0));
            if (!$block->isSolid()) {
                $this->motion->y = -$this->gravity * 4;
            } else {
                $this->motion->y = 0;
            }
        }
        return true;
    }

    public function canContinue(): bool
    {
        $direction = $this->getDirectionVector();
        $position = new Vector3($this->location->x + $direction->x, $this->location->y, $this->location->z + $direction->z);
        $blockGround = $this->getWorld()->getBlock($position->add(0, -1, 0));
        if ($blockGround instanceof Lava || $blockGround instanceof Water) {
            return false;
        }
        $block = $this->getWorld()->getBlock($position);
        if ($this->isSeaAnimal()) {
            /** VERIFICATION DU BLOC EN FACE DE L'ENTITÉ */
            if ($block->isSolid() || $this->isCollidedHorizontally && !$block instanceof Water) {
                /** VERIFICATION DE SI IL Y A UN BLOC AU DESSUS DU BLOC EN FACE */
                $block2 = $block->getPosition()->getWorld()->getBlock($block->getPosition()->add(0, 1, 0));
                if ($block2->isSolid() && !$block2 instanceof Water) {
                    return false; // IL PEUT PAS
                }
            } elseif ($block instanceof Fire || $block instanceof Cobweb) {
                return false;
            }
        } else {
            /** VERIFICATION DU BLOC EN FACE DE L'ENTITÉ */
            if ($block->isSolid() || $this->isCollidedHorizontally || $this->isUnderwater()) {
                /** VERIFICATION DE SI IL Y A UN BLOC AU DESSUS DU BLOC EN FACE */
                $block2 = $block->getPosition()->getWorld()->getBlock($block->getPosition()->add(0, 1, 0));
                if ($block2->isSolid()) {
                    return false; // IL PEUT PAS
                }
            } elseif ($block instanceof Fire || $block instanceof Cobweb) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $min
     * @param int $max
     * @return void
     */
    public function generatePath(int $min, int $max): void
    {
        $x = $this->getPosition()->getX() + mt_rand($min, $max);
        $z = $this->getPosition()->getZ() + mt_rand($min, $max);

        $this->setDestination(new Vector3($x, $this->getPosition()->getY(), $z));
    }

}