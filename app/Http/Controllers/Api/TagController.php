<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Interfaces\TagInterface;

class TagController extends Controller
{
    protected $tagInterface;

    public function __construct(TagInterface $tagInterface)
    {
        $this->tagInterface = $tagInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        return $this->tagInterface->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TagRequest $request
     * @return
     */
    public function store(TagRequest $request)
    {
        return $this->tagInterface->store($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return
     */
    public function update(TagRequest $request, $id)
    {
        return $this->tagInterface->update($request, $id);
    }

    /**
     * @param string $id
     *
     * @return
     */
    public function getById(string $id)
    {
        return $this->tagInterface->getById($id);
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
        return $this->tagInterface->destroy($id);
    }
}
