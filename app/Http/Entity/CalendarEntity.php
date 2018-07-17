<?php


namespace App\Http\Entity;

use App\Http\Manager\ChainManager;
use App\Model\Calendar;
use App\Model\Chain;

class CalendarEntity
{
    public $flag;
    public $note;
    public $chain;

    /**
     * @return mixed
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param mixed $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return ChainEntity
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * @param $chainId
     * @throws \Exception
     */
    public function setChain($chainId)
    {
        $chainManager = new ChainManager();
        foreach ($chainManager->getAllChain() as $chain) {
            if ($chain->id == $chainId) {
                $this->chain = $chainManager->map($chain);
            }
        }
    }

    public function save()
    {
        if (empty($this->chain))
        {
            Throw new \Exception("Chain not found");
        }

        $calendar = new Calendar();
        $calendar->chain_id = $this->chain->getId();
        $calendar->flag = $this->flag;
        $calendar->note = $this->note;

        if ($calendar->save()) {
            return $this;
        }

        return false;
    }
}