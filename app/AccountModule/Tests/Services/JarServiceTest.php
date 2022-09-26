<?php

namespace App\AccountModule\Tests\Services;

use App\AccountModule\DTO\CreateJarData;
use App\AccountModule\DTO\UpdateJarData;
use App\AccountModule\Models\Account;
use App\AccountModule\Models\Jar;
use App\AccountModule\Services\JarService;
use Arr;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class JarServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_jars(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Collection $jars */
        $jars = Jar::factory()->count(3)->create([
            'account_id' => $account->id,
        ]);

        $service = $this->app->make(JarService::class);
        $res = $service->getAllJars();

        $this->assertCount(3, $res);
        $jars->each(fn (Jar $jar) => $this->assertContains(
            $jar->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_jars_paginated(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Collection $jars */
        $jars = Jar::factory()->count(3)->create([
            'account_id' => $account->id,
        ]);

        $service = $this->app->make(JarService::class);
        $res = $service->getAllJarsPaginated();

        $this->assertCount(3, $res);
        $jars->each(fn (Jar $jar) => $this->assertContains(
            $jar->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_jar(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        $service = $this->app->make(JarService::class);
        $res = $service->getJar($jar->id);

        $this->assertEquals($jar->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_creates_jar(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jarData */
        $jarData = Jar::factory()->make([
            'account_id' => $account->id,
        ]);

        $data = new CreateJarData($jarData->toArray());

        $service = $this->app->make(JarService::class);
        $res = $service->createJar($data);

        $this->assertEquals(
            $data->toArray(),
            Arr::except($res->toArray(), [
                'id',
                'created_at',
                'updated_at',
            ])
        );
        $this->assertDatabaseHas('jars', $data->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_jar(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Jar $jarData */
        $jarData = Jar::factory()->make([
            'account_id' => $account->id,
        ]);

        $data = new UpdateJarData($jarData->toArray());

        $service = $this->app->make(JarService::class);
        $res = $service->updateJar($jar->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('jars', $data->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_jar(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        $service = $this->app->make(JarService::class);
        $res = $service->deleteJar($jar->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('jars', [
            'id' => $jar->id,
        ]);
    }
}
