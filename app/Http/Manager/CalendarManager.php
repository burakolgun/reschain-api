<?php


namespace App\Http\Manager;


use App\Http\Entity\CalendarEntity;
use App\Model\Calendar;
use App\User;

class CalendarManager
{
    public function map(Calendar $data)
    {
        $calendar = new CalendarEntity();
        $calendar->setFlag($data->flag);
        $calendar->setNote($data->note);
        $calendar->setChain($data->chainId);

        return $calendar;
    }

    public function mapExternal($data)
    {
        $calendar = new CalendarEntity();
        $calendar->setFlag($data->flag);
        $calendar->setNote($data->note);
        $calendar->setChain($data->chainId);

        return $calendar;
    }
}