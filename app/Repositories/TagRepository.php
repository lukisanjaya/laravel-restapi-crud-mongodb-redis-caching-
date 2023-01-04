<?php

namespace App\Repositories;

use App\Tag;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\TagRequest;
use App\Interfaces\TagInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TagRepository implements TagInterface
{
    /**
     *
     * @param Request $request
     * @return
     */
    public function getAll(Request $request)
    {
        $page     = (int) $request->get('page') ?? 1;
        $limit    = (int) $request->get('limit') ?? 10;
        $keyCache = 'tag:collection:' . $limit . ':' . $page;

        if ($tag = Redis::get($keyCache)) {
            return $tag;
        }

        $tag = Tag::orderBy('name', 'asc')->paginate($limit);

        /*
        $tag = Cache::remember($keyCache, 2 * 60, function () use ($limit) {
            return Tag::orderBy('name', 'asc')->paginate($limit);
        });

        if (!$tag->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        return ApiResponseHelper::respondSuccessCollection($tag);
        */

        if (!$tag->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        $response = ApiResponseHelper::respondSuccessCollection($tag)->getOriginalContent();

        Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

        return $response;
    }

    /**
     * @param TagRequest $request
     *
     * @return
     */
    public function store(TagRequest $request)
    {
        $slug     = Str::slug($request->name);
        $tag = Tag::where('slug', $slug)->first();

        if ($tag) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->slug = $slug;
            $tag->save();

            $keyCache = 'tag:item:' . $tag->{'_id'};

            $response = ApiResponseHelper::respondCreated($tag)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest();
        }
    }

    /**
     * @param string $id
     * @return
     */
    public function getById(string $id)
    {
        $keyCache = 'tag:item:' . $id;
        if ($tag = Redis::get($keyCache)) {
            return $tag;
        }

        try {
            $tag = Tag::where('_id', $id)->first();
            if (!$tag) {
                throw new \Exception("Not Found");
            }

            $response = ApiResponseHelper::respondSuccess($tag);

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondNotFound();
        }
    }

    /**
     *
     * @param string $id
     * @return
     */
    public function destroy(string $id)
    {
        $tag = Tag::where('_id', $id)->first();
        if (!$tag) {
            return ApiResponseHelper::respondNotFound();
        }

        try {
            $tag->delete();

            $keyCache = 'tag:item:' . $id;
            Redis::del($keyCache);

            return ApiResponseHelper::respondOk();
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest('Failed');
        }
    }

    /**
     * @param TagRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(TagRequest $request, string $id)
    {
        $keyCache = 'tag:item:' . $id;
        $name     = $request->name;
        $slug     = Str::slug($name);

        $tag = Tag::where('_id', $id)->first();
        if (!$tag) {
            return ApiResponseHelper::respondNotFound();
        }

        if (Tag::where('slug', '=', $slug)->where('_id', '!=', $id)->first()) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $tag->name = $name;
            $tag->slug = $slug;
            $tag->save();

            Redis::del($keyCache);

            $response = ApiResponseHelper::respondCreated($tag)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);
        } catch (\Throwable $th) {
            $response = ApiResponseHelper::respondBadRequest();
        }

        return $response;
    }
}
