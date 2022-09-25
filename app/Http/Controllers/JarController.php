<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\UpdateJarRequest;
use App\Models\Jar;

class JarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreJarRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Jar  $jar
     * @return \Illuminate\Http\Response
     */
    public function show(Jar $jar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJarRequest  $request
     * @param  \App\Models\Jar  $jar
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJarRequest $request, Jar $jar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jar  $jar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jar $jar)
    {
        //
    }
}
