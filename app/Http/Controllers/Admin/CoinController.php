<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminUserBehavior;
use App\Handlers\ImageUpload;
use App\Model\CenterStoWallet;
use App\Model\coinType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\RedisTool;
use Illuminate\Support\Facades\DB;

class CoinController extends Controller
{
    use RedisTool;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = coinType::query()->where('is_usable', 1)->latest()->paginate();

        return view('admin.coin.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coinType $coinType)
    {
        return view('admin.coin.create_and_edit', compact('coinType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ImageUpload $imageUpload)
    {
        if (strtoupper($request->coin_name) != 'USDT') {
            $this->validate($request, [
                'coin_name' => 'required',
                'current_price' => 'required|numeric',
                'coin_icon' => 'required',
                'is_sto' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'coin_name' => 'required',
                'coin_icon' => 'required'
            ]);
        }
        $data = $request->all();
        unset($data['current_price']);
        if ($data['is_sto']) $data['is_see'] = 0;
//        unset($data['is_sto']);

        if ($request->coin_icon) {
            $res = $imageUpload->save($request->coin_icon, 'coin', 'coin');
            $data['coin_icon'] = $res['path'];
        }

        $res = coinType::create($data);

        if (!$res) return back()->with('danger','error');


        DB::transaction(function () use ($request, $res,$data) {
            DB::table('real_coin_center_wallet')->insert(['coin_id' => $res->coin_id]);
            DB::table('coin_exchange_rate')->insert(['virtual_coin_id' => $res->coin_id,  'real_coin_id' => 174, 'rate' => $request->current_price]);
            DB::table('coin_des')->insert(['coin_symbol'=>$res->coin_name,'coin_name'=>$res->coin_name,'coin_zh_name'=>$res->coin_name,'coin_icon'=>$data['coin_icon']]);
            DB::table('coin_fees')->insert(['coin_id'=>$res->coin_id]);
        });
        event(new AdminUserBehavior(auth('web')->user()->id,"创建币种信息",'币种信息'));

        return redirect()->route('coinType.index')->with('success', '创建成功');
    }

    public function show(coinType $coinType)
    {
        //获取除了当前点击货币得其他货币
        $others = coinType::query()->where('coin_id', '!=', $coinType->coin_id)->get();

        return view('admin.coin.show', compact('coinType', 'others'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(coinType $coinType)
    {
        $rate = DB::table('coin_exchange_rate')->where('virtual_coin_id', $coinType->coin_id)->pluck('rate');

        return view('admin.coin.create_and_edit', compact('coinType', 'rate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, coinType $coinType, ImageUpload $imageUpload)
    {
        $data = $request->all();//dd($data);
//        $data['is_see'] = $data['is_usable'];unset($data['is_usable']);

        DB::table('coin_des')->where(['coin_name'=>$coinType->coin_name])->update(['coin_des'=>$data['coin_content']]);
        if ($request->coin_icon) {
            $res = $imageUpload->save($request->coin_icon, 'coin', 'coin');
            $data['coin_icon'] = $res['path'];
            DB::table('coin_des')->where(['coin_name'=>$coinType->coin_name])->update(['coin_icon'=>$res['path']]);
        }
        $coinType->update($data);
        event(new AdminUserBehavior(auth('web')->user()->id,"修改币种信息",'币种信息'));

        return redirect()->route('coinType.index')->with('success', '操作成功');
    }

    public function open(coinType $coinType)
    {
        if ($coinType->is_outside) {
            $coinType->is_outside = 0;
        } else {
            $coinType->is_outside = 1;
        }

        $coinType->save();
        return back()->with('success', '操作成功');
    }

    //富文本编辑器上传图片
    public function uploadImage(Request $request, ImageUpload $uploader)
    {
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'coin', 'coin');
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }

}
