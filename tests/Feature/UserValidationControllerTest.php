<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserValidationControllerTest extends TestCase
{
    /**
     * @test
     */
    public function email_validation_returns_false_if_email_exists()
    {
        $user = User::factory()->create();

        $this->get(route('user.validate.email', ['email' => $user->email]))->assertJson(['valid' => false]);
    }

    /**
     * @test
     */
    public function email_validation_returns_true_if_email_doesnt_exists()
    {
        $user = User::factory()->create();

        $this->get(route('user.validate.email', ['email' => 'someother@email.com']))->assertJson(['valid' => true]);
    }
}
