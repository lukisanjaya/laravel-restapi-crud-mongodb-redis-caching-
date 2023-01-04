<?php
namespace App\Interfaces;

use App\Http\Requests\SubCategoryRequest;
use Illuminate\Http\Request;

interface SubCategoryInterface
{
    /**
     * @param SubCategoryRequest $request
     *
     * @return [type]
     */
    public function store(SubCategoryRequest $request);

    /**
     * @param SubCategoryRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(SubCategoryRequest $request, string $id);

    /**
     * @param string $id
     *
     * @return [type]
     */
    public function getById(string $id);

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function getAll(Request $request);

    /**
     * @param string $id
     *
     * @return [type]
     */
    public function destroy(string $id);
}
