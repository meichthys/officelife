<?php

namespace Tests\Unit\Services\Company\Employee;

use Tests\TestCase;
use App\Models\Company\Employee;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\Company\Employee\UpdateHiringInformation;

class UpdateHiringInformationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_the_hiring_information()
    {
        $employee = factory(Employee::class)->create([]);

        $request = [
            'company_id' => $employee->company_id,
            'author_id' => $employee->user->id,
            'employee_id' => $employee->id,
            'hired_at' => '2010-02-20',
        ];

        $updatedEmployee = (new UpdateHiringInformation)->execute($request);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'company_id' => $employee->company_id,
            'hired_at' => '2010-02-20',
        ]);

        $this->assertInstanceOf(
            Employee::class,
            $updatedEmployee
        );
    }

    /** @test */
    public function it_logs_an_action()
    {
        $employee = factory(Employee::class)->create([]);

        $request = [
            'company_id' => $employee->company_id,
            'author_id' => $employee->user->id,
            'employee_id' => $employee->id,
            'hired_at' => '2010-02-20',
        ];

        $updatedEmployee = (new UpdateHiringInformation)->execute($request);

        $this->assertdatabasehas('audit_logs', [
            'company_id' => $employee->company_id,
            'action' => 'employee_updated_hiring_information',
        ]);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given()
    {
        $request = [
            'hired_at' => '2010-02-20',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateHiringInformation)->execute($request);
    }
}