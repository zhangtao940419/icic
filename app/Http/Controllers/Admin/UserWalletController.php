<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Model\Admin\adminUser;
use App\Model\Admin\AdminWalletFlow;
use App\Model\CenterWallet;
use App\Model\CenterWalletDetail;
use App\Model\CoinType;
use App\Model\OrePoolTransferRecord;
use App\Model\User;
use App\Model\WalletDetail;
use App\Model\WalletFlow;
use App\Model\WalletTransferRecords;
use App\Traits\RedisTool;
use App\Traits\Tools;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserWalletController extends Controller
{
    use RedisTool,Tools;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        $data = [];
        if ($request->username) {
            $data = [
                ['user_name', 'like', '%' . $request->username . '%']
            ];
        }

        $users = $user->with(['userIdentify'])->where($data)->latest()->paginate();

        return view('admin.wallet.index', compact('users', 'data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $userwallet)
    {
        return view('admin.wallet.show', compact('userwallet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(WalletDetail $userwallet,adminUser $adminUser)
    {
        $adminPhone = $adminUser->where('username','admin')->first()->phone;
        $adminPhone = substr($adminPhone,0,3) . '****' . substr($adminPhone,7,4);
        return view('admin.wallet.edit', compact('userwallet','adminPhone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WalletDetail $userwallet,adminUser $adminUser)
    {
        $this->validate($request, [
            'code' => 'required|min:1'
        ]);
        $aUser = $adminUser->where('username','admin')->first();

        if (! $result = $this->checkCode('HT'.$aUser->phone,$request->code)){
            return back()->with('danger', '请重新发送验证码');//请重新发送验证码
        }else if ($result != 1){
            return back()->with('danger', '验证码错误');//错误
        }

        $data = $request->all();
        $data['user_id'] = $userwallet->user_id;
        $data['admin_user_id'] = auth('web')->user()->id;
        $data['coin_id'] = $userwallet->coin_id;
//dd($userwallet->where('user_id',$userwallet->user_id)->first([\DB::raw('SUM(wallet_usable_balance+wallet_withdraw_balance) as total')])->toArray());
        $coinName = CoinType::find($userwallet->coin_id)->coin_name;//dd($coinName);
        if ($coinName == 'USDT' && ($request->add_usable_balance || $request->add_withdraw_balance)){
            $add = $request->add_usable_balance ? $request->add_usable_balance : $request->add_withdraw_balance;
            //验证usdt总数100万限额
            $b1 = WalletDetail::where('coin_id',$userwallet->coin_id)->first([\DB::raw('SUM(wallet_usable_balance+wallet_withdraw_balance+wallet_freeze_balance) as total')])->total;
            $b2 = CenterWallet::where('coin_id',$userwallet->coin_id)->value('total_interest_money');
            $t = bcadd($b1,$b2,8);
            $max = 2000000;
            if (bcadd($add,$t,8) > $max) return back()->with('message','当前总额为'.$t.',上限'.$max);
        }
//        dd($coinName);
//        dd($data);
        $des = '';
        if ($request->add_usable_balance) {
            $data['amount'] = (int)$data['add_usable_balance'];
            $data['type'] = 1;
            $data['wallet_type'] = 1;
            $userwallet->increment('wallet_usable_balance', $request->add_usable_balance);//dd($data);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',场内+' . $request->add_usable_balance;
            (new WalletFlow())->insertOne($userwallet->user_id,$userwallet->wallet_id,$userwallet->coin_id,$request->add_usable_balance,18,1,'后台增加',1);
        }
        if ($request->reduce_usable_balance) {
            $data['amount'] = $data['reduce_usable_balance'];
            $data['type'] = 2;
            $data['wallet_type'] = 1;
            $userwallet->decrement('wallet_usable_balance', $request->reduce_usable_balance);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',场内-' . $request->reduce_usable_balance;
            (new WalletFlow())->insertOne($userwallet->user_id,$userwallet->wallet_id,$userwallet->coin_id,$request->reduce_usable_balance,18,2,'后台减少',1);
        }
        if ($request->add_withdraw_balance) {
            $data['amount'] = $data['add_withdraw_balance'];
            $data['type'] = 1;
            $data['wallet_type'] = 2;
            $userwallet->increment('wallet_withdraw_balance', $request->add_withdraw_balance);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',可提+' . $request->add_withdraw_balance;
            (new WalletFlow())->insertOne($userwallet->user_id,$userwallet->wallet_id,$userwallet->coin_id,$request->add_withdraw_balance,18,1,'后台增加',2);
        }
        if ($request->reduce_withdraw_balance) {
            $data['amount'] = $data['reduce_withdraw_balance'];
            $data['type'] = 2;
            $data['wallet_type'] = 2;
            $userwallet->decrement('wallet_withdraw_balance', $request->reduce_withdraw_balance);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',可提-' . $request->reduce_withdraw_balance;
            (new WalletFlow())->insertOne($userwallet->user_id,$userwallet->wallet_id,$userwallet->coin_id,$request->reduce_withdraw_balance,18,1,'后台减少',2);
        }
        if ($request->add_ore_pool_balance) {
            $data['amount'] = $data['add_ore_pool_balance'];
            $data['type'] = 1;
            $data['wallet_type'] = 3;
            $userwallet->increment('ore_pool_balance', $request->add_ore_pool_balance);
            (new OrePoolTransferRecord())->insertOne($userwallet->wallet_id,$userwallet->user_id,$userwallet->coin_id,$request->add_ore_pool_balance,9);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',矿池+' . $request->add_ore_pool_balance;

        }
        if ($request->reduce_ore_pool_balance) {
            $data['amount'] = $data['reduce_ore_pool_balance'];
            $data['type'] = 2;
            $data['wallet_type'] = 3;
            $userwallet->decrement('ore_pool_balance', $request->reduce_ore_pool_balance);
            (new OrePoolTransferRecord())->insertOne($userwallet->wallet_id,$userwallet->user_id,$userwallet->coin_id,$request->reduce_ore_pool_balance*-1,10);
            (new AdminWalletFlow())->saveOneFlow($data);
            $des .= ',矿池-' . $request->reduce_ore_pool_balance;
        }

//        if (isset($data['type'])) $adminWalletFlow->saveOneFlow($data);
//        if ($res) {
//            $data = ['content' => $request->user()->username . '在' . $userwallet->updated_at . '将用户名为' . $userwallet->user->user_name . '的' . $userwallet->coin->coin_name . '数量修改成了' . $userwallet->wallet_usable_balance];
//            \DB::table('operation_log')->insert($data);
//        }
        $this->redisDelete('HT'.$aUser->phone);
        event(new AdminUserBehavior(auth('web')->user()->id,"更改会员{$coinName}资产,用户id{$userwallet->user_id},{$des}",'更改会员资产',$userwallet->user_id));
        return redirect()->route('userwallet.show', $userwallet->user_id)->with('success', '操作成功');
    }


    public function blockWalletIndex(Request $request,WalletDetail $walletDetail)
    {
        $centerWallets = CenterWalletDetail::with('coin')->get();

        $data = [];
        if ($request->username) {
            if ($user = User::where(['user_phone'=>$request->username])->first())
            $data[] = ['user_id', $user->user_id];
        }
        if ($request->coin_id) {
            $data[] = ['coin_id',$request->coin_id];
        }
        $wallets = $walletDetail->with(['user.userIdentify','coin'])->where($data)->latest()->paginate();
        $coins = CoinType::all();
        foreach ($wallets as &$wallet){
            if($wallet['parent_id']) $wallet['wallet_address'] = $walletDetail->find($wallet['parent_id'])->wallet_address;
        }

        return view('admin.wallet.block',compact('wallets','coins','centerWallets'));
    }


    //内部用户usdt划转表
    public function getInsideUserUSDTFlow(Request $request,WalletTransferRecords $walletTransferRecords,CoinType $coinType,User $user)
    {
//        if ($vr = $this->verifyField($request->all(),[
//            'user_phone' => 'required|string'
//        ])) return $vr;

        $usdt = $coinType->getRecordByCoinName('USDT');

        if (!$request->user_phone){
            $records = $walletTransferRecords->where(['coin_id'=>$usdt->coin_id])->latest()->paginate();;
        }else{

            $user = $user->getUserByPhone($request->user_phone);
            $records = $walletTransferRecords->where(['coin_id'=>$usdt->coin_id,'user_id'=>$user->user_id])->latest()->paginate();
        }



        return view('admin.wallet.inside_user_usdt_flow',compact('records'));


    }

    //流水
    public function flow(Request $request,WalletFlow $walletFlow)
    {

        $query = WalletFlow::query()->with(['user.userIdentify','coin','s_user.userIdentify']);

        if ($request->username){
            $query->whereHas('user',function ($q)use($request){
                $q->where(['user_phone'=>$request->username]);
            });
        }
        if ($request->coin_id){
            $query->where(['coin_id'=>$request->coin_id]);
        }

        if ($request->flow_type){
            $query->where(['flow_type'=>$request->flow_type]);
        }

        $total = $query->sum('flow_number');
        $tfee =  $query->sum('fee');

        $flows = $query->latest('id')->paginate();

        $coins = (new CoinType())->where(['is_usable'=>1])->get();


        $types = [1=>'提币',2=>'转入',3=>'c2c买',4=>'1c2c卖',5=>'商家买',6=>'商家卖',7=>'场内交易',8=>'理财',9=>'理财提取',10=>'资金划转',11=>'sto买',12=>'sto提取',13=>'下级奖励',14=>'矿池划转',15=>'挖矿(场内买单)',23=>'平台分红(矿池)',16=>'解锁',17=>'场内超时未交易转入矿池',18=>'后台操作',22=>'场内撮单异常返还',20=>'plc转入',21=>'plc转出',24=>'邀请注册奖励',25=>'推荐奖励'];

        return view('admin.wallet.wallet_flow',compact('flows','coins','types','total','tfee'));


    }

    //
    public function ore_flow(Request $request)
    {
        $query = OrePoolTransferRecord::query()->with(['user.userIdentify','coin','s_user.userIdentify']);




        if ($request->username){
            $query->whereHas('user',function ($q)use($request){
                $q->where(['user_phone'=>$request->username]);
            });
        }
        if ($request->flow_type){
            $query->where(['type'=>$request->flow_type]);
        }


        $total = $query->sum('amount');

        $flows = $query->latest('id')->paginate();



        $types = [1=>'互链转入',2=>'场内转入',3=>'可提转入',4=>'场内交易释放',5=>'下级奖励',6=>'每日自动释放',7=>'sto买入',8=>'场内超时自动转入',9=>'后台增加',10=>'后台减少'];

        return view('admin.wallet.ore_flow',compact('flows','types','total'));

    }



}
