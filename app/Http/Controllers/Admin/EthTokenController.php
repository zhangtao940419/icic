<?php

namespace App\Http\Controllers\Admin;

use App\Model\CoinType;
use App\Model\EthToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EthTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ethtokens = EthToken::with('coin')->paginate();

        return view('admin.token.index', compact('ethtokens'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(EthToken $token)
    {
        $coins = CoinType::all();

        return view('admin.token.create_or_edit', compact('token', 'coins'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'token_contract_address' => 'required',
            'token_contract_abi' => 'required',
            'coin_id' => 'required|unique:eth_token_list'
        ]);

        $data = $request->except('_token');
        $data['token_symbol'] = (new CoinType)->getCoinName(['coin_id' => $request->coin_id]);

        EthToken::create($data);

        return redirect()->route('token.index')->with('success', '操作成功');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EthToken $token)
    {
        $coins = CoinType::all();

        return view('admin.token.create_or_edit', compact('token', 'coins'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EthToken $token)
    {
        $this->validate($request, [
            'token_contract_address' => 'required',
            'token_contract_abi' => 'required',
            'coin_id' => 'required'
        ]);

        $data = $request->except('_token');
        $data['token_symbol'] = (new CoinType)->getCoinName(['coin_id' => $request->coin_id]);

        $token->update($data);


        return redirect()->route('token.index')->with('success', '操作成功');
    }

}
