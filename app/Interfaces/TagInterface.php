<?php
namespace App\Interfaces;

use App\Http\Requests\TagRequest;
use Illuminate\Http\Request;

interface TagInterface
{
    /**
     * @param TagRequest $request
     *
     * @return [type]
     */
    public function store(TagRequest $request);

    /**
     * @param TagRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(TagRequest $request, string $id);

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
