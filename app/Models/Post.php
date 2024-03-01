<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public static function getAllPosts($request)
    {
        $query = Post::where('title', '!=', null);

        if ($request->order === null) {
            $query->orderBy('id', 'desc');
        }
        return Datatables::of($query)
            ->addColumn('action', function ($data) {
                $editLink = URL::to('/') . '/admin/posts/' . $data->id . '/edit';
                $viewLink = URL::to('/') . '/admin/posts/' . $data->id;
                $deleteLink = $data->id;
                return Helper::Action($editLink, $deleteLink, $viewLink);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function AddEdit($request, $id = '')
    {
        if (isset($id) && $id != '') {
            $data = Post::find($id);
        } else {
            $data = new Post();
        }
        $data->user_id = Auth::user()->id;
        $data->title = isset($request->title) ? $request->title : null;
        $data->content = isset($request->content) ? $request->content : null;
        $data->save();
        return $data;
    }

    public static function getList($request, $id=null)
    {
        $data = self::where('title', '!=', null);
        if($id != null){
            $data->where('id', $id);
        }
        $posts = $data->orderBy('id', 'DESC')->get();
        return $posts;
    }
}
