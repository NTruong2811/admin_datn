<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\PartnerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $name = $request->name ? $request->name : null;
            $phone = $request->phone ? $request->phone : null;
            $from = $request->from ? $request->from : null;
            $to = $request->to ? $request->to : null;
            $is_running = $request->is_running;
            $is_delete = $request->is_delete;

            $partner = DB::table('partners');

            if ($name) {
                $partner->where('partners.name', 'like', "%{$name}%");
            }
            if ($phone) {
                $partner->where('partners.phone', 'like', "%{$phone}%");
            }
            if ($from) {
                $partner->whereDate('partners.create_at', '>=', $from);
            }
            if ($to) {
                $partner->whereDate('partners.create_at', '<=', $to);
            }
            if ($is_running != null) {
                $partner->where('partners.is_running', $is_running);
            }
            if ($is_delete != null) {
                $partner->where('partners.is_delete', $is_delete);
            }

            $data = $partner->paginate(config('const.pagination.per_page'));
            return  PartnerResource::collection($data);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $name = $request->name;
            $phone = $request->phone;

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:100',
                    'phone' => 'required|regex:/^(0)[0-9]{9}$/|unique:partners,phone',
                ],
                [
                    'name.required' => 'Vui l??ng nh???p h??? v?? t??n ?????i t??c!',
                    'name.max:100' => 'T??n kh??ng qu?? 100 k?? t???!',
                    'phone.required' => 'Vui l??ng nh???p s??? ??i???n tho???i!',
                    'phone.regex' => 'S??? ??i???n tho???i kh??ng ????ng ?????nh d???ng',
                    'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i trong h??? th???ng'
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()
                ]);
            }

            $newPartner = PartnerModel::create([
                'name' => $name,
                'phone' => $phone,
                'is_running' => 1,
            ]);
            return response()->json([
                'data' => $newPartner
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $partner = PartnerModel::find($id);
            if (!$partner) {
                return response()->json([
                    'error' => true,
                    "message" => 'id kh??ng t???n t???i'
                ]);
            }

            $request->validate(
                [
                    'name' => 'required|max:100',
                    'phone' => 'required|regex:/^(0)[0-9]{9}$/|unique:partners,phone,' . $id,
                    'point' => 'numeric|min:0'
                ],
                [
                    'name.required' => 'Vui l??ng nh???p h??? v?? t??n ?????i t??c!',
                    'name.max:100' => 'T??n kh??ng qu?? 100 k?? t???!',
                    'phone.required' => 'Vui l??ng nh???p s??? ??i???n tho???i!',
                    'phone.regex' => 'S??? ??i???n tho???i kh??ng ????ng ?????nh d???ng',
                    'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i trong h??? th???ng',
                    'point.numeric' => 'S??? ti???n ph???i l?? s???!',
                    'point.min' => 'S??? ti???n ph???i l?? s??? d????ng',
                ]
            );
            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'is_running' => $request->is_running ? 1 : 0,
                'is_delete' => $request->is_delete ? 1 : 0,
                'point' => $request->point ? $request->point : 0
            ];

            $partner->fill($data);
            $partner->save();
            return response()->json([
                'data' => $partner
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $partner = PartnerModel::findOrFail($id);
            return response()->json([
                'data' => $partner
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }
}
