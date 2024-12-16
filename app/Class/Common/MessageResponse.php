<?php
namespace App\Class\Common;

class MessageResponse
{
    const CODE_GET_ALL_SUCCESS = 1000;
    const CODE_GET_ALL_FAIL = 1001;
    const CODE_GET_SUCCESS = 1002;
    const CODE_GET_FAIL = 1003;
    const CODE_SAVE_SUCCESS = 1004;
    const CODE_SAVE_FAIL = 1005;
    const CODE_SAVE_INVALID_JSON = 1006;

    const MESSAGE = [
        "1000" => "All data retrieve successful",
        "1001" => "All data retrieve fail",
        "1002" => "Data get successful",
        "1003" => "Data get fail",
        "1004" => "Data save successful",
        "1005" => "Data save fail",
        "1006" => "Invalid json."
    ];

    public static function Response($message_code, $result = null, $status_code = 200)
    {
        $mresult['code'] = $message_code;
        $mresult['message'] = self::MESSAGE[$message_code];
        if($result) {
            $mresult["result"] = $result;
        }
        return response()->json($mresult)->setStatusCode($status_code);;
    }
}