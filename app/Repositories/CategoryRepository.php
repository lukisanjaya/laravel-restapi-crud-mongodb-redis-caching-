<?php

namespace App\Repositories;

use App\Category;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class CategoryRepository implements CategoryInterface
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
        $keyCache = 'category:collection:' . $limit . ':' . $page;

        if ($category = Redis::get($keyCache)) {
            return $category;
        }

        $category = Category::orderBy('name', 'asc')->paginate($limit);

        /*
        $category = Cache::remember($keyCache, 2 * 60, function () use ($limit) {
            return Category::orderBy('name', 'asc')->paginate($limit);
        });

        if (!$category->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        return ApiResponseHelper::respondSuccessCollection($category);
        */

        if (!$category->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        $response = ApiResponseHelper::respondSuccessCollection($category)->getOriginalContent();

        Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

        return $response;
    }

    /**
     * @param CategoryRequest $request
     *
     * @return
     */
    public function store(CategoryRequest $request)
    {
        $slug     = Str::slug($request->name);
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $slug;
            $category->save();

            $keyCache = 'category:item:' . $category->{'_id'};

            $response = ApiResponseHelper::respondCreated($category)->getOriginalContent();

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
        $keyCache = 'category:item:' . $id;
        if ($category = Redis::get($keyCache)) {
            return $category;
        }

        try {
            $category = Category::where('_id', $id)->first();
            if (!$category) {
                throw new \Exception("Not Found");
            }

            $response = ApiResponseHelper::respondSuccess($category);

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
        $category = Category::where('_id', $id)->first();
        if (!$category) {
            return ApiResponseHelper::respondNotFound();
        }

        try {
            $category->delete();

            $keyCache = 'category:item:' . $id;
            Redis::del($keyCache);

            return ApiResponseHelper::respondOk();
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest('Failed');
        }
    }

    /**
     * @param CategoryRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(CategoryRequest $request, string $id)
    {
        $keyCache = 'category:item:' . $id;
        $name     = $request->name;
        $slug     = Str::slug($name);

        $category = Category::where('_id', $id)->first();
        if (!$category) {
            return ApiResponseHelper::respondNotFound();
        }

        if (Category::where('slug', '=', $slug)->where('_id', '!=', $id)->first()) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $category->name = $name;
            $category->slug = $slug;
            $category->save();

            Redis::del($keyCache);

            $response = ApiResponseHelper::respondCreated($category)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);
        } catch (\Throwable $th) {
            $response = ApiResponseHelper::respondBadRequest();
        }

        return $response;
    }
}
