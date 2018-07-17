<?php


namespace App\Http\Entity;


use App\Model\Chain;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Hash;

class ChainEntity
{
    public $name;
    public $startDate;
    public $endDate;
    public $note;
    public $id;
    public $default;

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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

    public function save()
    {
        $chain = new Chain();
        $chain->user_id = auth()->user()['id'];
        $chain->start_date = $this->startDate;
        $chain->end_date = $this->endDate;
        $chain->note = $this->note;
        $chain->name = $this->name;

        if ($chain->save()) {
            return $this;
        }

        return false;
    }
}