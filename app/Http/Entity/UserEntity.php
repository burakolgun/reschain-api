<?php


namespace App\Http\Entity;


class UserEntity
{
    public $chain;

    /**
     * @return mixed
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * @param mixed $chain
     */
    public function setChain($chain)
    {
        $this->chain = $chain;
    }
}