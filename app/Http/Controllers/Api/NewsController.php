<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Interfaces\NewsInterface;

class NewsController extends Controller
{
    protected $newsInterface;

    public function __construct(NewsInterface $newsInterface)
    {
        $this->newsInterface = $newsInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        return $this->newsInterface->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsRequest $request
     * @return
     */
    public function store(NewsRequest $request)
    {
        return $this->newsInterface->store($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  NewsRequest $request
     * @param string $id
     * @return
     */
    public function update(NewsRequest $request, $id)
    {
        return $this->newsInterface->update($request, $id);
    }

    /**
     * @param NewsRequest $request
     * @param mixed $slug
     *
     * @return
     */
    public function updateBySlug(NewsRequest $request, $slug)
    {
        return $this->newsInterface->updateBySlug($request, $slug);
    }

    /**
     * @param string $id
     *
     * @return
     */
    public function getById(string $id)
    {
        return $this->newsInterface->getById($id);
    }

    public function getBySlug(string $slug)
    {
        return $this->newsInterface->getBySlug($slug);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     *
     * @return
     */
    public function destroy($id)
    {
        return $this->newsInterface->destroy($id);
    }

    /**
     * @param mixed $slug
     *
     * @return
     */
    public function destroyBySlug($slug)
    {
        return $this->newsInterface->destroyBySlug($slug);
    }
}
