<?php


namespace App\Http\Manager;


use App\Http\Entity\ChainEntity;
use App\Model\Chain;
use App\User;

class ChainManager
{
    public function map(Chain $data)
    {
        $chain = new ChainEntity();
        $chain->setId($data->id);
        $chain->setName($data->name);
        $chain->setNote($data->note);
        $chain->setStartDate($data->start_date);
        $chain->setEndDate($data->end_date);
        $chain->setDefault($data->default);

        return $chain;
    }

    public function mapExternal($data)
    {
        $chain = new ChainEntity();
        //$chain->setId($data->id ?? null);
        $chain->setName($data->name);
        $chain->setNote($data->note);
        $chain->setStartDate($data->startDate);
        $chain->setEndDate($data->endDate);

        return $chain;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getChainById($id)
    {
        $user = User::find(auth()->user()['id']);
        $chain = $user->chain;

        if (empty($chain)) {
            throw new \Exception("Chain not found");
        }

        foreach ($chain as $ch) {
            if ($ch->id == $id) {
                return $ch;
            }
        }

        throw new \Exception("Chain not found");
    }

    public function getAllChain()
    {
        $user = User::find(auth()->user()['id']);
        $chainModel = $user->chain;

        $chains = [];
        foreach ($chainModel as $chain) {
            $chains[] = self::map($chain);
        }

        if (empty($chains)) {
            throw new \Exception("Chain not found");
        }

        return $chains;
    }
}