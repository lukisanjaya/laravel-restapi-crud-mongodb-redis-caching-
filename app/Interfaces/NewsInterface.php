<?php
namespace App\Interfaces;

use App\Http\Requests\NewsRequest;
use Illuminate\Http\Request;

interface NewsInterface
{
    /**
     * @param NewsRequest $request
     *
     * @return [type]
     */
    public function store(NewsRequest $request);

    /**
     * @param NewsRequest $request
     * @param string $id
     *
     * @return
     */
    public function update(NewsRequest $request, string $id);

    /**
     * @param NewsRequest $request
     * @param string $slug
     *
     * @return
     */
    public function updateBySlug(NewsRequest $request, string $slug);

    /**
     * @param string $id
     *
     * @return [type]
     */
    public function getById(string $id);

    /**
     * @param string $slug
     *
     * @return [type]
     */
    public function getBySlug(string $slug);

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

    /**
     * @param string $slug
     *
     * @return [type]
     */
    public function destroyBySlug(string $slug);
}
