<?php

namespace Ylateef\LaPromo\Test;

use Promocodes;
use Ylateef\LaPromo\Models\Promocode;
use Ylateef\LaPromo\Test\Models\User;
use Ylateef\LaPromo\Exceptions\InvalidPromocodeException;

class CheckPromocodeValidationTest extends TestCase
{
    /** @test */
    public function it_throws_exception_if_there_is_not_such_promocode()
    {
        $this->expectException(InvalidPromocodeException::class);

        Promocodes::check('INVALID-CODE');
    }

    /** @test */
    public function it_returns_false_if_promocode_is_expired()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        Promocode::byCode($promocode['code'])->update([
            'expires_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $this->assertCount(1, $promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertFalse($checkPromocode);
    }

    /** @test */
    public function it_returns_false_if_promocode_is_disposable_and_used()
    {
        $promocodes = Promocodes::createDisposable();
        $promocode = $promocodes->first();

        $promocode = Promocode::byCode($promocode['code'])->first();
        $user = User::find(1);

        $promocode->users()->attach($user->id, ['used_at' => date('Y-m-d H:i:s')]);

        $this->assertCount(1, $promocodes);
        $this->assertCount(1, $user->promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertFalse($checkPromocode);
    }

    /** @test */
    public function it_returns_promocode_model_if_validation_passes()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertTrue($checkPromocode instanceof Promocode);
        $this->assertEquals($promocode['code'], $checkPromocode->code);
    }
}
