<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCategoryRequest;
use App\Interfaces\SubCategoryInterface;

class SubCategoryController extends Controller
{
    protected $subcategoryInterface;

    public function __construct(SubCategoryInterface $subcategoryInterface)
    {
        $this->subcategoryInterface = $subcategoryInterface;
        // $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        return $this->subcategoryInterface->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SubCategoryRequest $request
     * @return
     */
    public function store(SubCategoryRequest $request)
    {
        return $this->subcategoryInterface->store($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return
     */
    public function update(SubCategoryRequest $request, $id)
    {
        return $this->subcategoryInterface->update($request, $id);
    }

    /**
     * @param string $id
     *
     * @return
     */
    public function getById(string $id)
    {
        return $this->subcategoryInterface->getById($id);
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
        return $this->subcategoryInterface->destroy($id);
    }
}
