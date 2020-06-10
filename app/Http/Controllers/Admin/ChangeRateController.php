<?php

namespace App\Http\Controllers\Admin;

use App\Traits\RedisTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChangeRateController extends Controller
{
    use RedisTool;

    //场外手续费
    public function outsideindex()
    {
        $key = strtoupper('outside-rate');
        $outsiderate = $this->redisHgetAll($key);

        $key = strtoupper('inside_rate');
        $insiderate = $this->redisHgetAll($key);//dd($insiderate['rate']);
        !empty($insiderate['rate']) ? :$insiderate['rate'] = 0.002;

        return view('admin.change_rate.index', compact('outsiderate', 'insiderate'));
    }

    public function outsideRate(Request $request)
    {
        $key = strtoupper('outside-rate');
        $outsiderate = $this->redisHgetAll($key);

        if ($request->isMethod('GET')) {
            return view('admin.change_rate.create_or_edit', compact('outsiderate'));
        } elseif($request->isMethod('POST')) {
            $this->validate($request, [
                'rate' => 'required'
        ]);
        $this->redisHmset($key, ['rate' => $request->rate]);

        return redirect()->route('outside-rate.index')->with('success', '创建成功');
        }
    }



    //场内手续费
    public function insideRate(Request $request)
    {
        $key = strtoupper('inside_rate');
        $insiderate = $this->redisHgetAll($key);
        !empty($insiderate['rate']) ? :$insiderate['rate'] = 0.002;

        if ($request->isMethod('GET')) {
            return view('admin.change_rate.create_or_edit2', compact('insiderate'));
        } elseif($request->isMethod('POST')) {
            $this->validate($request, [
                'rate' => 'required'
            ]);
            $this->redisHmset($key, ['rate' => $request->rate / 100]);

            return redirect()->route('outside-rate.index')->with('success', '创建成功');
        }
    }

}
