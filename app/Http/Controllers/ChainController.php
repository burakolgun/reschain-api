<?php

namespace App\Http\Controllers;

use App\Http\Manager\ChainManager;
use App\Model\Chain;
use App\User;
use http\Exception\UnexpectedValueException;
use Illuminate\Http\Request;

class ChainController extends Controller
{
    protected $chainManager;
    protected $user;

    public function __construct()
    {
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
        $chain = $this->chainManager->mapExternal($request);

        if (!$chain->save()) {
            throw new UnexpectedValueException('generalMessages.we-cant-save');
        }
        return response()
            ->json(Chain::all()
            ->where('user_id', auth()
            ->user()['id']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->user = User::find(auth()->user()['id']);
        $chain = $this->user->chain;

        if (empty($chain)) {
            throw new UnexpectedValueException(__('generalMessages.chain-not-found'));
        }

        foreach ($chain as $ch) {
            if ($ch->id == $id) {
                $ch = $this->chainManager->map($ch);
                return response()->json($ch);
            }
        }
        return response()->json(__('generalMessages.chain-not-found'));

    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function updateChain(Request $request, $id)
    {
        $dbChain = $this->chainManager->getChainById($id);
        $dbChain->start_date = $request->get('startDate');
        $dbChain->end_date = $request->get('endDate');
        $dbChain->note = $request->get('note');
        $dbChain->name = $request->get('name');

        if ($dbChain->save()) {
            return response()->json($this->chainManager->getAllChain());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $chain = $this->chainManager->getChainById($id);

        foreach ($chain->calendar as $day) {
            $day->delete();
        }

        $chain->delete();

        return response()->json($this->chainManager->getAllChain());
    }

    public function doDefault($chainId)
    {
        $oldDefaultChain = Chain::where('default', true)->first();
        $newDefault = Chain::find($chainId);

        if(empty($newDefault)) {
            return response()->json(false);
        }

        if (!empty($oldDefaultChain)) {
            $oldDefaultChain->default = null;
            $oldDefaultChain->save();
        }

        $newDefault->default = true;

        if ($newDefault->save()) {
            return response()->json(true);
        }

        return response()->json(false);

    }
}
