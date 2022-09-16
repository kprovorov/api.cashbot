<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Group;
use App\Models\Jar;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentController extends Controller
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
     * @param \App\Http\Requests\StorePaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        $jar = Jar::with(['account'])->findOrFail($request->input('jar_id'));
        $repeat = $request->input('repeat', 'none');

        if ($repeat === 'quarterly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 4; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'group_id' => $group->id,
                    'date'     => $date->addMonths($i * 3),
                    'jar_id'   => $jar->id,
                    'currency' => $jar->account->currency,
                ]);
            }
        } elseif ($repeat === 'monthly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'group_id' => $group->id,
                    'date'     => $date->addMonths($i),
                    'jar_id'   => $jar->id,
                    'currency' => $jar->account->currency,
                ]);
            }
        } elseif ($repeat === 'weekly') {
            $group = Group::create([
                'name' => $request->input('description'),
            ]);

            for ($i = 0; $i < 52; $i++) {
                $date = Carbon::parse($request->input('date'));

                Payment::create([
                    ...$request->only([
                        'description',
                        'amount',
                    ]),
                    'group_id' => $group->id,
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
     * Display the specified resource.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePaymentRequest $request
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update(
            $request->only([
                'description',
                'jar_id',
                'amount',
                'date',
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        $transfer = $payment->from_transfer ?? $payment->to_transfer;
        if ($transfer) {
            $transfer->payment_from->delete();
            $transfer->payment_to->delete();
            $transfer->delete();
        } else {
            $payment->delete();
        }
    }
}
