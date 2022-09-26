<?php

namespace App\AccountModule\Controllers;

use App\AccountModule\DTO\CreateJarData;
use App\AccountModule\DTO\UpdateJarData;
use App\AccountModule\Models\Jar;
use App\AccountModule\Requests\StoreJarRequest;
use App\AccountModule\Requests\UpdateJarRequest;
use App\AccountModule\Services\JarService;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class JarController extends Controller
{
    /**
     * JarController constructor.
     *
     * @param  JarService  $jarService
     */
    public function __construct(protected JarService $jarService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->jarService->getAllJars();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreJarRequest  $request
     * @return Jar
     *
     * @throws UnknownProperties
     */
    public function store(StoreJarRequest $request): Jar
    {
        $data = new CreateJarData($request->all());

        return $this->jarService->createJar($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  Jar  $jar
     * @return Jar
     */
    public function show(Jar $jar): Jar
    {
        return $this->jarService->getJar($jar->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateJarRequest  $request
     * @param  Jar  $jar
     * @return Jar
     *
     * @throws UnknownProperties
     */
    public function update(UpdateJarRequest $request, Jar $jar): Jar
    {
        $data = new UpdateJarData($request->all());

        $this->jarService->updateJar($jar->id, $data);

        return $this->jarService->getJar($jar->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Jar  $jar
     * @return bool
     */
    public function destroy(Jar $jar): bool
    {
        return $this->jarService->deleteJar($jar->id);
    }
}
