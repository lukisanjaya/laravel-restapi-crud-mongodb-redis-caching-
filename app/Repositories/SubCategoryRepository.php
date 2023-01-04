<?php

namespace App\Repositories;

use App\Category;
use App\SubCategory;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\SubCategoryRequest;
use App\Interfaces\SubCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use stdClass;

class SubCategoryRepository implements SubCategoryInterface
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
        $keyCache = 'subcategory:collection:' . $limit . ':' . $page;

        if ($subCategory = Redis::get($keyCache)) {
            return $subCategory;
        }

        $subCategory = SubCategory::orderBy('name', 'asc')->paginate($limit);

        /*
        $subCategory = Cache::remember($keyCache, 2 * 60, function () use ($limit) {
            return SubCategory::orderBy('name', 'asc')->paginate($limit);
        });

        if (!$subCategory->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        return ApiResponseHelper::respondSuccessCollection($subCategory);
        */

        if (!$subCategory->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        $response = ApiResponseHelper::respondSuccessCollection($subCategory)->getOriginalContent();

        Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

        return $response;
    }

    /**
     * @param SubCategoryRequest $request
     *
     * @return
     */
    public function store(SubCategoryRequest $request)
    {
        $slug     = Str::slug($request->name);
        $subCategory = SubCategory::where('slug', $slug)->first();

        if ($subCategory) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $slug;

            $subCategory->category = new stdClass();
            if ($categoryId = $request->category_id) {
                $category = Category::select(['id', 'name', 'slug'])->where('_id', $categoryId)->first();

                if ($category->count()) {
                    $subCategory->category = (object) $category->attributesToArray();
                }
            }
            $subCategory->save();

            $keyCache = 'subcategory:item:' . $subCategory->{'_id'};

            $response = ApiResponseHelper::respondCreated($subCategory)->getOriginalContent();

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
        $keyCache = 'subcategory:item:' . $id;
        if ($subCategory = Redis::get($keyCache)) {
            return $subCategory;
        }

        try {
            $subCategory = SubCategory::where('_id', $id)->first();
            if (!$subCategory) {
                throw new \Exception("Not Found");
            }

            $response = ApiResponseHelper::respondSuccess($subCategory);

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
        $subCategory = SubCategory::where('_id', $id)->first();
        if (!$subCategory) {
            return ApiResponseHelper::respondNotFound();
        }

        try {
            $subCategory->delete();

            $keyCache = 'subcategory:item:' . $id;
            Redis::del($keyCache);

            return ApiResponseHelper::respondOk();
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest('Failed');
        }
    }

    /**
     * @param SubCategoryRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(SubCategoryRequest $request, string $id)
    {
        $keyCache = 'subcategory:item:' . $id;
        $slug     = Str::slug($request->name);

        $subCategory = SubCategory::where('_id', $id)->first();
        if (!$subCategory) {
            return ApiResponseHelper::respondNotFound();
        }

        if (SubCategory::where(
            'slug', '=', $slug
        )->where('_id', '!=', $id)->first()) {
            return ApiResponseHelper::respondBadRequest('Duplicate Data');
        }

        try {
            $subCategory->name = $request->name;
            $subCategory->slug = $slug;

            $subCategory->category = new stdClass();
            if ($categoryId = $request->category_id) {
                $category = Category::select(['id', 'name', 'slug'])->where('_id', $categoryId)->first();

                if ($category->count()) {
                    $subCategory->category = (object) $category->attributesToArray();
                }
            }
            $subCategory->save();

            $keyCache = 'subcategory:item:' . $id;

            Redis::del($keyCache);

            $response = ApiResponseHelper::respondCreated($subCategory)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest();
        }
    }
}
