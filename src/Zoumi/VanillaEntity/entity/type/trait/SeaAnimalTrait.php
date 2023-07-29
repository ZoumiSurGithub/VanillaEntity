<?php

namespace Zoumi\VanillaEntity\entity\type\trait;

trait SeaAnimalTrait
{

    private bool $sea_animal = false;

    /**
     * @return bool
     */
    public function isSeaAnimal(): bool
    {
        return $this->sea_animal;
    }

    /**
     * @param bool $sea_animal
     */
    public function setSeaAnimal(bool $sea_animal): void
    {
        $this->sea_animal = $sea_animal;
    }

}