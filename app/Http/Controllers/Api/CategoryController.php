<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryInterface;

class CategoryController extends Controller
{
    protected $categoryInterface;

    public function __construct(CategoryInterface $categoryInterface)
    {
        $this->categoryInterface = $categoryInterface;
        // $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        return $this->categoryInterface->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return
     */
    public function store(CategoryRequest $request)
    {
        return $this->categoryInterface->store($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return
     */
    public function update(CategoryRequest $request, $id)
    {
        return $this->categoryInterface->update($request, $id);
    }

    /**
     * @param string $id
     *
     * @return
     */
    public function getById(string $id)
    {
        return $this->categoryInterface->getById($id);
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
        return $this->categoryInterface->destroy($id);
    }
}
