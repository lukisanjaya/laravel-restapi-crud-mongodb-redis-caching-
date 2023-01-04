<?php

namespace App\Repositories;

use App\Tag;
use App\News;
use stdClass;
use App\Category;
use App\SubCategory;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use App\Interfaces\NewsInterface;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\NewsRequest;
use Illuminate\Support\Facades\Redis;

class NewsRepository implements NewsInterface
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
        $keyCache = 'news:collection:' . $limit . ':' . $page;

        if ($news = Redis::get($keyCache)) {
            return $news;
        }

        $news = News::orderBy('name', 'asc')->paginate($limit);

        /*
        $news = Cache::remember($keyCache, 2 * 60, function () use ($limit) {
            return News::orderBy('name', 'asc')->paginate($limit);
        });

        if (!$news->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        return ApiResponseHelper::respondSuccessCollection($news);
        */

        if (!$news->count()) {
            return ApiResponseHelper::respondNotFound();
        }

        $response = ApiResponseHelper::respondSuccessCollection($news)->getOriginalContent();

        Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

        return $response;
    }

    /**
     * @param NewsRequest $request
     *
     * @return
     */
    public function store(NewsRequest $request)
    {
        $slug = Str::slug($request->title);
        $news = News::where('title', $request->title)->count();

        if ($news) {
            $slug .= '-' . str::lower(Str::random(4));
        }

        try {
            $news                = new News();
            $news->title         = $request->title;
            $news->slug          = $slug;
            $news->teaser        = $request->teaser;
            $news->content       = $request->content;

            if ($request->file('image')) {
                $news->image         = ImageHelper::uploadFile($request, $news->title, 'news');
                $news->image_caption = $request->image_caption;
            }

            $news->tag = array();

            if ($tags = $request->tag) {
                $news->tag = Tag::select(['_id', 'name', 'slug'])->whereIn('_id', $tags)->get()->toArray();
            }

            $news->category      = new stdClass();
            $news->subcategory   = new stdClass();
            if ($categoryId = $request->category_id) {
                $category = Category::select(['_id', 'name', 'slug'])->where('_id', $categoryId)->first();
                if ($category->count()) {
                    $news->category = (object) $category->attributesToArray();
                }
            }

            if ($subcategoryId = $request->subcategory_id) {
                $subcategory = SubCategory::select(['_id', 'name', 'slug'])->where('_id', $subcategoryId)->first();
                if ($subcategory->count()) {
                    $news->subcategory = (object) $subcategory->attributesToArray();
                }
            }

            $news->save();

            $keyCache     = 'news:item:' . $news->{'_id'};
            $keyCacheSlug = 'news:item:' . $news->{'slug'};

            $response = ApiResponseHelper::respondCreated($news)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);
            Redis::set($keyCacheSlug, json_encode($response), 'EX', 60 * 2);

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
        $keyCache = 'news:item:' . $id;
        if ($news = Redis::get($keyCache)) {
            return $news;
        }

        try {
            $news = News::where('_id', $id)->first();
            if (!$news) {
                throw new \Exception("Not Found");
            }

            $response = ApiResponseHelper::respondSuccess($news);

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondNotFound();
        }
    }

    /**
     * @param Request $request
     * @param string $id
     *
     * @return
     */
    public function update(Request $request, string $id)
    {
        $title = $request->title;
        $slug  = Str::slug($title);

        $news  = News::where('_id', $id)->first();

        if (!$news) {
            return ApiResponseHelper::respondNotFound();
        }

        if ($news->title != $title) {
            $slug .= '-' . str::lower(Str::random(4));
            $news->slug    = $slug;
        }

        try {
            $news->title   = $title;
            $news->teaser  = $request->teaser;
            $news->content = $request->content;

            if ($request->file('image')) {
                $news->image         = ImageHelper::uploadFile($request, $news->title, 'news');
                $news->image_caption = $request->image_caption;
            }

            $news->tag = array();

            if ($tags = $request->tag) {
                $news->tag = Tag::select(['_id', 'name', 'slug'])->whereIn('_id', $tags)->get()->toArray();
            }

            $news->category    = new stdClass();
            $news->subcategory = new stdClass();
            if ($categoryId = $request->category_id) {
                $category = Category::select(['_id', 'name', 'slug'])->where('_id', $categoryId)->first();
                if ($category->count()) {
                    $news->category = (object) $category->attributesToArray();
                }
            }

            if ($subcategoryId = $request->subcategory_id) {
                $subcategory = SubCategory::select(['_id', 'name', 'slug'])->where('_id', $subcategoryId)->first();
                if ($subcategory->count()) {
                    $news->subcategory = (object) $subcategory->attributesToArray();
                }
            }

            $news->save();

            $keyCache     = 'news:item:' . $news->{'_id'};
            $keyCacheSlug = 'news:item:' . $news->{'slug'};

            $response = ApiResponseHelper::respondCreated($news)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);
            Redis::set($keyCacheSlug, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest();
        }
    }

    /**
     * @param NewsRequest $request
     * @param string $slug
     * @return mixed
     */
    public function updateBySlug(NewsRequest $request, string $slug)
    {
        $title = $request->title;
        $slug  = Str::slug($title);

        $news  = News::where('slug', $slug)->first();

        if (!$news) {
            return ApiResponseHelper::respondNotFound();
        }

        if ($news->title != $title) {
            $slug .= '-' . str::lower(Str::random(4));
            $news->slug    = $slug;
        }

        try {
            $news->title   = $title;
            $news->teaser  = $request->teaser;
            $news->content = $request->content;

            if ($request->file('image')) {
                $news->image         = ImageHelper::uploadFile($request, $news->title, 'news');
                $news->image_caption = $request->image_caption;
            }

            $news->tag = array();

            if ($tags = $request->tag) {
                $news->tag = Tag::select(['_id', 'name', 'slug'])->whereIn('_id', $tags)->get()->toArray();
            }

            $news->category    = new stdClass();
            $news->subcategory = new stdClass();
            if ($categoryId = $request->category_id) {
                $category = Category::select(['_id', 'name', 'slug'])->where('_id', $categoryId)->first();
                if ($category->count()) {
                    $news->category = (object) $category->attributesToArray();
                }
            }

            if ($subcategoryId = $request->subcategory_id) {
                $subcategory = SubCategory::select(['_id', 'name', 'slug'])->where('_id', $subcategoryId)->first();
                if ($subcategory->count()) {
                    $news->subcategory = (object) $subcategory->attributesToArray();
                }
            }

            $news->save();

            $keyCache     = 'news:item:' . $news->{'_id'};
            $keyCacheSlug = 'news:item:' . $news->{'slug'};

            $response = ApiResponseHelper::respondCreated($news)->getOriginalContent();

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);
            Redis::set($keyCacheSlug, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest();
        }
    }

    /**
     *
     * @param string $slug
     * @return
     */
    public function getBySlug(string $slug)
    {
        $keyCache = 'news:item:' . $slug;
        if ($news = Redis::get($keyCache)) {
            return $news;
        }

        try {
            $news = News::where('slug', $slug)->first();
            if (!$news) {
                throw new \Exception("Not Found");
            }

            $response = ApiResponseHelper::respondSuccess($news);

            Redis::set($keyCache, json_encode($response), 'EX', 60 * 2);

            return $response;
        } catch (\Exception $th) {
            return ApiResponseHelper::respondNotFound();
        }
    }

    /**
     *
     * @param string $slug
     * @return
     */
    public function destroyBySlug(string $slug)
    {
        $news = News::where('slug', $slug)->first();
        if (!$news) {
            return ApiResponseHelper::respondNotFound();
        }

        try {
            $news->delete();

            $keyCache = 'news:item:' . $slug;
            Redis::del($keyCache);

            return ApiResponseHelper::respondOk();
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest('Failed');
        }
    }

    /**
     *
     * @param string $id
     * @return
     */
    public function destroy(string $id)
    {
        $news = News::where('_id', $id)->first();
        if (!$news) {
            return ApiResponseHelper::respondNotFound();
        }

        try {
            $news->delete();

            $keyCache = 'news:item:' . $id;
            Redis::del($keyCache);

            return ApiResponseHelper::respondOk();
        } catch (\Exception $th) {
            return ApiResponseHelper::respondBadRequest('Failed');
        }
    }
}
