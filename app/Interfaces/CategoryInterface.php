<?php
namespace App\Interfaces;

use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

interface CategoryInterface
{
    /**
     * @param CategoryRequest $request
     *
     * @return [type]
     */
    public function store(CategoryRequest $request);

    /**
     * @param CategoryRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(CategoryRequest $request, string $id);

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
