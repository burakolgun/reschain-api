<?php

namespace App\Http\Controllers;

use App\Http\Manager\CalendarManager;
use App\Http\Manager\ChainManager;
use App\Model\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $calendarManager;
    protected $chainManager;

    public function __construct()
    {
        $this->calendarManager = new CalendarManager();
        $this->chainManager = new ChainManager();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        return response()->json($this->chainManager->getAllChain());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $calendar = $this->calendarManager->mapExternal($request);
        $calendar->save();
        return response()->json($calendar);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show($id)
    {
        $calendar = Calendar::find($id);

        $chainId = $calendar->chain_id;

        $this->chainManager->getChainById($chainId);

        if (empty($calendar)) {
            return response()->json("Chain not found");
        }

        $calendar = $this->calendarManager->map($calendar);

        return response()->json($calendar);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $dbCalendar = Calendar::find($id);
        $this->chainManager->getChainById($dbCalendar->id);

        $dbCalendar->flag = $request->get('flag');
        $dbCalendar->note = $request->get('note');

        if ($dbCalendar->save()) {
            return response()->json($this->calendarManager->map($dbCalendar));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json('destroy');
    }
}
