<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostFiles;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Multicaret\Acquaintances\Interaction;


/**
 * @group Posts
 *
 * APIs for Post
 */
class PostController extends Controller
{
    /**
     * List all posts / get single post.
     * This endpoint returns the list of all posts.
     *
     * @authenticated
     *
     * @urlParam id string required The id of the user. Example: 2
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function index(Request $request, string $id = ''): JsonResponse
    {
        try {
            if ((!empty($request->$id)) || !empty($id)) {
                $data = Post::find($id);
                if ($data === null) {
                    return Helper::fail([], trans('api.invalidId'));
                } else {
                    $data = Post::getList($request, $id);
                    return Helper::success($data);
                }
            } else {
                $data = Post::getList($request, '');
                return Helper::success($data);
            }
        } catch (Exception $e) {
            return Helper::fail([], $e->getMessage());
        }
    }

    /**
     * Crate new post.
     * This endpoint allows user to create new posts.
     *
     * @authenticated
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function store(Request $request, $id = null): JsonResponse
    {
        if ($id != null) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:posts,title,' . $id,
                'content' => 'required',
            ]);

        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:posts,title',
                'content' => 'required',
            ]);

        }

        if ($validator->fails()) {
            return Helper::fail([], Helper::error_parse($validator->errors()));
        }

        try {
            DB::beginTransaction();
            if ($id != null) {
                $data = Post::AddEdit($request, $id);
            } else {
                $data = Post::AddEdit($request, '');
            }

            DB::commit();
            if ($id != null) {
                return Helper::success($data, trans('api.editPost'));
            } else {
                return Helper::success($data, trans('api.addPost'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Helper::fail([], $e->getMessage());
        }

    }

    /**
     * Delete My Post.
     * This end point lets a user delete post.
     *
     * @authenticated
     *
     * @urlParam id string required The id of the post. Example: 22
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $isPost = Post::find($id);
            if ($isPost) {
                Post::where('id', $id)->delete();
                DB::commit();
                return Helper::success([], trans('api.deletePost'));
            } else {
                return Helper::fail([], trans('api.postNotExist'));
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Helper::fail([], $e->getMessage());
        }
    }


}
