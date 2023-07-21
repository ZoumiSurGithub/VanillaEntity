<?php

namespace Zoumi\VanillaEntity\entity\type\trait;

use pocketmine\entity\Entity;

trait LoveTrait
{

    public int $tickToRemoveLove = 20 * 10;
    public int $tickToCanLoveAgain = 20 * 15;
    private bool $in_love = false;
    private bool $canLove;
    private ?Entity $targetLove = null;

    /**
     * @return Entity|null
     */
    public function getTargetLove(): ?Entity
    {
        return $this->targetLove;
    }

    /**
     * @param Entity|null $targetLove
     */
    public function setTargetLove(?Entity $targetLove): void
    {
        $this->targetLove = $targetLove;
    }

    /**
     * @param bool $canLove
     */
    public function setCanLove(bool $canLove): void
    {
        $this->canLove = $canLove;
    }

    /**
     * @return bool
     */
    public function canLove(): bool
    {
        return $this->canLove;
    }

    /**
     * @param bool $in_love
     */
    public function setInLove(bool $in_love): void
    {
        $this->in_love = $in_love;
    }

    /**
     * @return bool
     */
    public function isInLove(): bool
    {
        return $this->in_love;
    }

    /**
     * @return void
     * TODO: IMPLEMENT COUPLED HERE exemple: create baby cow
     */
    public function whenCoupled(): void
    {
    }

}