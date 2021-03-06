<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::all();
        return response()->json([
            'status_code' => 201,
            'message' => 'Fetched from redis',
            'data' => $blogs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $blog = Blog::create($request->all());

        //Redis::set('blog_' . $blog->id, $blog);

        //Redis::setex('blog_' . $blog->id, 60, $blog);

        return response()->json([
            'status_code' => 201,
            'message' => 'Create blog success',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $blog = Blog::create($request->all());

        //Redis::set('blog_' . $blog->id, $blog);

        ////set key and expire
        Redis::setex('blog_' . $blog->id, 60, $blog);

        return response()->json([
            'status_code' => 201,
            'message' => 'Create blog success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cachedBlog = Redis::get('blog_' . $id);


        if (isset($cachedBlog)) {
            $blog = json_decode($cachedBlog, FALSE);

            return response()->json([
                'status_code' => 200,
                'message' => 'Fetched from redis',
                'data' => $blog,
            ]);
        } else {
            $blog = Blog::find($id);
            Redis::set('blog_' . $id, $blog);

            //set key and expire
            // Redis::setex('blog_' . $id, 60, $blog);

            return response()->json([
                'status_code' => 200,
                'message' => 'Fetched from database',
                'data' => $blog,
            ]);
        }
    }

    public function showExpire($id)
    {
        $expireCachedBlog = Redis::ttl('blog_' . $id);

        return response()->json([
            'status_code' => 200,
            'message' => 'Expire time',
            'data' => $expireCachedBlog,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update = Blog::findOrFail($id)->update($request->all());

        if ($update) {

            // Delete blog_$id from Redis
            Redis::del('blog_' . $id);

            $blog = Blog::find($id);
            // Set a new key with the blog id
            Redis::set('blog_' . $id, $blog);

            //set key and expire
            //Redis::setex('blog_' . $id, 60, $blog);

            return response()->json([
                'status_code' => 201,
                'message' => 'User updated',
                'data' => $blog,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Blog::findOrFail($id)->delete();
        Redis::del('blog_' . $id);

        return response()->json([
            'status_code' => 201,
            'message' => 'Blog deleted'
        ]);
    }

    public function checkRedis($id)
    {
        $checkRedisKey = Redis::exists('blog_' . $id);

        if (boolval($checkRedisKey) !== true) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Blog not exists'
            ]);
        }

        return response()->json([
            'status_code' => 201,
            'message' => 'Blog exists'
        ]);
    }
}
