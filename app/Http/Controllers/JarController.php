<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJarRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateJarRequest;
use App\Models\Account;
use App\Models\Jar;
use App\Models\Payment;
use Carbon\Carbon;

class JarController extends Controller
{
    public function createPayment(Jar $jar, StorePaymentRequest $request): void
    {
        $repeat = $request->input('repeat', 'none');

        if ($repeat === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'date'     => $date->addMonths($i),
                    'jar_id'   => $jar->id,
                    'currency' => $jar->account->currency,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            for ($i = 0; $i < 52; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'date'     => $date->addWeeks($i),
                    'jar_id'   => $jar->id,
                    'currency' => $jar->account->currency,
                ]);
            }
        } else {
            Payment::create([
                ...$request->only([
                    'description',
                    'amount',
                    'date',
                ]),
                'jar_id'   => $jar->id,
                'currency' => $jar->account->currency,
            ]);
        }
    }

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
     * @param \App\Http\Requests\StoreJarRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Jar $jar
     * @return \Illuminate\Http\Response
     */
    public function show(Jar $jar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateJarRequest $request
     * @param \App\Models\Jar $jar
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJarRequest $request, Jar $jar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Jar $jar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jar $jar)
    {
        //
    }
}
