<?php

namespace App\Http\Controllers;

use App\Class\Common\MessageResponse;
use App\Models\Objects;
use DateTime;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ObjectController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->post("JSON");
        $time = $request->post("time");
        if(empty($data)){
            return MessageResponse::Response(MessageResponse::CODE_SAVE_SUCCESS, null, 400);
        }
        if(is_string($data)){
            $data = json_decode($data);
        }
        if($time) {
            $time = DateTime::createFromFormat("Y-m-d H:i:s", $time)->getTimestamp();
        }
        $jsonData = NULL;
        foreach ($data as $key => $value){
            $jsonData = new Objects();
            $jsonData->key = $key;
            $jsonData->value =  is_string($value) ? $value : json_encode($value);
            $jsonData->value = urlencode($jsonData->value);
            $jsonData->timestamp = $time;
            $jsonData->save();
        }
        if($jsonData == NULL){
            return MessageResponse::Response(MessageResponse::CODE_SAVE_SUCCESS, null, 400);
        }
        $jsonData = $jsonData->getSelectedFields(["id"]);
        return MessageResponse::Response(MessageResponse::CODE_SAVE_SUCCESS, $jsonData);
    }

    public function getByKey(Request $request, string $key)
    {
        $timestamp = $request->get("timestamp");
        if($timestamp) {
            $timestamp = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp)->getTimestamp();
        }
        $query = DB::table("objects");
        $query->where("key", $key);
        $timestamp = intval($timestamp);
        if(is_int($timestamp)){

            $query->whereRaw('IFNULL(timestamp,0) <= '. $timestamp);
        }

        $query->orderByRaw("IFNULL(`timestamp`,0) desc");
        $query->limit(1);
        $object = $query->first();
        if($object == NULL){
            return MessageResponse::Response(MessageResponse::CODE_GET_FAIL, null, 400);
        }
        $value = urldecode($object->value);
        if(is_string($value)){
            $value = json_decode($value) ? json_decode($value) : $value;
        }
        return MessageResponse::Response(MessageResponse::CODE_GET_SUCCESS, $value);
    }

    public function getAllRecords(Request $request)
    {
        $jsonData = Objects::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'key' => $item->key,
                'value' => $item->value
            ];
        });;
        return MessageResponse::Response(MessageResponse::CODE_GET_SUCCESS, $jsonData);
    }
}
