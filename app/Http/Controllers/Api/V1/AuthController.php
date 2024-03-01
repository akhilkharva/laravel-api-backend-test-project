<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\LoginType;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\AccountVerified;
use App\Mail\PasswordChanged;
use App\Mail\PasswordUpdated;
use App\Mail\ReactivateAccount;
use App\Mail\ResetPassword;
use App\Mail\VerifyYourAccountUsingOtp;
use App\Models\LoginDevice;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * @group Auth
 *
 * APIs for authenticating user
 */
class AuthController extends Controller
{
    /**
     * Login.
     * This endpoint lets user to log in.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user. Example: hello@testapp.org
     * @bodyParam password string required The password of the user. Example: XXXXXX
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Helper::fail([], trans('api.invalidParameters'), 400, ['errors' => $validator->errors()->toArray()]);
        }

        try {
            $userMail = User::checkEmailExist($request->email);
            if ($userMail !== null) {
                if (Auth::attempt(['email' => $request->email, 'password' => request('password')])) {
                    $user = Auth::user();
                    $user->access_token = $user->createToken('API Token')->accessToken;
                    return Helper::success([$user], trans('api.loginSuccess'));
                }
                return Helper::fail([], trans('api.invalidCredentials'));
            }
            return Helper::fail([], trans('api.emailNotFound'));
        } catch (Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    /**
     * User Device Management.
     * This endpoint lets user add device information.
     *
     * @authenticated
     *
     * @bodyParam fcm_token string required The Fcm Token. Example: xxxxx
     * @bodyParam device_name string required The device name of the device. Example: iPhone 12
     * @bodyParam device_id string required The device id of the device. Example: skdlfsk-sfs-dsfsdf-sdfs
     * @bodyParam app_version string required The app version of the device. Example: v1
     * @bodyParam os_version string required The os version of the device. Example: iOS 14.1
     * @bodyParam time_zone string required The time zone of the user. Example: NZ
     * @bodyParam platform string required The platform of the device. Example: Apple
     *
     * @param Request $request
     * @return JsonResponse
     **/
    public function storeDeviceInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
            'device_name' => 'required',
            'device_id' => 'required',
            'app_version' => 'required',
            'os_version' => 'required',
            'time_zone' => 'required',
            'platform' => 'required'
        ]);

        if ($validator->fails()) {
            return Helper::fail([], trans('api.invalidParameters'), 400, ['errors' => $validator->errors()->toArray()]);
        }

        try {
            $data = LoginDevice::deviceTokenManagement($request);
            return Helper::success($data);
        } catch (\Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    /**
     * User Device Detail.
     * This endpoint lets user add device information.
     *
     * @authenticated
     *
     * @urlParam id string The id of the login device. Example: 12
     *
     * @param string $id
     * @return JsonResponse
     */
    public function getUserDeviceDetail(string $id = ''): JsonResponse
    {
        try {
            $data = LoginDevice::getUserDeviceDetail($id);
            return Helper::success($data);
        } catch (\Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    /**
     * Logout.
     * This endpoint lets user to logout.
     *
     * @authenticated
     *
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            LoginDevice::where('user_id', Auth::user()->id)->where('fcm_token', $request->fcm_token)->delete();
            Auth::user()->token()->revoke();
            return Helper::success([], trans('api.logout'));
        } catch (\Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    /**
     * USER: Reactivate an old deleted account.
     * This endpoint allows a user to reactivate its account.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user. Example: hello@testapp.org
     * @param Request $request
     * @return JsonResponse
     */
    public function reActivateAccount(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->withTrashed()->get()->first();
        if ($user) {
            try {
                DB::beginTransaction();
                $user->token = sprintf("%06d", mt_rand(1, 999999));
                $user->email_verified_at = null;
                $user->save();
                DB::commit();
                Mail::to($user)->send(new ReactivateAccount($user));
                return Helper::success($user, trans('api.reactivationVerify'), 201);
            } catch (Exception $e) {
                DB::rollBack();
                return Helper::fail([], $e->getMessage());
            }
        } else {
            return Helper::fail([], trans('auth.userAccountNotFound'), 422);
        }
    }

    /**
     * USER: verify re-activation : Manually Verify Re-Activation-Account.
     * This endpoint lets a user verify its account using token and password.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The email of the user. Example: hello@testapp.org
     * @bodyParam password string required The password of the user. Example: XXXXXX
     * @bodyParam confirm_password string required To be re-enter above password. Example: XXXXXX
     * @bodyParam token string required To restrict unauthorized registration. Example: 987654
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyReActivateAccount(Request $request): JsonResponse
    {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Helper::fail([], trans('auth.verifyAccountFailed'), 400, ['errors' => $validator->errors()->toArray()]);
        }
        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->withTrashed()->get()->first();
            if ($user == null) {
                return Helper::fail([], trans('api.emailNotFound'));
            }

            if ($user->token == null && $user->email_verified_at != null) {
                return Helper::fail([], trans('api.alreadyVerified'));
            }

            if ($user->token != $request->token && $user->email_verified_at == null) {
                return Helper::fail([], trans('api.tokenMismatch'));
            }

            if ($user->token == $request->token && $user->email_verified_at == null) {
                $updated_at = new DateTime($user->updated_at);
                $now = new DateTime(date('Y-m-d H:i:s'));
                if ($updated_at->diff($now)->days > 7) {
                    return Helper::fail([], trans('api.tokenExpired'));
                }
                $user->email_verified_at = now();
                $user->deleted_at = null;
                $user->token = null;
                $user->password = bcrypt($request->password);
                $user->save();
                DB::commit();
                Mail::to($user)->send(new AccountVerified($user));
                return Helper::success([$user], trans('api.reActivationVerifySuccess'));
            }
            return Helper::fail([], trans('api.somethingWentWrongTryAgain'));
        } catch (Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    public function changePassword(Request $request): JsonResponse|RedirectResponse
    {
        $rules = array(
            'old_password' => 'required',
            'new_password' => ['required', 'different:old_password', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,16})/'],
        );
        $messages = array(
            'old_password.required' => 'The old password is required.',
            'new_password.required' => 'The new password is required.',
            'new_password.regex' => 'The new password must include at least one lowercase letter, one uppercase letter, one digit, and one special character (!@#\$%\^&\*), and it must be between 8 and 16 characters in length.',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Helper::fail([], trans('api.invalidCredentials'), 400, ['errors' => $validator->errors()->toArray()]);
        }
        try {
            $checkIsSocial = self::isSocialUser(Auth::user());
            if ($checkIsSocial === true) {
                return Helper::fail([], trans('api.notAllowedUsingSocialLogin'));
            }
            // Check if the old password matches the user's current password
            if (!Hash::check($request->old_password, Auth::user()->password)) {
                return Helper::fail([], trans('api.passwordMismatch'));
            }
            DB::beginTransaction();
            $user = User::find(Auth::user()->id);
            $user->password = bcrypt($request->new_password);
            $user->save();
            DB::commit();
            Mail::to($user)->send(new PasswordChanged($user));
            return Helper::success([], trans('api.passwordChanged'));
        } catch (Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    public static function isSocialUser($user): bool
    {
        if ($user->provider === LoginType::FACEBOOK) {
            return true;
        }
        if ($user->provider === LoginType::GMAIL) {
            return true;
        }
        if ($user->provider === LoginType::APPLE) {
            return true;
        }
        return false;
    }
}
