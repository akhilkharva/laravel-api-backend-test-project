<?php

namespace App\Helpers;

use App;
use App\Enums\Status;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use JsonException;
use URL;
class Helper
{

    /**
     * @return JsonResponse
     */
    public static function res($data, $message, $code, $extras = [])
    {
        $response = [
            'status' => ($code >= 200 && $code <= 299) ? true : false,
            'code' => $code,
            'msg' => $message,
            'version' => '1.0.0',
            'data' => $data
        ];
        return response()->json(array_merge($response, $extras), $code);
    }

    /**
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function success($data = [], string $message = 'Success', int $code = 200): JsonResponse
    {
        return self::res($data, $message, $code);
    }

    /**
     * @param array $data
     * @param string $msg
     * @param int $code
     * @param array $extras
     * @return JsonResponse
     */
    public static function fail($data = [], $msg = "An unknown error has occurred. Please try again.", $code = 400, $extras = []): JsonResponse
    {
        return self::res($data, $msg, $code, $extras);
    }

    /**
     * @param $msg
     * @return mixed|void
     */
    public static function error_parse($msg)
    {
        foreach ($msg->toArray() as $key => $value) {
            foreach ($value as $ekey => $evalue) {
                return $evalue;
            }
        }
    }

    /**
     * @return string
     */
    public static function getTimezone(): string
    {
        if (Session::get('customTimeZone') && Session::get('customTimeZone') != '') {
            return Session::get('customTimeZone');
        } else {
            return "Europe/Berlin";
        }
    }

    /**
     * @param $status
     * @return string
     */
    public static function Status($status): string
    {
        if ($status === (Status::ACTIVE_INT)) {
            return '<span class="badge badge-success">Active</span>';
        }
        if ($status === (Status::INACTIVE_INT)) {
            return '<span class="badge badge-warning">InActive</span>';
        }
        if ($status === (Status::SUSPEND_INT)) {
            return '<span class="badge badge-danger">Suspended</span>';
        }
        return '<button type="button" class="btn red btn-xs pointerhide cursornone">---</button>';
    }

    /**
     * @param string $editLink
     * @param string $deleteID
     * @param string $viewLink
     * @param string $recoveryLink
     * @param string $emailLink
     * @param string $blockLink
     * @return string
     */
    public static function Action($editLink = '', $deleteID = '', $viewLink = '')
    {
        $h = 14;
        $w = 14;
        if ($editLink)
            $edit = '<a href="' . $editLink . '"  data-toggle="tooltip" title="Edit" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="View"><svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'"  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>';
        else
            $edit = '';

        if ($deleteID)
            $delete = '<a onclick="deleteValueSet(' . $deleteID . ')"  class=""  title="Delete" data-toggle="modal" data-target="#delete-modal" > <svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
        else
            $delete = '';

        if ($viewLink)
            $view = '<a  href="' . $viewLink . '" class="text-info btn btn-sm btn-clean btn-icon btn-icon-md"><svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>';
        else
            $view = '';

        return $view . '' . $edit . '' . $delete;
    }
    /**
     * @param $date
     * @param string $format
     * @return string
     * @throws Exception
     */
    public static function displayDateTimeConvertedWithFormat($date, string $format = ''): string
    {
        if (!$format) {
            $format = config('const.displayDateTimeFormatForAll');
        }

        $dt = new DateTime($date);
        $tz = new DateTimeZone(Helper::getTimezone()); // or whatever zone you're after

        $dt->setTimezone($tz);
        return $dt->format($format);
    }

    /**
     * @return string[]
     */
    public static function getSearchStatusArray(): array
    {
        return array(
            '1' => "Active",
            '0' => "InActive",
        );
    }

    /**
     * @return string
     */
    public static function defaultDisplayProfilePath(): string
    {
        return URL::to('/') . '/inspinia/img/user/default.jpg';
    }

    /**
     * @return string
     */
    public static function displayNoImagePath(): string
    {
        return URL::to('/') . '/inspinia/img/user/default-no-image.jpg';
    }

    /**
     * Store Path
     * @return string
     */
    public static function userProfileImageUploadPath(): string
    {
        return 'public/users';
    }

    public static function userProfileDisplayPath(): string
    {
        return URL::to('/') . '/storage/users/';
    }

    //

    public static function postUploadDirPath(): string
    {
        return 'public/post-files';
    }

    public static function displayPostDirPath(): string
    {
        return URL::to('/') . '/storage/post-files/';
    }
    public static function commonStatusArray(){
        return array(
            '1'=>"Active",
            '0'=>"InActive",
        );
    }
    /**
     * Upload File to S3 Bucket
     * This will also make a new directory if it doesn't exist and delete the old file as well if exists.
     *
     * @param $type
     * @param $directoryPath
     * @param $newFile
     * @param $oldFile
     * @param $height
     * @param $width
     * @return string
     */
    public static function uploadPostFilesToS3Bucket($type, $directoryPath, $newFile, $oldFile, $height, $width): string
    {

        if (((env('APP_ENV'))) == "local") {
            $s3 = Storage::disk('local');
        } else {
            $s3 = Storage::disk(env('FILE_STORAGE'));
        }

        /* create dir if not exist */
        if (!$s3->exists($directoryPath)) {
            $s3->makeDirectory($directoryPath);
        }

        //delete old file
        if (isset($oldFile) && $oldFile != NULL) {
            $oldFileName = pathinfo($oldFile);
            $s3->delete($directoryPath . '/' . $oldFileName['basename']);
        }

        /* get file name with an extension */
        $newName = (Str::random(10)) . time() . '.' . $newFile->getClientOriginalExtension();
        if ($type === 'image') {
            /* resize image with provided height width */
            $imgFile = Image::make($newFile->getRealPath());
            $imgFile = $imgFile->resize($height, $width, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            /* Store new size to s3 bucket */
            $resource = $imgFile->stream()->detach();
            $filePath = $directoryPath . '/' . $newName;
            $s3->put($filePath, $resource, 'public');
        }

        if ($type === 'video') {
            $resource = $newFile->getRealPath();
            $filePath = $directoryPath . '/' . $newName;
            $s3->put($filePath, fopen($resource, 'r+'));

        }
        if ($type === 'document') {
            $docFile = $newFile;
            /* Store new size to s3 bucket */
            $resource = $docFile->stream()->detach();
            $filePath = $directoryPath . '/' . $newName;
        }


        return $newName;
    }

    public static function uploadFileToS3Bucket($type, $directoryPath, $newFile, $oldFile, $height, $width): string
    {

        if (((env('APP_ENV'))) == "local") {
            $s3 = Storage::disk('local');
        } else {
            $s3 = Storage::disk(env('FILE_STORAGE'));
        }


        /* create dir if not exist */
        if (!$s3->exists($directoryPath)) {
            $s3->makeDirectory($directoryPath);
        }

        //delete old file
        if (isset($oldFile) && $oldFile != NULL) {
            $oldFileName = pathinfo($oldFile);
            $s3->delete($directoryPath . '/' . $oldFileName['basename']);
        }

        /* get file name with an extension */
        $newName = (Str::random(10)) . time() . '.' . $newFile->getClientOriginalExtension();
        if ($type === 'image') {
            /* resize image with provided height width */
            $imgFile = Image::make($newFile->getRealPath());
            $imgFile = $imgFile->resize($height, $width, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            /* Store new size to s3 bucket */
            $resource = $imgFile->stream()->detach();
            $filePath = $directoryPath . '/' . $newName;
        }

        if ($type === 'document') {
            $docFile = $newFile;
            /* Store new size to s3 bucket */
            $resource = $docFile->stream()->detach();
            $filePath = $directoryPath . '/' . $newName;
        }

        $s3->put($filePath, $resource, 'public');
        return $newName;
    }

    /**
     * Method sendNotification
     *
     * @param String $title [explicit description]
     * @param string|null $message [explicit description]
     * @param string $firebaseToken
     * @param null $imageUrl
     * @param string $pushType
     * @param string $userId
     * @param string $postId
     * @param string $commentId
     * @param $replyCommentId
     * @return RedirectResponse
     * @throws JsonException
     */
    public function sendNotification(string $title, string $message = null, string $firebaseToken, $imageUrl = null, string $pushType, string $userId, string $postId, string $commentId, $replyCommentId)
    {
        $SERVER_API_KEY = env('FCM_SERVER_KEY');
        $dataMessage = json_encode([
            "push_type" => $pushType,
            "image" => $imageUrl,
            "user_id" => $userId,
            "post_id" => $postId,
            "comment_id" => $commentId,
            "reply_comment_id" => $replyCommentId,
            "redirect_to" => ""
        ], JSON_THROW_ON_ERROR);

        $data = [
            "to" => $firebaseToken,
            "priority" => "high",
            "mutable_content" => true,
            "content_available" => true,
            "notification" => [
                "title" => $title,
                "body" => isset($message) ?? null,
            ],
            "android" => [
                "notification" => ["image" => "https://foo.bar/pizza-monster.png"],
            ],
            "data" => [
                "push_type" => $pushType,
                "image" => $imageUrl,
                "user_id" => $userId,
                "post_id" => $postId,
                "comment_id" => $commentId,
                "reply_comment_id" => $replyCommentId,
                "redirect_to" => "",
                "message" => $dataMessage,
            ],
        ];

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
//        echo $response;

        return back()->with('success', 'Notification send successfully.');
    }
    /*function sendNotification_na(string $title, string $message, array $fcmTokens)
    {

        try {
            $server_key = env("FCM_SERVER_KEY");
            $URL = 'https://fcm.googleapis.com/fcm/send';

            $notification = [
                'title' => $title,
                'body' => $message,
                'icon' => 'myIcon',
                'sound' => 'mySound',
                // 'notification_type'  => $type,
                'image' => ''
            ];

            $extraNotificationData = ["data" => $notification];
            $post_data = [
                // 'registration_ids'    => $tokens, //multple token array
                // 'to'                  => $tokens, //single token
                'to' => json_encode($fcmTokens), //multple token array
                'notification' => $notification,
                'data' => $extraNotificationData
            ];

            $crl = curl_init();
            $headers = array(
                'Authorization: key=' . $server_key,
                'Content-Type: application/json'
            );
//            $headr = array();
//            $headr[] = 'Content-type: application/json';
//            $headr[] = 'Authorization: Bearer ' . $server_key;

            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($crl, CURLOPT_URL, $URL);
            curl_setopt($crl, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

            $rest = curl_exec($crl);
             dd($rest);
            return true;
        } catch (Exception $e) {
            return self::fail([], $e->getMessage());
            //throw new GeneralException($e->getMessage());
        }
    }*/

    // function makes curl request to firebase servers
    /*private function sendPushNotification($fields, $server_key = '')
    {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = env("FCM_TOKEN");
        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));


        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }*/
}
