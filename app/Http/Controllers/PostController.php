<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Response;
class PostController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getAllPosts(Request $request)
    {
        try {
            return Post::getAllPosts($request);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('posts.index');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = array(
                'title' => 'required|max:75|unique:posts',
                'content' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->errors());
            }

            $return_response = Post::AddEdit($request);
            if ($return_response) {
                session()->flash('success', trans('admin.postCreateSuccess'));
                return redirect()->route('posts.index');
            } else {
                session()->flash('error', trans('admin.oopsError'));
                return redirect()->route('posts.create');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Post::find($id);
        return view('admin.product.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Post::where('id', $id)->first();
        return view('admin.product.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $rules = array(
                'title' => 'required|unique:posts,title,' . $id,
                'content' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator->errors());
            }

            $data = Post::AddEdit($request, $id);
            if ($data) {
                session()->flash('success', trans('admin.postUpdateSuccess'));
                return redirect()->route('posts.index');
            } else {
                session()->flash('error', trans('admin.oopsError'));
                return redirect()->route('posts.create');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data_del = Post::where('id', $id)->delete();
            return Response::json($data_del);
        } catch (\Exception $e) {
            return Response::json($e);
        }
    }
}
